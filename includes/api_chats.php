<?php

    global $DATA_OBJ, $DB, $info;

    $new_message = false;

    $refresh = false;
    $chat_refresh = false;
    $seen = false;
    if ($DATA_OBJ->data_type == "chats_refresh") { // messages refresh
        $refresh = true;
        $seen = $DATA_OBJ->seen;
    } else if ($DATA_OBJ->data_type == "chats_contacts_refresh") { // contacts refresh
        $chat_refresh = true;
    }

    if (isset($DATA_OBJ->userid) ) { // if we opened a message

        $chat_other_user = $DB->getChatReceiver($DATA_OBJ->userid);

        if ($chat_other_user) {

            $html_contacts_panel = ""; // initialize
            if (!$refresh) { // don't redisplay if we are refreshing the messages
                $html_contacts_panel = "
                    <h3>You are chatting with: </h3>
                    <div id='back-to-chat' style='width: 100%; position: relative;'>
                        <button onclick='getChats(event)' style='right: 0; position: absolute'>Back</button>
                    </div>
                    <p>$chat_other_user->username</p>
                ";
            }

            // find chat if exist
            $chat = "";
            try {
                $chat = $DB->chatFinder($chat_other_user->userid, $_SESSION['userid']); // other's userid and my userid
            } catch (Exception $e) {
                $info->chat_contact = "No chats found: " . $e->getMessage();
                $info->data_type = "error";
            }

            // find chat messages
            $chat_messages = "";
            if ($chat != "") { // if a chat exists, get its messages
                $chat_messages = $DB->getChatMessages($chat->chat_id);
            }

            $html_messages = ""; // initialize messages section
            if (!$refresh) { // don't redisplay if we are refreshing the messages
                $html_messages = "
                    <div id='messages_wrapper' onclick='setSeen(event)' '>
                ";
            }

            // the messages itself
            if ($chat_messages) {
                foreach ($chat_messages as $message) {
                    if ($message->receiver == $chat_other_user->userid) { // if the other person is the receiver
                        $html_messages .= getMessageRight($message);
                    } else {
                        $html_messages .= getMessageLeft($chat_other_user, $message); // if the receiver is me

                        if ($message->receiver == $_SESSION['userid'] && $message->received == 0) {
                            $new_message = true;
                        }

                        if ($message->receiver == $_SESSION['userid']) {

                            if ($seen && $message->received == 1 && $message->seen == 0) {
                                $DB->write("UPDATE `messages` SET `seen` = 1 WHERE `sender` = " . $chat_other_user->userid . " ");
                            }
                            if ($message->received == 0) {
                                $DB->write("UPDATE `messages` SET `received` = 1 WHERE `sender` = " . $chat_other_user->userid . " ");
                            }
                        }
                    }
                }
            }

            $html_messages .= "</div>";

            if (!$refresh) { // don't redisplay if we are refreshing the messages
                $html_messages .= "
                <div id='messages_inputs'>
                    <label for='message_file' id='file_input_label'>File</label>
                    <input type='file' id='message_file' style='display: none' >
                    <input type='text' onkeyup='enter_pressed(event)' id='message_text' placeholder='Enter your message here...' >
                    <input type='button' onclick='sendMessage(event)' id='send_message' value='SEND'>    
                </div>
                ";
            }

            $info->chat_contact = $html_contacts_panel;
            $info->messages = $html_messages;
            $info->data_type = "chats"; // send to responseText
            $info->new_message = $new_message;
            if ($refresh) { // if we are refreshing, only refresh the messages section
                $info->data_type = "chats_refresh";
            }
        } else { // if other user is not found
            $info->chat_contact = "No chats found";
            $info->data_type = "error";
        }

    } else { // if we haven't open a chat

        $all_chats = $DB->findAllMyChat($_SESSION['userid']);

        $html_previous_chats_panel = "";
        if (is_array($all_chats)) { // if chats exist

            $html_previous_chats_panel = "
                <h4>Previous Chats: </h4>
                <div>
            ";

            foreach ($all_chats as $chat) {

                $other_user_id = $chat->sender; // the default other user is the sender.
                if ($chat->sender == $_SESSION['userid']) { // if the sender is me,
                    $other_user_id = $chat->receiver; // the other user is the receiver of the chat
                }

                $other_user_obj = $DB->getChatReceiver($other_user_id);
                $html_previous_chats_panel .= "
                    <div id='previous_chat_item' userid='$other_user_obj->userid' onclick='startChat(event)'>
                        <h5>$other_user_obj->username</h5>
                        <p>$chat->txt_message</p>
                    </div>
                ";

                if ($chat->received == 0) {
                    $new_message = true;
                }

                if ($chat->receiver == $_SESSION['userid'] && $chat->received == 0) {
                    $DB->write("UPDATE `messages` SET `received` = 1 WHERE `sender` = " . $other_user_obj->userid . " ");
                }
            };


                $html_previous_chats_panel .= "
            </div>
            ";

        } else { // if we can't find any chat
            $html_previous_chats_panel = "<p>Go to contacts to start a chat</p>";
        }

        $html_messages = "
            <div id='messages_wrapper'>
                <h1>click chats to open</h1>
                <input id='message_text' style='display: none'>
            </div>
        ";

        $info->chat_contact = $html_previous_chats_panel;
        $info->messages = $html_messages;
        $info->data_type = "chats";
        if ($chat_refresh) {
            $info->chat_contact = $html_previous_chats_panel;
            $info->data_type = "chats_contacts_refresh";
            $info->new_message = $new_message;
        }

    }

    echo json_encode($info);






