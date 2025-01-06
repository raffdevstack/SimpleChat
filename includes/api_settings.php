<?php

    global$email_iv;

    global $info, $DB;

    $id = $_SESSION['userid'];
    $query = "SELECT * FROM `users` WHERE `userid` = :userid LIMIT 1";
    $result = $DB->read($query, ['userid'=>$id]);

    $html_markup = "";
    if (is_array($result)) {

        $user = $result[0];

        $user->email = decryptAES($user->email, $email_iv);


        $html_markup = '

            <div id="form_wrapper">
            
                <h2>Settings</h2>
                
                <div id="profile_img_set">
                    <label for="profile_img" id="change_profile_button">Change Profile</label>
                    <input type="file" id="profile_img" onchange="uploadProfileImg(this.files)" >
                    <p class="input_errors" id="profile_img_error"></p>
                </div>
                
                <form id="settings_form">
                    
                    <div class="input-container">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="'.$user->email.'">
                        <p class="input_errors" id="email_error"></p>
                    </div>
                    
                    <br>
                    
                    <div class="input-container">
                        <label for="password">Old Password:</label>
                        <input type="text" id="password" name="password" > 
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


