<?php

require_once("classes/autoload.php");

$DB = new Database();
$DATA_RAW_STRING = file_get_contents('php://input');
$DATA_OBJ = json_decode($DATA_RAW_STRING); // this is like JSON.parse() in js

session_start();

$info = (object)[]; // this info object will be the base of the response to ajax

// check if logged in
if (!isset($_SESSION['userid'])) { // if no userid in sessions, it's not logged in
    if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type != "login" ) { // if we are not in the login page // --  --
        $info->logged_in  = false;
        echo json_encode($info); // put it in the info object, it is echoed so it is part of the result, the $info is maybe just a dummy object
        die;
    }
} else {
    $info->logged_in  = true;
    echo json_encode($info);
}

$Error = "";

// process the data
if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "signup") {
    include("includes/api_signup.php");
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "login") {
    include("includes/api_login.php");
}  else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "user_info") {
    include("includes/api_user_info.php");
}

