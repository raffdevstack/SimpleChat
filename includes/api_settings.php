
<?php

    global $info, $DB;

    $query = "SELECT * FROM `users` WHERE `userid` = :userid LIMIT 1";
    $id = $_SESSION['userid'];
    $result = $DB->read($query, ['userid'=>$id]);

    $html_markup = "";
    if (is_array($result)) {
        $user = $result[0];
        $html_markup = '
        <div id="form_wrapper">
            <h2>Settings</h2>
            <p id="error" style="color: red; display: none"></p>
            <form id="settings_form">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="'.$user->username.'" >
                <br>
                <br>
                <label for="password">Password:</label>
                <input id="password" name="password" value="'.$user->password.'" >
                <br>
                <br>
                <br>
                <input type="submit" id="save_settings_button" value="Save Settings" onclick="collectUpdatedSettings()" />
            </form>
            <p id="message"></p>
        </div>
    ';
    }



    $info->message = $html_markup;
    $info->data_type = "settings"; // send to responseText
    echo json_encode($info);


