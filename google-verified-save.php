<?php

global $info, $DB, $DATA_OBJ, $first_name_iv, $last_name_iv, $email_iv, $aes;


// prevent duplicate
$data['email'] = $DATA_OBJ->google_id;
$sql = "SELECT COUNT(*) FROM users WHERE email = :email";
$result = $DB->read($sql, $data);

if (is_array($result)) {

    $count = $result[0]->{'COUNT(*)'};

    if ($count == 0) {

        $aes->setIV($first_name_iv);
        $first_name_ciphertext = $aes->encrypt($DATA_OBJ->first_name);
        $aes->setIV($last_name_iv);
        $last_name_ciphertext = $aes->encrypt($DATA_OBJ->last_name);
        $aes->setIV($email_iv);
        $email_ciphertext = $aes->encrypt($DATA_OBJ->email);

        $encrypted_first_name = base64_encode($first_name_iv . $first_name_ciphertext);
        $encrypted_last_name = base64_encode($last_name_iv . $last_name_ciphertext);
        $encrypted_email = base64_encode($email_iv . $email_ciphertext);

        $user_data ['first_name'] = $encrypted_first_name;
        $user_data ['last_name'] = $encrypted_last_name;
        $user_data ['email'] = $encrypted_email;
        $user_data['userid'] = (int) $DATA_OBJ->google_id;

        $query_user = "INSERT INTO `users` (`first_name`, `last_name`, `email`, `userid`) 
          VALUES (:first_name, :last_name, :email, :userid)";
        $result = $DB->write($query_user, $user_data);

        if ($result == 1) {
            $_SESSION['userid'] = $user_data['userid']; // store user id to session
            $info->message = "Successfully authenticated " . $DATA_OBJ->email; ;
            $info->data_type = "google_authenticated";
        }

    } else {
        $info->message = "Email already used";
        $info->data_type = "error";
    }

}



echo json_encode($info);