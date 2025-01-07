<?php

    global $DATA_OBJ, $DB, $info, $first_name_iv, $last_name_iv;

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

        $me_as_member = "";

        // get my other chats
        $members_userid = [];
        $data = [];
        $data["group_id"] = $my_group->id;
        $query = "SELECT * FROM `group_member_roles` WHERE `group_id` = :group_id GROUP BY `user_id`";
        $members = $DB->read($query, $data);

        // collect the userid of the all members
        if (is_array($members)) {
            foreach ($members as $member) {
                $members_userid[] = $member->user_id;

                if ($member->user_id == $logged_user) {
                    $me_as_member = $member;
                }
            }
        }

        $html_contacts_panel = ""; // initialize

        $html_contacts_panel = "
                    <h3>You are chatting with: </h3>
                    <div id='group_chat_side_bar'>
                        <h4>$my_group->group_name </h4>
                        <div>
                            <button onclick='getChats(event)'>Back</button>
                        </div>
                        <h5 style='display: inline-block'>Your Role: </h5> 
                    ";
        // get the role
        $get_role_data = [
            "role_id" => $me_as_member->role_id,
        ];
        $get_role_query = "SELECT * FROM `roles` WHERE `id` = :role_id LIMIT 1";
        $get_role = $DB->read($get_role_query, $get_role_data);

        if (isset($get_role)) {
            $role = $get_role[0];
            $html_contacts_panel .= "
                 <p style='display: inline-block'>$role->name</p>
            ";
        }

        $html_contacts_panel .="
                    <h5>Actions: </h5>
                    ";

        $is_permitted = false;
        if ($DB->hasPermission($me_as_member->user_id, $me_as_member->group_id, 'add_member')->count > 0) {
            $html_contacts_panel .= "<button class='group_actions' group_id='$me_as_member->group_id'
                onclick='addGroupMember(event)'>Add Member</button>";
            $is_permitted = true;
        }
        if ($DB->hasPermission($me_as_member->user_id, $me_as_member->group_id, 'delete_group')->count > 0) {
            $html_contacts_panel .= "<button class='group_actions' group_id='$me_as_member->group_id'
                onclick='removeMember(event)'>Remove Member</button>";
            $is_permitted = true;
        }
        if ($DB->hasPermission($me_as_member->user_id, $me_as_member->group_id, 'delete_group')->count > 0) {
            $html_contacts_panel .= "<button class='group_actions' group_id='$me_as_member->group_id'
                onclick='editRoles(event)'>Edit Roles</button>";
            $is_permitted = true;
        }
        if ($DB->hasPermission($me_as_member->user_id, $me_as_member->group_id, 'delete_group')->count > 0) {
            $html_contacts_panel .= "<button class='group_actions' group_id='$me_as_member->group_id'
                onclick='editPermissions(event)'>Edit Permissions</button>";
            $is_permitted = true;
        }
        if ($DB->hasPermission($me_as_member->user_id, $me_as_member->group_id, 'rename_group')->count > 0) {
            $html_contacts_panel .= "<button class='group_actions' group_id='$me_as_member->group_id'
                onclick='renameGroup(event)'>Rename Group</button>";
            $is_permitted = true;
        }
        if ($DB->hasPermission($me_as_member->user_id, $me_as_member->group_id, 'delete_group')->count > 0) {
            $html_contacts_panel .= "<button class='group_actions' group_id='$me_as_member->group_id'
                onclick='deleteGroup(event)'>Delete Group</button>";
            $is_permitted = true;
        }

        if (!$is_permitted) {
            $html_contacts_panel .= "A member has limited permissions to this group.";
        }

        $html_contacts_panel .= "                    
                    <h5>Members: </h5>
                    <p>";

        foreach ($members_userid as $member_userid) {
            $data_user["userid"] = $member_userid;
            $query = "SELECT * FROM `users` WHERE `userid` = :userid LIMIT 1 ";
            $result = $DB->read($query, $data_user);

            if (isset($result[0])) {
                $user = $result[0];
                $user->first_name = decryptAES($user->first_name, $first_name_iv);
                $user->last_name = decryptAES($user->last_name, $last_name_iv);
                $html_contacts_panel .= $user->first_name . " " . $user->last_name . "<br>";
            }
        }

        $html_contacts_panel .= "
                </p>
            </div>
        ";

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
        $query = "SELECT * FROM `messages` WHERE `group_id` = :group_id ORDER BY `id` LIMIT 1";
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
                    $other_user->first_name = decryptAES($other_user->first_name, $first_name_iv);
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
                    <input type='text' onkeyup='enter_pressed_on_group(event)' id='message_text' placeholder='Enter your message here...' >
                    <input type='button' onclick='sendGroupMessage(event)' id='send_message' value='SEND'>    
                </div>
                ";

        $info->chat_contact = $html_contacts_panel;
        $info->messages = $html_messages;
        $info->data_type = "group_chats";
    }

    echo json_encode($info);






