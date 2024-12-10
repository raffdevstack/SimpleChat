<?php

    global $DATA_OBJ, $DB, $info;

    if (isset($DATA_OBJ->message_id) ) {

        $query = "SELECT * FROM `messages` WHERE `id` = '$DATA_OBJ->message_id' LIMIT 1";
        $message = $DB->read($query);

        if (isset($message)) {

            $message = $message[0];

            if ($message->sender == $_SESSION['userid']) { // if I am the sender
                $query = "UPDATE `messages` SET `deleted_sender` = 1 WHERE `id` = '$DATA_OBJ->message_id' LIMIT 1";
                $DB->write($query);
            }

            if ($message->receiver == $_SESSION['userid']) {
                $query = "UPDATE `messages` SET `deleted_receiver` = 1 WHERE `id` = '$DATA_OBJ->message_id' LIMIT 1";
                $DB->write($query);
            }

        }
    }
