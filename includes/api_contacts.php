
<?php

    global $info;

    $contacts_markup = '<div id="contacts_container">';
    for ($i = 1; $i <= 10; $i++) {
        $contacts_markup .= "<p>Username {$i}</p>";
    }
    $contacts_markup .= '</div>';

    $info->message = $contacts_markup;
    $info->data_type = "contacts"; // send to responseText
    echo json_encode($info);

//    die;
//    $info->message = "No contacts found";
//    $info->data_type = "error";
//    echo json_encode($info);

?>


