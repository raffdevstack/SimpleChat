
<?php

    global $info;

    $contacts_markup = '
        <div id="contacts_container">
            <p>Username</p>
            <p>Username</p>
            <p>Username</p>
        </div>
    ';

    $info->message = $contacts_markup;
    $info->data_type = "contacts"; // send to responseText
    echo json_encode($info);

//    die;
//    $info->message = "No contacts found";
//    $info->data_type = "error";
//    echo json_encode($info);

?>


