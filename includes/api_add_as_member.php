<?php

global $info, $DB, $DATA_OBJ;
$logged_user = $_SESSION['userid'];

if (isset($DATA_OBJ->userid) && isset($DATA_OBJ->groupid)) {

    $data = [];
    $data["group_id"] = $DATA_OBJ->groupid;
    $data["role_id"] = 3;

    foreach ($DATA_OBJ->userid as $user) {

        $data["user_id"] = $user;
        $sql = "INSERT INTO group_member_roles (`group_id`, `user_id`, `role_id`) VALUES (:group_id, :user_id, :role_id)";
        $result = $DB->write($sql, $data);

        if ($result) {
            $info->message = "Success added group member";
            $info->group_id = $DATA_OBJ->groupid;
            $info->data_type = "add_as_member";
        } else {
            $info->message = "Failed to add group member";
            $info->data_type = "error";
        }
    }
}

echo json_encode($info);




