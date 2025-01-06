<?php

global $aes, $iv, $email_iv;
$info = (Object)[]; // this info object will be the base of the respose to ajax

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

//    function verifyEmail($storedEncryptedEmail, $loginEmailInput, $aes) {

    $matched_email = "";

    // get all email here
    $query = "SELECT `email` FROM `users`"; // to get the user first
    $all_encrypted_email = $DB->read($query);

    foreach ($all_encrypted_email as $email) {
        if (verifyEmail($email->email, $data['email'], $email_iv)) {
            $matched_email = $email->email;
        }
    }

    $result = "";
    if ($matched_email != "") {
        $data['email'] = $matched_email;
        $query = "SELECT userid,password FROM `users` WHERE `email` = :email";
        $result = $DB->read($query,$data);
    }

    if (is_array($result)) {

        $result = $result[0]; // get the first result (array)

        // Verify the password
        if (password_verify($DATA_OBJ->password, $result->password)) {
            $_SESSION['userid'] = $result->userid; // store user id to session
            $info->message = "Successfully logged in"; // this is required to have a response to ajax
            $info->data_type = "info"; // this is required to have a response to ajax
        } else {
            $info->message = "Wrong credentials";
            $info->data_type = "cred_error";
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
