<?php

global $info, $DB, $DATA_OBJ;
$logged_user = $_SESSION['userid'];

if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == 'remove_member') {

    if (isset($DATA_OBJ->group_id)) {

        $data = [];
        $data["group_id"] = $DATA_OBJ->group_id;
        $data["me"] = $logged_user;
        $query = "SELECT * 
        FROM 
            `group_member_roles` as gmr
        JOIN 
            users as u
        ON 
            gmr.user_id = u.userid
        WHERE 
            gmr.group_id = :group_id
        AND 
            u.userid != :me;
        ";
        $members = $DB->read($query, $data);

        $html_markup = '

    <div id="create_group_wrapper">
    
        <h2>Edit Group Chat</h2>
         <h4>Remove member</h4>
         
        <form id="remove_member_form">
            <div id="gc_contact_list">
        
    ';

        if (is_array($members)) {
            foreach ($members as $user) {
                $fullname = $user->first_name . " " . $user->last_name;
                $html_markup .= "
            <label class='gc_contact'>
                <input type='checkbox' name='$user->userid' >
                <p>$fullname</p>
            </label>";
            }
        }

        $html_markup .= '
                </div>
                <input type="button" groupid="' . htmlspecialchars($DATA_OBJ->group_id, ENT_QUOTES, 'UTF-8') . '" 
                    id="remove_selected_mmbrs_btn" onclick="removeSelectedMmbrs(event)" value="Remove Member">
            </form>
        </div>
    ';

        $info->message = $html_markup;
        $info->data_type = "remove_member"; // send to responseText

    }

} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == 'remove_member_ondb') {

    print_r($DATA_OBJ); die;

}



echo json_encode($info);




