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

        $query = "SELECT * FROM `messages` WHERE `chat_id` = '$chat->chat_id'";
        $messages = $DB->read($query);

        if (is_array($messages) && count($messages) > 0) {
            foreach ($messages as $message) {

                if ($message->sender == $_SESSION['userid']) { // if I am the sender
                    $query = "UPDATE `messages` SET `deleted_sender` = 1 WHERE `id` = '$message->id' ";
                    $DB->write($query);
                }

                if ($message->receiver == $_SESSION['userid']) {
                    $query = "UPDATE `messages` SET `deleted_receiver` = 1 WHERE `id` = '$message->id' ";
                    $result = $DB->write($query);
                }

            }
        }



    }

echo json_encode($info);
