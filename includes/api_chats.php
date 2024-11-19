
<?php

    global $info;

    $html_markup = '
        hello from chats
    ';

    $info->message = $html_markup;
    $info->data_type = "chats"; // send to responseText
    echo json_encode($info);

//    die;
//    $info->message = "No contacts found";
//    $info->data_type = "error";
//    echo json_encode($info);

?>


