<?php

    global $DATA_OBJ, $DB, $info;

    $arr['userid'] = "";

    if (isset($DATA_OBJ->find->userid)) {

        $arr['userid'] = $DATA_OBJ->find->userid;
        $sql = "SELECT * FROM `users` WHERE `userid` = :userid LIMIT 1 ";
        $result = $DB->read($sql, $arr);

        if (is_array($result)) {
            $user = $result[0];
            $html_markup = "
                <h3>You are chatting with: </h3>
                <p>$user->username</p>
            ";
            $html_message = "
                <div id='messages_wrapper'>";
                    $html_message .= getMessageLeft($user->username);
                    $html_message .= getMessageRight($user->username);
                    $html_message .= getMessageLeft($user->username);
                    $html_message .= getMessageRight($user->username);
                $html_message .= "
                </div>
                <div id='messages_inputs'>
                    <input type='text' placeholder='Enter your message here...' name='' id=''>
                    <input type='button' name='' id='send_message' value='SEND'>
                </div>
            "
            ;
            $info->chat_contact = $html_markup;
            $info->messages = $html_message;
            $info->data_type = "chats"; // send to responseText
        } else {
            $info->chat_contact = "No chats found";
            $info->data_type = "error";
        }
    } else {
        $info->message = "Select a contact first";
        $info->data_type = "error";
    }

echo json_encode($info);






