<?php

session_start();

$info = (object)[]; // this info object will be the base of the respose to ajax

// check if logged in
if (!isset($_SESSION['userid'])) { // if no userid in sessions, it's not logged in
    $info->logged_in  = false;
    echo json_encode($info); // put it in the info object, it is echoed so it is part of the result, the $info is maybe just a dummy object
    die;
}

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
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "login") {
    include("includes/login.php");
}

