<?php

require './vendor/autoload.php'; // Load Composer autoloader

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

global $DATA_OBJ, $DB, $info;

$errors = [];
$data = [];

$data['userid'] = $DB->generate_id(20);
$data['date'] = date("Y-m-d H:i:s");

// Error validation
$required_fields = [
    'first_name' => 'First name is required.',
    'last_name' => 'Last name is required.',
    'email' => 'Email is required.',
    'password' => 'Password is required.',
    'password_confirm' => 'Password confirmation is required.'
];

// Loop through required fields to validate input
foreach ($required_fields as $field => $error_message) {
    $data[$field] = isset($DATA_OBJ->$field) ? trim($DATA_OBJ->$field) : '';
    if (empty($data[$field])) {
        $errors[$field] = $error_message;
    }
}

// Additional validations

if (!isset($errors['first_name'])) {
    if (!preg_match('/^[a-zA-Z\s]+$/', $data['first_name'])) {
        $errors['first_name'] = 'First name can only contain letters and spaces.';
    } elseif (strlen($data['first_name']) < 2 || strlen($data['first_name']) > 50) {
        $errors['first_name'] = 'First name must be between 2 and 50 characters.';
    }
}

if (!isset($errors['last_name'])) {
    if (!preg_match('/^[a-zA-Z\s]+$/', $data['last_name'])) {
        $errors['last_name'] = 'Last name can only contain letters and spaces.';
    } elseif (strlen($data['last_name']) < 2 || strlen($data['last_name']) > 50) {
        $errors['last_name'] = 'Last name must be between 2 and 50 characters.';
    }
}

if (!isset($errors['email'])) {
    if ($DB->userFinder($data['email'])) {
        $errors['email'] = 'Email already used by other account.';
    } else {
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format.';
        } else {
            // Additional validation:
            if (strlen($data['email']) > 100) {
                $errors['email'] = 'Email address is too long.';
            } elseif (preg_match('/[\x00-\x1F\x7F-\xFF]/', $data['email'])) {
                $errors['email'] = 'Email address contains invalid characters.';
            }
        }
    }
}

if (!isset($errors['password'])) {
    if (strlen($data['password']) < 8) {
        $errors['password'] = 'Password must be at least 8 characters.';
    } else {
        // Additional validation:
        if (!preg_match('/[A-Z]/', $data['password'])) {
            $errors['password'] = 'Password must contain at least one uppercase letter.';
        } elseif (!preg_match('/[a-z]/', $data['password'])) {
            $errors['password'] = 'Password must contain at least one lowercase letter.';
        } elseif (!preg_match('/[0-9]/', $data['password'])) {
            $errors['password'] = 'Password must contain at least one number.';
        } elseif (!preg_match('/[^A-Za-z0-9]/', $data['password'])) {
            $errors['password'] = 'Password must contain at least one special character.';
        }
    }
}

if (!isset($errors['password']) && !isset($errors['password_confirm']) && $data['password'] !== $data['password_confirm']) {
    $errors['password_confirm'] = 'Passwords do not match.';
} else {
    unset($data["password_confirm"]);
}

// If no errors
if ($errors == []) {
    // Hash the password
    $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
    $data['password'] = $hashed_password;

    // Generate verification token
    $verification_token = bin2hex(random_bytes(32));
    $data['VerificationToken'] = $verification_token;

    // Insert into the database
    $query = "INSERT INTO users(`userid`, `first_name`, `last_name`, `email`, `password`, `date`, `IsVerified`, `VerificationToken`) 
    VALUES(:userid, :first_name, :last_name, :email, :password, :date, FALSE, :VerificationToken)";

    $result = $DB->write($query, $data);

    if ($result) {
        // Send verification email
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'dc.yahzeemailer@gmail.com'; // Your Gmail
            $mail->Password = 'mfvw xlva ihbn lumk';        // Your app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
        
            // Recipients
            $mail->setFrom('dc.yahzeemailer@gmail.com', 'SimpleChat');
            $mail->addAddress($data['email']);
        
            // Email content
            $verification_link = "http://localhost:3000/programs_JYDC_New/SimpleChat/includes/verify.php?token=" . $data['VerificationToken'];
            $mail->isHTML(true);
            $mail->Subject = 'Email Verification';
            $mail->Body = "Click the link to verify your email: <a href='$verification_link'>Verify Now</a>";
        
            $mail->send();
            $info->message = "Your account has been created. Please check your email to verify your account.";
            $info->data_type = "info";
        } catch (Exception $e) {
            $info->message = "Mailer Error: " . $mail->ErrorInfo;
            $info->data_type = "error";
            // Optional: Log the error for further investigation
            file_put_contents('mail_errors.log', $mail->ErrorInfo, FILE_APPEND);
        }
        
    } else {
        $info->message = "Your account has not been created due to some error.";
        $info->data_type = "error";
    }    
} else {
    $info->message = $errors;
    $info->data_type = "error";
}

echo json_encode($info);
?>
