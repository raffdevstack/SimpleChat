<?php

$info = (Object)[]; // this info object will be the base of the respose to ajax

global $DATA_OBJ, $DB, $Error;

$data = false;
$data['userid'] = $_SESSION['userid'];

if ($Error == "") {

    $query = "select * from users where userid = :userid limit 1"; // to get the logged in user first
    $result = $DB->read($query, $data);

    if (is_array($result)) {
        $result = $result[0]; // get the first result (array)

    } else {
        $info->message = "Wrong username";
        $info->data_type = "error";
    }
} else {
    $info->message = $Error;
    $info->data_type = "error";
}
echo json_encode($info); // this is required to have a response to ajax
