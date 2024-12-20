
<?php

    global $info, $DB;
    $logged_user = $_SESSION['userid'];

    $sql = "SELECT * FROM `users` WHERE `userid`!='$logged_user' ";
    $users = $DB->read($sql, []);

    $contacts_markup = '<div id="contacts_container">';
    if (is_array($users)) {
        foreach ($users as $user) {
            $fullname = $user->first_name . " " . $user->last_name;
            $contacts_markup .= "
                <div class='contact' id='contact' userid='$user->userid' onclick='startChat(event)'>
                    <img src='images/profile.png' />
                    <p>$fullname</p>
                </div>";
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




