<?php

global $info, $DB, $DATA_OBJ, $first_name_iv;
$logged_user = $_SESSION['userid'];

if (isset($DATA_OBJ->userid) && $DATA_OBJ->userid !== []) {

    $users_array = $DATA_OBJ->userid;

    array_unshift($users_array, $logged_user); // add myself as the first value

    // find me
    $me = $DB->userFinderId($logged_user);

    // retrieve names from db
    $id_list = implode(",", $users_array);

    // search groups with the users
    $query = "SELECT gmr.group_id
    FROM group_member_roles gmr
    WHERE gmr.user_id IN ($id_list)
    GROUP BY gmr.group_id
    HAVING COUNT(DISTINCT gmr.user_id) = " . count($users_array) . "
    AND NOT EXISTS (
        SELECT 1 
        FROM group_member_roles gmr2 
        WHERE gmr2.group_id = gmr.group_id 
        AND gmr2.user_id NOT IN ($id_list)
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

            for ($i = 0; $i < count($users_array); $i++) { // traverses all users

                $user = $users_array[$i];  // retrieve the user at count $i
                $data["user_id"] = $user;

                if ($i == 0) { // the first user as admin which is always me
                    $data["role_id"] = 1;
                } else {
                    $data["role_id"] = 3;
                }

                $query = "INSERT INTO `group_member_roles`(`group_id`, `user_id`, `role_id`) VALUES(:group_id, :user_id, :role_id)";
                $result = $DB->write($query, $data);

                if ($result) {
                    $info->message = "Your group and permissions has been created.";
                    $info->group_id = $gc->id;
                    $info->data_type = "create_group_with";

                }
            }

            if ($info->data_type == "create_group_with") {
                // create first message here
                $me->first_name = decryptAES($me->first_name, $first_name_iv);
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
