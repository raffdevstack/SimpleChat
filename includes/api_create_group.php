<?php

global $info, $DB;
$logged_user = $_SESSION['userid'];

$sql = "SELECT * FROM `users` WHERE `userid`!='$logged_user' ";
$users = $DB->read($sql, []);

$html_markup = '

    <div id="create_group_wrapper">
        <h2>Create Group Chat</h2>
        <h4>Add Members</h4>
        
    ';

if (is_array($users)) {
    foreach ($users as $user) {
        $fullname = $user->first_name . " " . $user->last_name;
        $html_markup .= "
            <div id='contact' userid='$user->userid' >
                <p>$fullname</p>
            </div>";
    }
}

$html_markup .= '
    </div>
';

$info->message = $html_markup;
$info->data_type = "settings"; // send to responseText
echo json_encode($info);