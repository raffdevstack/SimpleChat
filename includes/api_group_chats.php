<?php

    global $DATA_OBJ, $DB, $info;

    print_r($DATA_OBJ); die;

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

        $html_messages = "
                    <div id='messages_wrapper'  >
                        <h1>Messages</h1>
                    </div>
                ";

        $info->chat_contact = $html_contacts_panel;
        $info->messages = $html_messages;
        $info->data_type = "group_chats";
    }

    echo json_encode($info);






