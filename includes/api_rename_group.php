<?php

global $info, $DB, $DATA_OBJ;
$logged_user = $_SESSION['userid'];

if (isset($DATA_OBJ->group_id)) {

    $data = [];
    $data['group_id'] = $DATA_OBJ->group_id;
    $query = "SELECT * FROM `groups` WHERE `id` = :group_id LIMIT 1";
    $result = $DB->read($query,$data);

    if (is_array($result)) {

        $group = $result[0];

        $html_markup = '

            <div id="form_wrapper">
            
                <h2>Edit Group Chat</h2>
                <h4>Rename group</h4>
                
                <form id="settings_form">
                    
                    <div class="input-container">
                        <label for="group_name">Group Name:</label>
                         <br>
                        <input type="text" id="group_name" name="group_name" value="'.$group->group_name.'">
                    </div>
                        
                    <br>
                    <br>
                    <br>
                    
                    <input type="submit" group_id="'.$group->id.'" id="rename_group_btn" value="Rename Group" 
                        onclick="updateGroupName(event)" />

                </form>
                <p id="message"></p>
            </div>
        ';

        $info->message = $html_markup;
        $info->data_type = "rename_group"; // send to responseText
    }

}

echo json_encode($info);




