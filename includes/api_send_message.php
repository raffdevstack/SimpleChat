<?php

    global $DATA_OBJ, $DB, $info;

    $arr['receiver_userid'] = "";

    if (isset($DATA_OBJ->receiver_userid)) {

        $arr['receiver_userid'] = $DATA_OBJ->receiver_userid;
        $arr['sender_userid'] = $_SESSION['userid']; // my user id

        $sql_find_chat = "SELECT * FROM `messages` WHERE `receiver` = :receiver_userid AND
                               `sender` = :sender_userid LIMIT 1";
        $result_chat = $DB->read($sql_find_chat, $arr);

        $arr2 = [];
        if (is_array($result_chat)) { // if chat exist
            // send this message to the chat
            $chat = $result_chat[0];
            $arr2['chat_id'] =$chat->chat_id;
        } else {
            $arr2['chat_id'] = generateRandomString(10);
        }

        $arr2['receiver_userid'] = $DATA_OBJ->receiver_userid;
        $arr2['sender_userid'] = $_SESSION['userid']; // my user id
        $arr2['message'] = $DATA_OBJ->message;
        $arr2['date'] = date('Y-m-d H:i:s');
        $sql_send = "INSERT INTO `messages` (chat_id, sender, receiver, message, date) VALUES 
                        (:chat_id, :sender_userid, :receiver_userid, :message, :date)";
        $result = $DB->write($sql_send, $arr2);

die;


        if (is_array($result)) {
            $user = $result[0];
            $html_markup = "
                <h3>You are chatting with: </h3>
                <p>$user->username</p>
            ";
            $html_message = "
                <div id='messages_wrapper'>";
                    $html_message .= getMessageLeft($user);
                    $html_message .= getMessageRight($user);
                    $html_message .= getMessageLeft($user);
                    $html_message .= getMessageRight($user);
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

function generateRandomString($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}







