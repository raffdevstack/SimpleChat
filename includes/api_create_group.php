<?php

global $info, $DB;
$logged_user = $_SESSION['userid'];

$sql = "SELECT * FROM `users` WHERE `userid`!='$logged_user' ";
$users = $DB->read($sql, []);

$html_markup = '

    <div id="create_group_wrapper">
        <h2>Create Group Chat</h2>
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
            <input type="button" id="create_group_with_btn" onclick="createGroupWith()" value="Create Group">
        </form>
    </div>
';

$info->message = $html_markup;
$info->data_type = "create_group"; // send to responseText
echo json_encode($info);