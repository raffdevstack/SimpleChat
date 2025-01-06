<?php

global $info, $DB, $DATA_OBJ;
$logged_user = $_SESSION['userid'];

if (isset($DATA_OBJ->group_id) ) {

    $data = [];
    $data["group_id"] = $DATA_OBJ->group_id;
    $query = "DELETE FROM `groups` WHERE `id`=:group_id";
    $result = $DB->write($query, $data);

    if ($result) {
        $info->message = "Group deleted successfully";
        $info->data_type = "delete_group";
    }
}

echo json_encode($info);




