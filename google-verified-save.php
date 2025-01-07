<?php

global $info, $DB, $DATA_OBJ;

print_r($DATA_OBJ);

$info->message = "hi";
$info->data_type = "info";


echo json_encode($info);