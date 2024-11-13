<?php
// Start output buffering to prevent any accidental output
ob_start();

error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:4200"); // Allow requests from your React/Angular app
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Allow the necessary HTTP methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow specific headers

// storeUserDetails.php
require_once '../db/connect_mongo.php'; // MongoDB connection file
require_once './checkAuthToken.php';      // Auth token verification function

// Decode incoming JSON data
$data = json_decode(file_get_contents('php://input'), true);

// Prepare response array
$response = [];

// Step 1: Verify the auth token
$authCheck = checkAuthToken();
if (!$authCheck['success']) {
    // If token is invalid or expired, return error response
    $response['success'] = false;
    $response['message'] = $authCheck['message'];
    echo json_encode($response);
    exit;
}

// Step 2: Extract hash and other data from the request
$hash = $data['hash'] ?? null;
$age = $data['age'] ?? null;
$dob = $data['dob'] ?? null;
$contact = $data['contact'] ?? null;

// Ensure all required fields are present
if (!$hash || !$age || !$dob || !$contact) {
    $response['success'] = false;
    $response['message'] = 'All fields (hash, age, dob, contact) are required!';
    $response['hash'] = $hash;
    $response['age'] = $age;
    $response['dob'] = $dob;
    $response['contact'] = $contact;
    
    // Clear buffer and send JSON response
    ob_end_clean();
    echo json_encode($response);
    exit;
}

// Function to update profile data in MongoDB
function updateProfileData($db, $hash, $age, $dob, $contact)
{
    $collection = $db->selectCollection('userDetails');
    $result = $collection->updateOne(
        ['hash' => $hash],
        ['$set' => [
            'age' => $age,
            'dob' => $dob,
            'contact' => $contact
        ]],
        ['upsert' => true]
    );
    return $result;
}

// Step 3: Call function to update data in MongoDB
$updateResult = updateProfileData($db, $hash, $age, $dob, $contact);

if ($updateResult->getMatchedCount() > 0 || $updateResult->getUpsertedCount() > 0) {
    $response['success'] = true;
    $response['message'] = 'Profile updated successfully!';
} else {
    $response['success'] = false;
    $response['message'] = 'Failed to update profile!';
}

// Clear the output buffer and send JSON response
ob_end_clean();
echo json_encode($response);
?>
