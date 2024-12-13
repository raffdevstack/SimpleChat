<?php

require_once("classes/autoload.php");
$DB = new Database();

$error = "";
$info = (Object)[];
$data_type = "";
$success_file_move = false;

session_start();
// check if logged in
if (!isset($_SESSION['userid'])) { // if no userid in sessions, it's not logged in
    if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type != "login" && $DATA_OBJ->data_type != "signup" ) { // if we are not in the login page // --  --
        $info->logged_in  = false;
        echo json_encode($info); // put it in the info object, it is echoed so it is part of the result, the $info is maybe just a dummy object
        die;
    }
}

$destination = "";
if (isset($_FILES["file"]) && $_FILES["file"]["name"] != "") {

    if ($_FILES["file"]["error"] > 0) {
        $error = $_FILES["file"]["error"];
    } else {
        $destination = "uploads/" . $_FILES["file"]["name"];
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $destination)) {
            $success_file_move = true;
        }
    }

} else {
    $error = "No file uploaded";
}

if (isset($_POST["data_type"])) {

    $data_type = $_POST["data_type"];

    if ($_POST["data_type"] == "change_profile") {

        if ($destination != "" && $success_file_move) {

            $query = "UPDATE `users` SET `image` = '$destination' WHERE `userid` = $_SESSION[userid]";
            $result = $DB->write($query);

            if ($result) {

                $info->data_type = $data_type;
                $info->message = "successfully uploaded image";

            }
        }
    }
}

if ($error != "") {
    $info->data_type = "error";
    $info->message = $error;
}

echo json_encode($info);


