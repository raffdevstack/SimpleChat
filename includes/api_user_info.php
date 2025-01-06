<?php

global $DATA_OBJ, $DB, $Error, $info, $first_name_iv, $last_name_iv, $email_iv;

$data = false;
$data['userid'] = $_SESSION['userid'];

if ($Error == "") {

    $query = "select * from users where userid = :userid limit 1"; // to get the logged in user first
    $result = $DB->read($query, $data);

    if (is_array($result)) {

        $result = $result[0]; // get the first result (array)

        $result->first_name = decryptAES($result->first_name, $first_name_iv);
        $result->last_name = decryptAES($result->last_name, $last_name_iv);
        $result->email = decryptAES($result->email, $email_iv);

        $result->data_type = "user_info"; // send to responseText

        echo json_encode($result);
    } else {
        $info->message = "Wrong username";
        $info->data_type = "error";
        echo json_encode($info); // this is required to have a response to ajax
    }
} else {
    $info->message = $Error;
    $info->data_type = "error";
    echo json_encode($info); // this is required to have a response to ajax
}

