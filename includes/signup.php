<?php

$info = (Object)[];

global $DATA_OBJ, $DB, $Error;

date_default_timezone_set('Asia/Manila');
$data = false;
$data['userid'] = $DB->generate_id(20);
$data['date'] = date("Y-m-d H:i:s");

$data['username'] = $DATA_OBJ->username;
if ($DATA_OBJ->username == "") {
    $Error .= "Username is required";
}
$data['password'] = $DATA_OBJ->password;
if ($DATA_OBJ->password == "") {
    $Error .= "Password is required";
}

if ($Error == "") {
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
    $info->message = $Error;
    $info->data_type = "error";
}
echo json_encode($info);
