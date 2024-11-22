
<?php

    global $info, $DB;
    $logged_user = $_SESSION['userid'];

    $sql = "SELECT * FROM `users` WHERE `userid`!='$logged_user' ";
    $users = $DB->read($sql, []);

    $contacts_markup = '
    <style>
        #contact:hover {
            color: blue;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
    <div id="contacts_container">';
    if (is_array($users)) {
        foreach ($users as $user) {
            $contacts_markup .= "<p id='contact'>$user->username</p>";
        }
    }
    $contacts_markup .= '</div>';

    if($users) {
        $info->message = $contacts_markup;
        $info->data_type = "contacts"; // send to responseText
    } else {
        $info->message = "No contacts found";
        $info->data_type = "error";
    }
    echo json_encode($info);




