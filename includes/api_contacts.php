
<?php

    global $info, $DB;

    $sql = "SELECT * FROM `users` LIMIT 10";
    $users = $DB->read($sql, []);

    $contacts_markup = '<div id="contacts_container">';
    if (is_array($users)) {
        foreach ($users as $user) {
            $contacts_markup .= "<p>$user->username</p>";
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




