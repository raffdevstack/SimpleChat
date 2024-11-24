<?php

    global $DATA_OBJ, $DB, $info;

    $arr['userid'] = $DATA_OBJ->find->userid;
    $sql = "SELECT * FROM `users` WHERE `userid` = :userid LIMIT 1 ";
    $result = $DB->read($sql, $arr);

    if (is_array($result)) {
        $user = $result[0];
        $html_markup = "
            <h3>Our chats with: </h3>
            <p>$user->username</p>
        ";
        $info->message = $html_markup;
        $info->data_type = "chats"; // send to responseText
        echo json_encode($info);
    } else {
        $info->message = "No chats found";
        $info->data_type = "error";
        echo json_encode($info);
    }





?>


