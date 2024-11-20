
<?php

    global $info, $DB;

    $result = $DB->read($query, ['userid'=>$id]);

    if (is_array($result)) {
        $user = $result[0];
    }

    $html_markup = '
        <div id="form_wrapper">
            <h2>Settings</h2>
            <p id="error" style="color: red; display: none"></p>
            <form id="signupForm">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" >
                <br>
                <br>
                <label for="password">Password:</label>
                <input id="password" name="password" >
                <br>
                <br>
                <br>
                <input type="submit" id="signup_button" value="Save Settings" />
            </form>
            <p id="message"></p>
        </div>
    ';

    $info->message = $html_markup;
    $info->data_type = "settings"; // send to responseText
    echo json_encode($info);

//    die;
//    $info->message = "No contacts found";
//    $info->data_type = "error";
//    echo json_encode($info);

?>


