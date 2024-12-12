<?php

global $DATA_OBJ, $DB, $info;

$data = false;
$data['userid'] = $DB->generate_id(20);
$data['date'] = date("Y-m-d H:i:s");

// error validation
$required_fields = [
    'first_name' => 'First name is required.',
    'last_name' => 'Last name is required.',
    'email' => 'Email is required.',
    'password' => 'Password is required.',
    'password_confirm' => 'Password confirmation is required.'
];

$errors = [];
$data = [];

// Loop through required fields to validate input
foreach ($required_fields as $field => $error_message) {
    $data[$field] = isset($DATA_OBJ->$field) ? trim($DATA_OBJ->$field) : '';
    if (empty($data[$field])) {
        $errors[$field] = $error_message;
    }
}

// Additional validations

if (!isset($errors['first_name']) && !preg_match('/^[a-zA-Z\s]+$/', $data['first_name'])) {
    $errors['first_name'] = 'First name can only contain letters and spaces.';
}

if (!isset($errors['last_name']) && !preg_match('/^[a-zA-Z\s]+$/', $data['last_name'])) {
    $errors['last_name'] = 'Last name can only contain letters and spaces.';
}

if (!isset($errors['email'])) {
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    } else {
        // Additional validation:
        if (strlen($data['email']) > 255) {
            $errors['email'] = 'Email address is too long.';
        } elseif (preg_match('/[\x00-\x1F\x7F-\xFF]/', $data['email'])) {
            $errors['email'] = 'Email address contains invalid characters.';
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
}

if ($errors == "") {
    $query = "insert into users(userid,username,password,date) values(:userid,:username,:password,:date)";
    $result = $DB->write($query, $data);
    if ($result) {
        $info->message = "Your account has been created.";
        $info->data_type = "info";
    } else {
        $info->message = "Your has not been created due to some error.";
        $info->data_type = "error";
    }
} else {
    $info->message = $errors;
    $info->data_type = "error";
}
echo json_encode($info);
