<?php

global $info, $DB, $DATA_OBJ, $first_name_iv, $last_name_iv;
$logged_user = $_SESSION['userid'];

if (isset($DATA_OBJ->group_id) && $DATA_OBJ->data_type == 'edit_roles_view') {

    $data = [];
    $data['group_id'] = $DATA_OBJ->group_id;
    $query = "SELECT
            gmr.group_id,
            u.userid,
            u.first_name,
            u.last_name,
            r.id AS role_id,
            r.name AS role_name,
            r.description
        FROM 
            group_member_roles gmr
        JOIN 
            users u ON gmr.user_id = u.userid
        JOIN 
            roles r ON gmr.role_id = r.id
        WHERE 
            gmr.group_id = :group_id
        ORDER BY 
            r.id ASC;
    ";

    $gmr_result = $DB->read($query, $data);

    if (is_array($gmr_result)) {

        $html_markup = '

            <div id="form_wrapper">
            
                <h2>Edit Group Chat</h2>
                <h4 style="text-align: center">Edit roles</h4>
                
                
                    
                    <div id="member_roles_container">
                        ';

        $html_markup .= '<table border="1" cellpadding="10" cellspacing="0">';
        $html_markup .= '<thead><tr><th>Name</th><th>Role</th><th>Description</th></tr></thead>';
        $html_markup .= '<tbody>';

        foreach ($gmr_result as $row) {
            // decrypt first
            $row->first_name = decryptAES($row->first_name, $first_name_iv);
            $row->last_name = decryptAES($row->last_name, $last_name_iv);

            $html_markup .= '<tr>';
            $html_markup .= '<td>' . $row->first_name . ' ' . $row->last_name . '</td>';

            // Add dropdown for roles
            $html_markup .= '<td>';
            $html_markup .= '<select class="role-dropdown" userid="' . $row->userid . '"
                 group_id="' . $row->group_id . '" onchange="updateRole(event)">';

            // get all roles
            $query = "SELECT `name`,`id` from `roles` ORDER BY `id`;";
            $all_roles = $DB->read($query);

            if (is_array($all_roles)) {
                foreach ($all_roles as $role) {
                    $selected = ($role->id == $row->role_id) ? 'selected' : '';
                    $html_markup .= '<option value="' . $role->name . '" ' . $selected . '>' . $role->name . '</option>';
                }
            }

            $html_markup .= '</select>';
            $html_markup .= '</td>';


            $html_markup .= '<td>' . $row->description . '</td>';
            $html_markup .= '</tr>';
        }

        $html_markup .= '</tbody>';
        $html_markup .= '</table>';

        $info->message = $html_markup;
        $info->data_type = "edit_roles_view"; // send to responseText
    }

} else if ($DATA_OBJ->data_type == 'update_role_db') {

    $role_data = [];
    $role_data['name'] = $DATA_OBJ->new_role;
    $sqlGetRoleId = "SELECT id FROM roles WHERE name = :name LIMIT 1";
    $role = $DB->read($sqlGetRoleId, $role_data);

    if (is_array($role)) {

        $role_id = $role[0]->id;

        $data = [];
        $data['user_id'] = $DATA_OBJ->user_id;
        $data['new_role_id'] = $role_id;
        $data['group_id'] = $DATA_OBJ->group_id;
        $sql = "UPDATE group_member_roles SET role_id = :new_role_id WHERE user_id = :user_id AND group_id = :group_id LIMIT 1";
        $result = $DB->write($sql, $data);

        if ($result) {
            $info->message = "Successfully updated role";
            $info->group_id = $DATA_OBJ->group_id;
            $info->data_type = "update_role_db"; // send to responseText
        }

    }

}

echo json_encode($info);




