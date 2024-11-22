<?php

$info = (Object)[]; // this info object will be the base of the respose to ajax

global $DATA_OBJ, $DB, $Error;

$data = false; // local var

// validation
if ($DATA_OBJ->username == "") {
    $Error .= "Username is required";
}
if ($DATA_OBJ->password == "") {
    $Error .= "Password is required";
}

$data['username'] = $DATA_OBJ->username;
$data['password'] = $DATA_OBJ->password;
$data['userid'] = $_SESSION['userid'];

if ($Error == "") {
    $query = "UPDATE users SET username = :username, password = :password WHERE userid = :userid";
    $result = $DB->write($query, $data);
    if ($result) {
        $info->message = "Your account has been updated successfully.";
        $info->data_type = "save_settings";
    } else {
        $info->message = "Your account has not been updated due to some error.";
        $info->data_type = "error";
    }
} else {
    $info->message = $Error;
    $info->data_type = "error";
}
echo json_encode($info);
