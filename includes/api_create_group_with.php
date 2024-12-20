<?php

global $info, $DB, $DATA_OBJ;
$logged_user = $_SESSION['userid'];

if (isset($DATA_OBJ->userid) && $DATA_OBJ->userid !== []) {

    $names = [];

    // retrieve names from db
    $id_list = implode(",", array_map('intval', $DATA_OBJ->userid)); // Convert the user IDs to integers to prevent SQL injection
    $query = "SELECT * FROM `users` WHERE `userid` IN ($id_list) LIMIT 3";
    $result = $DB->read($query);

    if (is_array($result)) {
        foreach ($result as $user) {
            $names[] = $user->first_name;
        }
    }

    $concatenated_names = implode(", ", $names);


    // create group on db
    $data = [];
    $data['name'] = $concatenated_names;
    $data['created_by'] = $logged_user;
    $query = "INSERT INTO `groups`(`group_name`,`created_by`) VALUES(:name,:created_by)";
    $result = $DB->write($query, $data);

    if ($result) {
        $info->message = "Your group has been created.";
        $info->data_type = "create_group_with";
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
