<?php

require_once("../classes/config.php");
require_once("../classes/database2.php"); // Add this to include the Database class

$DB = new Database(); // Initialize the Database object

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Find the user by token
    $query = "SELECT * FROM users WHERE VerificationToken = ?";
    $user = $DB->read($query, [$token]);

    if ($user && !$user[0]->IsVerified) { // Verify if the user exists and is not already verified
        // Mark user as verified
        $update_query = "UPDATE users SET IsVerified = TRUE, VerificationToken = NULL WHERE VerificationToken = ?";
        $DB->write($update_query, [$token]);

        echo "Your account has been verified! You can now log in.";
    } else {
        echo "Invalid or expired verification token.";
    }
} else {
    echo "No token provided.";
}
?>
