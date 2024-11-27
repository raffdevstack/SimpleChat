<?php

    global $DATA_OBJ, $DB, $info;

    $arr['userid'] = "";

    if (isset($DATA_OBJ->userid)) {

        $arr['userid'] = $DATA_OBJ->userid;
        $sql = "SELECT * FROM `users` WHERE `userid` = :userid LIMIT 1 ";
        $result = $DB->read($sql, $arr);

        if (is_array($result)) {
            $user = $result[0];
            $html_markup = "
                <h3>You are chatting with: </h3>
                <p>$user->username</p>
            ";
            // get our chats

            $html_message = "
                <div id='messages_wrapper'>";
//                    $html_message .= getMessageLeft($user);
//                    $html_message .= getMessageRight($user);
//                    $html_message .= getMessageLeft($user);
//                    $html_message .= getMessageRight($user);
                $html_message .= "
                </div>
                <div id='messages_inputs'>
                    <label for='message_file' id='file_input_label'>File</label>
                    <input type='file' id='message_file' style='display: none' >
                    <input type='text' id='message_text' placeholder='Enter your message here...' >
                    <input type='button' onclick='sendMessage(event)' id='send_message' value='SEND'>    
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






