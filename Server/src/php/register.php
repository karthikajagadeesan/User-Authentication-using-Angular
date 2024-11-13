<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:4200"); // Allow requests from your React/Angular app
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Allow the necessary HTTP methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow specific headers
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../db/connect_mysql.php';

// Handle preflight OPTIONS request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Read and decode JSON data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['name'], $data['email'], $data['password'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input!']);
    exit;
}

$salt = bin2hex(random_bytes(16)); // Generate a random 16-byte salt

// Retrieve fields from JSON
$name = $data['name'];
$email = $data['email'];
$hash = hash("sha512", $salt . $email);
$password = hash("sha512", $salt . $data['password']);

// Prepare a statement to check if the email already exists
$checkStmt = $mysqli->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
$checkStmt->bind_param("s", $email);
$checkStmt->execute();
$checkStmt->bind_result($emailCount);
$checkStmt->fetch();
$checkStmt->close();

$response = [];
if ($emailCount > 0) {
    // Email already exists
    $response['success'] = false;
    $response['message'] = 'Email already registered!';
} else {
    // Proceed with registration if email is not found
    $stmt = $mysqli->prepare("INSERT INTO users (hash, name, email, password, salt) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $hash, $name, $email, $password, $salt);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Registration successful!';
    } else {
        $response['success'] = false;
        $response['message'] = 'Registration failed!';
    }

    $stmt->close();
}
$mysqli->close();

// Output the JSON response
echo json_encode($response);