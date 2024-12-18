<?php

global $info, $DB;

$html_markup = "
    <div style='background-color: red'>
        <h1>Create a group</h1>
    </div>
";

$info->message = $html_markup;
$info->data_type = "settings"; // send to responseText
echo json_encode($info);