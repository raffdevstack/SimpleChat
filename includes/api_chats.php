<?php

    // last check 12/24/24

    global $DATA_OBJ, $DB, $info;

    $logged_user = $_SESSION['userid'];

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

        if ($chat_other_user) {  // if other user is found in database

            $html_contacts_panel = ""; // initialize
            if (!$refresh) { // don't redisplay if we are refreshing the messages
                $html_contacts_panel = "
                    <h3>You are chatting with: </h3>
                    <div id='back-to-chat' style='width: 100%; position: relative;'>
                        <button onclick='getChats(event)' style='right: 0; position: absolute'>Back</button>
                    </div>
                    <p>$chat_other_user->first_name</p>
                    <button userid='$chat_other_user->userid' onclick='deleteThread(event)'>Delete Thread</button>
                ";
            }

            // find chat if exist
            $chat = "";
            try {
                $chat = $DB->chatFinder($chat_other_user->userid, $_SESSION['userid']); // args receiver, sender
            } catch (Exception $e) {
                $info->chat_contact = "No chats found: " . $e->getMessage();
                $info->data_type = "error";
            }

            // find chat messages
            $chat_messages = "";
            if ($chat != "") { // if a chat exists, get its messages
                $chat_messages = $DB->getChatMessages(
                    $chat->chat_id,
                    $chat_other_user->userid, // receiver
                    $_SESSION['userid'] // sender
                );
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
            $info->chat_contact = "No chats found in records";
            $info->data_type = "error";
        }

    } else { // if we haven't open a chat

        $all_chats = $DB->findAllMyChat($_SESSION['userid']);

        $data = [];
        $data["userid"] = $logged_user;
        $query = "SELECT m1.* 
            FROM `messages` AS m1 
            JOIN `group_member_roles` AS gmr ON m1.group_id = gmr.group_id 
            WHERE m1.`group_id` IS NOT NULL 
              AND gmr.`user_id` = :userid  
              AND m1.`id` = (
                  SELECT MAX(`id`) 
                  FROM `messages` AS m2 
                  WHERE m1.`group_id` = m2.`group_id`
              )
            ORDER BY m1.`id` DESC;
            ";

        $all_group_chats = $DB->read($query, $data);

        $aggregated_chats = "";
        if (is_array($all_group_chats) && is_array($all_chats)) {
            $aggregated_chats = array_merge($all_chats, $all_group_chats);
        } else if (is_array($all_chats)) {
            $aggregated_chats = $all_chats;
        } else if (is_array($all_group_chats)) {
            $aggregated_chats = $all_group_chats;
        }

        // sort aggregated chats by latest
        if (is_array($aggregated_chats)) {
            usort($aggregated_chats, function ($a, $b) {
                return $b->id - $a->id;
            });
        }

        $html_previous_chats_panel = "";

        if ($aggregated_chats != "") { // if chats exist

            $html_previous_chats_panel = "
                <h4>Previous Chats: </h4>
                <div id='previous_chats'>
            ";

            $group = "";
            $chat_name = "";
            foreach ($aggregated_chats as $chat) {

                if ($chat->group_id !== NULL)  { // if it's a group message
                    // get group
                    $group = $DB->groupFinderId($chat->group_id);
                    $chat_name = $group->group_name;

                    $html_previous_chats_panel .= "
                    <div class='previous_chat_item' groupid='$chat->group_id'  onclick='startChatWithGroupFromChats(event)'>
                        <h5>$chat_name</h5>
                        <p>$chat->txt_message</p>
                    </div>
                    ";

                } else { // not a group message

                    $other_user_id = $chat->sender; // the default other user is the sender.
                    if ($chat->sender == $_SESSION['userid']) { // if the sender is me,
                        $other_user_id = $chat->receiver; // the other user is the receiver of the chat
                    }

                    $other_user_obj = $DB->getChatReceiver($other_user_id);
                    $chat_name = $other_user_obj->first_name . " " . $other_user_obj->last_name;

                    $html_previous_chats_panel .= "
                    <div class='previous_chat_item' userid='$other_user_obj->userid' onclick='startChat(event)'>
                        <h5>$chat_name</h5>
                        <p>$chat->txt_message</p>
                    </div>
                    ";

                    // set it to received if it is
                    if ($chat->receiver == $_SESSION['userid'] && $chat->received == 0) {
                        $new_message = true;
                    }
                    if ($chat->receiver == $_SESSION['userid'] && $chat->received == 0) {
                        $DB->write("UPDATE `messages` SET `received` = 1 WHERE `sender` = " . $other_user_obj->userid . " ");
                    }
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






