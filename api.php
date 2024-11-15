<?php
require_once("classes/autoload.php");

$DB = new Database();

$DATA_RAW_STRING = file_get_contents('php://input');
$DATA_OBJ = json_decode($DATA_RAW_STRING); // this is like JSON.parse() in js

$Error = "";

// process the data
if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "signup") {
    include("includes/signup.php");
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "user_info") {
    echo "info is okay";
}

