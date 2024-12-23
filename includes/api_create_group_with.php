<?php

global $info, $DB, $DATA_OBJ;
$logged_user = $_SESSION['userid'];


if (isset($DATA_OBJ->userid) && $DATA_OBJ->userid !== []) {

    $users_array = $DATA_OBJ->userid;

    array_unshift($users_array, $logged_user); // add myself as the first value

    // find me
    $me = $DB->userFinderId($logged_user);

    // retrieve names from db
    $id_list = implode(",", $users_array);

    // search groups with the users
    $query = "SELECT gm.group_id
    FROM group_members gm
    WHERE gm.user_id IN ($id_list)
    GROUP BY gm.group_id
    HAVING COUNT(DISTINCT gm.user_id) = " . count($users_array) . "
    AND NOT EXISTS (
        SELECT 1 
        FROM group_members gm2 
        WHERE gm2.group_id = gm.group_id 
        AND gm2.user_id NOT IN ($id_list)
    )";
    $results_same_group = $DB->read($query);

    if (!empty($results_same_group)) {
        $info->message = "Same group already exists";
        $info->data_type = "error";
        echo json_encode($info);
        exit;
    }

    $created_group_name = "Group ". $DB->generate_id(1000);

    // create group on db
    $data = [];
    $data['name'] = "$created_group_name";
    $data['created_by'] = $logged_user;
    $query = "INSERT INTO `groups`(`group_name`,`created_by`) VALUES(:name,:created_by)";
    $result = $DB->write($query, $data);

    if ($result) {

//        add members to db

//        get the group
        $data = [];
        $data["gc_name"] = $created_group_name;
        $query = "SELECT * FROM `groups` ORDER BY `created_at` AND `group_name` = :gc_name DESC LIMIT 1;";
        $group_chat = $DB->read($query, $data);

        if (is_array($group_chat)) {
            $gc = $group_chat[0];

            $data = [];
            $data["group_id"] = $gc->id;

            for ($i = 0; $i < count($users_array); $i++) {

                $user = $users_array[$i];  // Make sure to retrieve the user at index $i
                $data["user_id"] = $user;

                if ($i == 0) {
                    $data["role"] = "admin";
                } else {
                    $data["role"] = "member";
                }

                $query = "INSERT INTO `group_members`(`group_id`, `user_id`, `role`) VALUES(:group_id, :user_id, :role)";
                $result = $DB->write($query, $data);

                if ($result) {
                    $info->message = "Your group has been created.";
                    $info->group_id = $gc->id;
                    $info->data_type = "create_group_with";

                    $role_data = [];
                    $role_data["role"] = $data["role"];
                    $roles_query = "SELECT * FROM `roles` WHERE `name` = :role LIMIT 1";
                    $roles_from_db = $DB->read($roles_query, $role_data);
                    $role_from_db = $roles_from_db[0];

                    $gmr_data = [];
                    $gmr_data["group_id"] = $gc->id;
                    $gmr_data["user_id"] = $user;
                    $gmr_data["role_id"] = $role_from_db->id;
                    $gmr_query = "INSERT INTO `group_member_roles`(`group_id`, `user_id`, `role_id`) 
                        VALUES(:group_id, :user_id, :role_id)";
                    $gmr_result = $DB->write($gmr_query, $gmr_data);

                    if ($gmr_result) {
                        $info->message = "Your group and permissions has been created.";
                        $info->group_id = $gc->id;
                        $info->data_type = "create_group_with";
                    }
                }

            }

            if ($info->data_type == "create_group_with") {
                // create first message here
                $msg_data["txt_message"] = $me->first_name . " created a new group";
                $msg_data["group_id"] = $gc->id;
                $msg_data["sender"] = $logged_user;
                $msg_data["date"] =  date('Y-m-d H:i:s');
                $query = "INSERT INTO `messages`(`group_id`, `txt_message`, `sender`, `date`) 
                    VALUES(:group_id, :txt_message, :sender, :date)";
                $result = $DB->write($query, $msg_data);
            }
        }

    } else {
        $info->message = "Your group has not been created due to some sql error.";
        $info->data_type = "error";
    }

} else {
    $info->message = "select a contact to create group";
    $info->data_type = "error";
}

// add group members to group
// display a ui


//$info->data_type = "create_group_with"; // send to responseText
echo json_encode($info);
