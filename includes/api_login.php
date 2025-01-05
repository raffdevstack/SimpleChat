<?php

$info = (Object)[]; // this info object will be the base of the response to ajax

global $DATA_OBJ, $DB;

$errors = [];

$data = false;
$data['email'] = $DATA_OBJ->email;

if ($DATA_OBJ->email == "") {
    $errors['email'] = "Email is required";
}
if ($DATA_OBJ->password == "") {
    $errors['password'] = "Password is required";
}

if ($errors == []) {

    $query = "SELECT * FROM `users` WHERE `email` = :email LIMIT 1"; // to get the user first
    $result = $DB->read($query, $data);

    if (is_array($result)) {
        $result = $result[0]; // get the first result (object)

        // Check if the user is verified
        if (!$result->IsVerified) {
            $info->message = "Please verify your email before logging in.";
            $info->data_type = "not_verified"; // Changed to "not_verified" to indicate unverified status
        } else {
            // Proceed with password verification
            if ($result->password == $DATA_OBJ->password) {  // Old method (plain text or sha256)
                
                // Rehash the password using bcrypt and update the database
                $hashed_password = password_hash($DATA_OBJ->password, PASSWORD_DEFAULT);  // Bcrypt hashing
                $update_query = "UPDATE `users` SET `password` = :password WHERE `email` = :email";
                $update_data = [
                    'password' => $hashed_password,
                    'email' => $data['email']
                ];
                $DB->write($update_query, $update_data);  // Update the password in the database

                $_SESSION['userid'] = $result->userid; // store user id to session
                $info->message = "Successfully logged in, and your password has been updated."; 
                $info->data_type = "info";

            } elseif (password_verify($DATA_OBJ->password, $result->password)) {  // New method (bcrypt)
                
                // User authenticated with bcrypt
                $_SESSION['userid'] = $result->userid; // store user id to session
                $info->message = "Successfully logged in"; 
                $info->data_type = "info";

            } else {
                $info->message = "Wrong credentials";
                $info->data_type = "cred_error";
            }
        }
    } else {
        $info->message = "Wrong credentials";
        $info->data_type = "cred_error";
    }
} else {
    $info->message = $errors;
    $info->data_type = "error";
}

echo json_encode($info); // this is required to have a response to ajax
