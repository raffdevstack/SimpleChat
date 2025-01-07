<?php

require_once 'vendor/autoload.php';

header('Content-Type: application/json');

// Get the raw POST data
$rawData = file_get_contents('php://input');

// Decode the JSON
$data = json_decode($rawData, true);

// Check if token is provided
if (!isset($data['id_token'])) {
    echo json_encode(['error' => 'ID token missing']);
    http_response_code(400);
    exit;
}

$idToken = $data['id_token'];

// Verify the token using Google API client
require_once 'vendor/autoload.php';

$client = new Google_Client(['client_id' => '581284499083-tosthllpap9ovpam423m7003rg8ghs1c.apps.googleusercontent.com']);
try {
    $payload = $client->verifyIdToken($idToken);
    if ($payload) {
        echo json_encode(['success' => true, 'payload' => $payload]);
    } else {
        echo json_encode(['error' => 'Invalid ID token']);
        http_response_code(401);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
    http_response_code(500);
}
