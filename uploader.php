<?php

$error = "";
$info = (Object)[];



if (isset($_FILES["file"]) && $_FILES["file"]["name"] != "") {


    if ($_FILES["file"]["error"] > 0) {
        $error = $_FILES["file"]["error"];
    } else {

        move_uploaded_file($_FILES["file"]["tmp_name"], "uploads/" . $_FILES["file"]["name"]);

    }

} else {
    $error = "No file uploaded";
}

if ($error != "") {
    $info->data_type = "error";
    $info->message = $error;
} else {
    $info->data_type = "success";
    $info->message = "file uploaded";
}


json_encode($info);


