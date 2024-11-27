<?php

    global $DATA_OBJ, $DB, $info;

    $arr['receiver_userid'] = "";

    if (isset($DATA_OBJ->receiver_userid)) {

//        checking if chat already exist
        $result_chat = $DB->chatFinder($DATA_OBJ->receiver_userid, $_SESSION['userid']);

//      send the message to db

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
        $arr2['message'] = $DATA_OBJ->text;
        $arr2['date'] = date('Y-m-d H:i:s');
        $sql_send = "INSERT INTO `messages` (chat_id, sender, receiver, txt_message, date) VALUES 
                        (:chat_id, :sender_userid, :receiver_userid, :message, :date)";
        $result = $DB->write($sql_send, $arr2);

        if ($result) {
//            $info->chat_contact = $html_markup;
            $info->message = "Message successfully sent";
            $info->data_type = "send_message"; // send to responseText
        } else {
            $info->chat_contact = "Message not sent due to error";
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







