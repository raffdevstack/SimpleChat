<?php

global $info, $DB, $DATA_OBJ;
$logged_user = $_SESSION['userid'];

if (isset($DATA_OBJ->group_id)) {

    $data = [];
    $data['group_id'] = $DATA_OBJ->group_id;
    $query = "SELECT 
            u.userid,
            u.first_name,
            u.last_name,
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

    $html_markup = '

            <div id="form_wrapper">
            
                <h2>Edit Group Chat</h2>
                <h4 style="text-align: center">Edit roles</h4>
                
                
                    
                    <div id="member_roles_container">
                        ';

        $html_markup .= '<table border="1" cellpadding="10" cellspacing="0">';
        $html_markup .= '<thead><tr><th>Name</th><th>Role</th><th>Description</th><th></th></tr></thead>';
        $html_markup .= '<tbody>';

        foreach ($gmr_result as $row) {
            $html_markup .= '<tr>';
            $html_markup .= '<td>' . $row->first_name . ' ' . $row->last_name . '</td>';
            $html_markup .= '<td>' . $row->role_name . '</td>';
            $html_markup .= '<td>' . $row->description . '</td>';
            if ($row->role_name != "admin") {
                $html_markup .= '<td><button type="button" class="btn btn-primary" userid=" '.$row->userid.' " 
                                    onclick="editRole(event)">
                                    Edit Role</button></td>';  // Add button here
            } else {
                $html_markup .= '<td><button type="button" class="btn btn-primary" disabled>Edit Role</button></td>';  // Add button here
            }
            $html_markup .= '</tr>';
        }

    $html_markup .= '</tbody>';
        $html_markup .= '</table>';

    $info->message = $html_markup;
    $info->data_type = "edit_roles_view"; // send to responseText
}

echo json_encode($info);




