<?php

global $info, $DB, $DATA_OBJ;

fakjdfajf
get group id to update

$logged_user = $_SESSION['userid'];

if(isset($DATA_OBJ->group_id)) {

    $group_id = $DATA_OBJ->group_id;

    // get users that are not my group members
    $sql = "
        SELECT u.*
        FROM users u
        LEFT JOIN group_member_roles gm ON u.userid = gm.userid
        WHERE u.userid != '$logged_user' 
          AND (gm.group_id IS NULL OR gm.group_id != '$group_id')
    ";

    $users = $DB->read($sql);
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
            <input type="button" id="add_member_btn" onclick="addAsGroupMember()" value="Add To Group">
        </form>
    </div>
';

$info->message = $html_markup;
$info->data_type = "add_group_member"; // send to responseText
echo json_encode($info);