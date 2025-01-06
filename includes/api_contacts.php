<?php

    global$last_name_iv; global$first_name_iv;

    global $info, $DB;
    $logged_user = $_SESSION['userid'];

    $sql = "SELECT first_name,last_name,userid FROM `users` WHERE `userid`!='$logged_user' ";
    $users = $DB->read($sql, []);

    $contacts_markup = '<div id="contacts_container">';
    if (is_array($users)) {
        foreach ($users as $user) {

            $user->first_name = decryptAES($user->first_name, $first_name_iv);
            $user->last_name = decryptAES($user->last_name, $last_name_iv);

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




