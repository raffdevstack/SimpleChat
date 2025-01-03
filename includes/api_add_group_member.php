<?php

global $info, $DB, $DATA_OBJ;

$logged_user = $_SESSION['userid'];

if(isset($DATA_OBJ->group_id)) {

    // Get users that are not my group members
    $data = [
        "group_id" => $DATA_OBJ->group_id,
        "logged" => $logged_user,
    ];
    $sql = "
        SELECT u.*
        FROM users u
        LEFT JOIN group_member_roles gmr 
            ON u.userid = gmr.user_id AND gmr.group_id = :group_id
        WHERE u.userid != :logged 
          AND gmr.group_id IS NULL
    ";

    $users = $DB->read($sql, $data);
}

$html_markup = '

    <div id="create_group_wrapper">
        <h2>Edit Group Chat</h2>
        <h4>Add Members</h4>
        <form id="create_group_form">
            <div id="gc_contact_list">
        
    ';

if (is_array($users)) {
    foreach ($users as $user) {
        $fullname = $user->first_name . " " . $user->last_name;
        $html_markup .= "
            <label class='gc_contact'>
                <input type='checkbox' name='$user->userid' >
                <p>$fullname</p>
            </label>";
    }
}

$html_markup .= '
            </div>
            <input type="button" group_id="" id="add_member_btn" onclick="addAsGroupMember()" value="Add To Group">
        </form>
    </div>
';

$info->message = $html_markup;
$info->data_type = "add_group_member"; // send to responseText
echo json_encode($info);