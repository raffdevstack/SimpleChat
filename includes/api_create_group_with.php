<?php

global $info, $DB, $DATA_OBJ;
$logged_user = $_SESSION['userid'];

print_r($DATA_OBJ); die;

$info->message = "";
$info->data_type = "create_group_with"; // send to responseText
echo json_encode($info);