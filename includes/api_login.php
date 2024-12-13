<?php

$info = (Object)[]; // this info object will be the base of the respose to ajax

global $DATA_OBJ, $DB;

$errors = "";

$data = false;
$data['email'] = $DATA_OBJ->email;

if ($DATA_OBJ->email == "") {
    $errors .= "Email is required";
}
if ($DATA_OBJ->password == "") {
    $errors .= "Password is required";
}

if ($errors == "") {

    $query = "SELECT * FROM `users` WHERE `email` = :email LIMIT 1"; // to get the user first
    $result = $DB->read($query, $data);

    if (is_array($result)) {

        $result = $result[0]; // get the first result (array)

        if ($result->password == $DATA_OBJ->password) {

            $_SESSION['userid'] = $result->userid; // store user id to session
            $info->message = "Successfully logged in"; // this is required to have a response to ajax
            $info->data_type = "info"; // this is required to have a response to ajax

        } else {

            $info->message = "Wrong credentials";
            $info->data_type = "error";

        }

    } else {

        $info->message = "Wrong credentials";
        $info->data_type = "error";

    }

} else {
    $info->message = $errors;
    $info->data_type = "error";
}
echo json_encode($info); // this is required to have a response to ajax
