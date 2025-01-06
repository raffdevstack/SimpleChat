<?php

require 'vendor/autoload.php';

use phpseclib3\Crypt\AES;

// Create a new AES object
$aes = new AES('cbc'); // 'cbc' mode

// Set a valid key length (16 bytes for AES-128)
$key = 'thisisaverysecre'; // Exactly 16 characters (16 bytes)
$aes->setKey($key);

//
//// The plaintext message to encrypt
//$plaintext = "Hello, this is a secret message!";
//
//// Encrypt the message
//$ciphertext = $aes->encrypt($plaintext);
//
//// Combine the IV and ciphertext for storage/transmission
//$encryptedData = base64_encode($iv . $ciphertext);
//
////echo "Encrypted: " . $encryptedData . PHP_EOL;
//
//// To decrypt, separate the IV and the ciphertext
//$decodedData = base64_decode($encryptedData);
//$iv = substr($decodedData, 0, 16); // First 16 bytes are the IV
//$ciphertext = substr($decodedData, 16); // The rest is the ciphertext
//
//// Set the IV for decryption
//$aes->setIV($iv);
//
//// Decrypt the message
//$decryptedText = $aes->decrypt($ciphertext);

// Generate a new IV for each field
$first_name_iv = openssl_random_pseudo_bytes(16);
$last_name_iv = openssl_random_pseudo_bytes(16);
$email_iv = openssl_random_pseudo_bytes(16);

require_once("classes/autoload.php");

$DB = new Database();
$DATA_RAW_STRING = file_get_contents('php://input');
$DATA_OBJ = json_decode($DATA_RAW_STRING); // this is like JSON.parse() in js

date_default_timezone_set('Asia/Manila');

$info = (object)[]; // this info object will be the base of the response to ajax

session_start();
// check if logged in
if (!isset($_SESSION['userid'])) { // if no userid in sessions, it's not logged in
    if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type != "login" && $DATA_OBJ->data_type != "signup" ) { // if we are not in the login page // --  --
        $info->logged_in  = false;
        echo json_encode($info); // put it in the info object, it is echoed so it is part of the result, the $info is maybe just a dummy object
        die;
    }
}

$Error = "";

// process the data
if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "signup") {
    include("includes/api_signup.php");
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "login") {
    include("includes/api_login.php");
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "logout") {
    include("includes/api_logout.php");
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "user_info") {
    include("includes/api_user_info.php");
} else if (isset($DATA_OBJ->data_type) && ($DATA_OBJ->data_type == "chats")
    || ($DATA_OBJ->data_type == "chats_refresh") || ($DATA_OBJ->data_type == "chats_contacts_refresh") ) {
    include("includes/api_chats.php");
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "group_chats") {
    include("includes/api_group_chats.php");
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "contacts") {
    include("includes/api_contacts.php");
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "settings") {
    include("includes/api_settings.php");
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "save_settings") {
    include("includes/api_save_settings.php");
}   else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "send_message") {
    include("includes/api_send_message.php");
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "delete_message") {
    include("includes/api_delete_message.php");
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "delete_thread") {
    include("includes/api_delete_thread.php");
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "create_group") {
    include("includes/api_create_group.php");
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "create_group_with") {
    include("includes/api_create_group_with.php");
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "add_group_member") {
    include("includes/api_add_group_member.php");
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "add_as_member") {
    include("includes/api_add_as_member.php");
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "rename_group") {
    include("includes/api_rename_group.php");
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "remove_member"
        || ($DATA_OBJ->data_type == "remove_member_ondb")) { //diri
    include("includes/api_remove_member.php");
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "update_group_name") {
    include("includes/api_update_group_name.php");
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "delete_group") {
    include("includes/api_delete_group.php");
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "edit_roles_view"
    ||  ($DATA_OBJ->data_type == "update_role_db")) {
    include("includes/api_edit_roles_view.php");
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "google-login") {
    include("process-google-login.php");
}


// template for messages on the left side
function getMessageLeft($user, $message)
{
    return "
        <div class='message_container'>
            <div class='message left_message'>
                <button message_id='$message->id' onclick='deleteMessage(event)' id='delete_message_left'>Delete</button>
                <h4>$user->first_name</h4>
                <p>$message->txt_message</p>
                <h6>". date('M j, Y, g:i a', strtotime($message->date)). "</h6>
            </div>
        </div>
    ";
}

// template for messages on the right side
function getMessageRight($message) {

    $status = "";
    if ($message->received == 1) {
        $status = "sent";
        if ($message->seen == 1) {
            $status = "seen";
        }
    }

    return "
        <div class='message_container'>
            <div class='message right_message'>
                <button message_id='$message->id' onclick='deleteMessage(event)' id='delete_message_right'>Delete</button>
                <h4>You</h4>
                <p>$message->txt_message</p>
                <h6>". date('M j, Y, g:i a', strtotime($message->date)). "</h6>
                <h6>" . $status . "</h6>
            </div>
        </div>
    ";
}

function generateRandomString($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function verifyEmail($storedEncryptedEmail, $loginEmailInput, $iv) {
    // Decode the stored encrypted email from base64
    global $aes;
    $combined = base64_decode($storedEncryptedEmail);

    // Extract IV and ciphertext
    // Assuming IV length is 16 bytes (128 bits) for AES
    $iv = substr($combined, 0, 16);
    $ciphertext = substr($combined, 16);

    // Set the same IV and encrypt the login input
    $aes->setIV($iv);
    $loginEmailCiphertext = $aes->encrypt($loginEmailInput);

    // Compare the ciphertexts
    return $ciphertext === $loginEmailCiphertext;
}

function decryptAES($storedEncrypted, $iv) {

    global $aes;

    $decodedData = base64_decode($storedEncrypted);
    $iv = substr($decodedData, 0, 16); // First 16 bytes are the IV
    $ciphertext = substr($decodedData, 16); // The rest is the ciphertext

// Set the IV for decryption
    $aes->setIV($iv);

// Decrypt the message
    return $aes->decrypt($ciphertext);
}



