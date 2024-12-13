
<?php

    global $info, $DB;

    $id = $_SESSION['userid'];
    $query = "SELECT * FROM `users` WHERE `userid` = :userid LIMIT 1";
    $result = $DB->read($query, ['userid'=>$id]);

    $html_markup = "";
    if (is_array($result)) {

        $user = $result[0];

        $html_markup = '

            <div id="form_wrapper">
            
                <h2>Settings</h2>
                
                <form id="settings_form">
                
                <div class="input-container">
                    <label for="profile_img">Profile:</label>
                    <input type="file" id="profile_img" name="profile_img" >
                    <p class="input_errors" id="profile_img_error"></p>
                </div>
                
                <div class="input-container">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="'.$user->email.'">
                    <p class="input_errors" id="email_error"></p>
                </div>
                
                <div class="input-container">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" value="'.$user->password.'" >
                    <p class="input_errors" id="password_error"></p>
                </div>
                    
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


