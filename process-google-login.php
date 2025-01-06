<?php

require_once 'vendor/autoload.php'; // Include Google API PHP Client library

global $info, $DB, $DATA_OBJ;

print_r($DATA_OBJ);

use Google\Client;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_token = $_POST['id_token'];

    // Initialize Google Client
    $client = new Google\Client();
    $client->setClientId('YOUR_CLIENT_ID'); // Replace with your Client ID

    try {
        $payload = $client->verifyIdToken($id_token);
        if ($payload) {
            $user_id = $payload['sub'];
            $email = $payload['email'];
            $name = $payload['name'];

            // Check if user exists in the database
            $pdo = new PDO('mysql:host=localhost;dbname=your_database', 'your_user', 'your_password');
            $stmt = $pdo->prepare('SELECT * FROM users WHERE google_id = ?');
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();

            if (!$user) {
                // New user, insert into database
                $stmt = $pdo->prepare('INSERT INTO users (google_id, name, email) VALUES (?, ?, ?)');
                $stmt->execute([$user_id, $name, $email]);
            }

            // Start a session and log in the user
            session_start();
            $_SESSION['user'] = [
                'id' => $user_id,
                'name' => $name,
                'email' => $email,
            ];

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid ID token']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
