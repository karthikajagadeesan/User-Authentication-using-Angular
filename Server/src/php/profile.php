<?php
ob_start();

error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:4200"); // Allow requests from your React/Angular app
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Allow the necessary HTTP methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow specific headers


require_once '../db/connect_mongo.php'; // MongoDB connection file
require_once '../db/connect_redis.php'; // Redis connection file
require_once './checkAuthToken.php'; // Auth token verification function


// Decode incoming JSON data
$data = json_decode(file_get_contents('php://input'), true);

// Step 1: Verify the auth token
$authCheck = checkAuthToken();
if (!$authCheck['success']) {
    $response['success'] = false;
    $response['message'] = $authCheck['message'];
    echo json_encode($response);
    exit;
}

// Step 2: Extract hash from the request
$hash = $data['hash'] ?? null;

// Ensure the hash is present
if (!$hash) {
    $response['success'] = false;
    $response['message'] = 'Hash is required!';
    echo json_encode($response);
    exit;
}

// Function to get profile data
function getProfileData($hash)
{
    global $db;
    $collection = $db->selectCollection('userDetails');
    $profile = $collection->findOne(['hash' => $hash]);
    return $profile;
}

$response = [];

// Fetching profile data
if ($hash) {
    $profile = getProfileData($hash);
    if ($profile) {
        $response['success'] = true;
        $response['profile'] = $profile;
    } else {
        $response['success'] = false;
        $response['message'] = 'Profile not found. Please update your profile.';
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Please login again.';
}

ob_end_clean();
echo json_encode($response);
