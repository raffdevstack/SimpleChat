<?php

    global $DATA_OBJ, $DB, $info;

    $logged_user = $_SESSION['userid'];

    $my_group = "";

    // get the group chat
    if (isset($DATA_OBJ->group_id) ) {
        $data = [];
        $data["group_id"] = $DATA_OBJ->group_id;
        $query = "SELECT * FROM `groups` WHERE `id` = :group_id LIMIT 1";
        $result = $DB->read($query, $data);

        if (isset($result)) {
            $my_group = $result[0];
        } else {
            $info->message = "no group found";
            $info->data_type = "error";
        }
    }

    if ($my_group !== "") {

        // get my other chats
        $members_userid = [];

        $data = [];
        $data["group_id"] = $my_group->id;
        $query = "SELECT * FROM `group_members` WHERE `group_id` = :group_id GROUP BY `user_id`";
        $result = $DB->read($query, $data);
        if (is_array($result)) {
            foreach ($result as $member) {
                $members_userid[] = $member->user_id;
            }
        }

        // get the actual members


        $html_contacts_panel = ""; // initialize

        $html_contacts_panel = "
                    <h3>You are chatting with: </h3>
                    <h4>Group: $my_group->group_name </h4>
                    <div>
                        <button onclick='getChats(event)'>Back</button>
                    </div>
                    <h5>Members: </h5>
                    <p>";

        foreach ($members_userid as $member_userid) {
            $data_user["userid"] = $member_userid;
            $query = "SELECT * FROM `users` WHERE `userid` = :userid LIMIT 1 ";
            $result = $DB->read($query, $data_user);


            if (isset($result[0])) {
                $user = $result[0];
                $html_contacts_panel .= $user->first_name . " " . $user->last_name . "<br>";
            }
        }

        $html_contacts_panel .= "</p>";

        // get all messages from group_id
        $arr = [];
        $arr["group_id"] = $my_group->id    ;
        $query = "SELECT * FROM `messages` WHERE `group_id` = :group_id";
        $messages = $DB->read($query, $arr);

        $html_messages = "
                    <div id='messages_wrapper'  >
                        ";

        // if first chat in a group
        $group_data = [];
        $group_data["group_id"] = $my_group->id;
        $query = "SELECT * FROM `messages` WHERE `group_id` = :group_id ORDER BY `id` DESC LIMIT 1";
        $result = $DB->read($query, $group_data);
        $first_message = $result[0];

        foreach ($messages as $message) {

            if ($first_message->id == $message->id) {
                $html_messages .= "<p id='first_group_message'>" . $message->txt_message . "</p>";
            } else {
                if ($message->sender == $logged_user) {
                    $html_messages .= getMessageRight($message);
                } else {
                    $other_user = $DB->getChatReceiver($message->sender);
                    $html_messages .= getMessageLeft($other_user, $message); // user, message
                }
            }
        }

        $html_messages .= "
                    </div>
                ";

        $html_messages .= "
                <div id='messages_inputs'>
                    <label for='message_file' id='file_input_label'>File</label>
                    <input type='file' id='message_file' style='display: none' >
                    <input type='text' id='message_text' placeholder='Enter your message here...' >
                    <input type='button' onclick='sendMessage(event)' id='send_message' value='SEND'>    
                </div>
                ";

        $info->chat_contact = $html_contacts_panel;
        $info->messages = $html_messages;
        $info->data_type = "group_chats";
    }

    echo json_encode($info);






