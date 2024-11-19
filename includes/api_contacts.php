
<?php

    global $info;

    // $info = $result[0]; // get the first result (array)
    $info->data_type = "user_info"; // send to responseText
    echo json_encode($info);

    die;

    $info->message = "Wrong username";
    $info->data_type = "error";
    echo json_encode($info);

?>

<div id="contacts_container">
    <p>Username</p>
    <p>Username</p>
    <p>Username</p>
</div>
