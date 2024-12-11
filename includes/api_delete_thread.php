<?php

    global $DATA_OBJ, $DB, $info;

    if (isset($DATA_OBJ->other_userid) ) {

        $chat = "";
        try {
            $chat = $DB->chatFinder($DATA_OBJ->other_userid, $_SESSION['userid']); // receiver, sender
        } catch (Exception $e) {
            $info->chat_contact = "No chats found: " . $e->getMessage();
            $info->data_type = "error";
        }

        if ($chat != "") {

            $query = "UPDATE `messages` SET `deleted_sender` = 1  WHERE `chat_id` = '$chat->chat_id' ";
            $DB->write($query);

            $query = "UPDATE `messages` SET `deleted_receiver` = 1  WHERE `chat_id` = '$chat->chat_id' ";
            $DB->write($query);

        }

    }
