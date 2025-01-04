<?php

global $info, $DB, $DATA_OBJ;
$logged_user = $_SESSION['userid'];

if (isset($DATA_OBJ->group_id) && isset($DATA_OBJ->new_group_name) ) {

    $data = [];
    $data['new_group_name'] = $DATA_OBJ->new_group_name;
    $data['group_id'] = $DATA_OBJ->group_id;
    $query = "UPDATE `groups` SET group_name = :new_group_name WHERE `id` = :group_id";
    $result = $DB->write($query, $data);

    if ($result) {
        $info->message = "Group renamed successfully";
        $info->data_type = "update_group_name";
    }

}

echo json_encode($info);




