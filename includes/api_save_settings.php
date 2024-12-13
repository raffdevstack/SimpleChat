<?php

$info = (Object)[]; // this info object will be the base of the respose to ajax

global $DATA_OBJ, $DB;

$errors = [];

$data = [];

// error validation
$required_fields = [
    'email' => 'Email is required.',
    'password' => 'Password is required.',
];

// Loop through required fields to validate input
foreach ($required_fields as $field => $error_message) {
    $data[$field] = isset($DATA_OBJ->$field) ? trim($DATA_OBJ->$field) : '';
    if (empty($data[$field])) {
        $errors[$field] = $error_message;
    }
}

if (!isset($errors['email'])) {

    if ($DB->userFinder($data['email'])) {

        $errors['email'] = 'Same email detected';

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

$data['email'] = $DATA_OBJ->email;
$data['password'] = $DATA_OBJ->password;
$data['userid'] = $_SESSION['userid'];

if ($errors == []) {
    $query = "UPDATE `users` SET `email` = :email, `password` = :password WHERE userid = :userid";
    $result = $DB->write($query, $data);
    if ($result) {
        $info->message = "Your account has been updated successfully.";
        $info->data_type = "save_settings";
    } else {
        $info->message = "Your account has not been updated due to some error.";
        $info->data_type = "error";
    }
} else {
    $info->message = $errors;
    $info->data_type = "error";
}
echo json_encode($info);
