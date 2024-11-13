<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:4200"); // Allow requests from your React/Angular app
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Allow the necessary HTTP methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow specific headers
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../db/connect_mysql.php';
require_once './jwt_file.php'; // Include your JWT connection file
require_once '../db/connect_redis.php'; // Include your Redis connection file

// Read and decode JSON data
$data = json_decode(file_get_contents('php://input'), true);

$email = $data['email'];
$password = $data['password'];

// Prepare response array
$response = [];

// Retrieve the stored hash, password hash, and salt from the database for the provided email
$stmt = $mysqli->prepare("SELECT hash, password, salt, name FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($storedHash, $storedPassword, $storedSalt, $name);
$stmt->fetch();
$stmt->close();

if ($storedHash) {
    // Calculate the hash with the stored salt
    $calculatedPasswordHash = hash("sha512", $storedSalt . $password);

    // Compare the hashes
    if (hash_equals($calculatedPasswordHash, $storedPassword)) {
        // Create JWT payload
        $payload = [
            'iat' => time(), // Issued at
            'exp' => time() + 3600, // Expiration time (1 hour)
            'email' => $email,
            'hash' => $storedHash,
            'name' => $name
        ];

        // Generate JWT using the function from your included file
        $authToken = JWTHandler::generateJWT($payload); // Function to generate JWT

        // Store user details in Redis using HSET under the key 'userdetails'
        $redisKey = 'userdetails'; // Key to store user details
        $redis->hSet($redisKey, $authToken, json_encode([
            'email' => $email,
            'name' => $name,
            'hash' => $storedHash
        ]));

        // Set expiration for the authToken in Redis (optional, based on your use case)
        $redis->expire($redisKey, 3600); // Expire the hash key in 1 hour (3600 seconds)

        // Prepare successful response
        $response['success'] = true;
        $response['message'] = 'Login successful!';
        $response['authToken'] = $authToken; // Include JWT in response
        $response['hash'] = $storedHash;
        $response['name'] = $name;
    } else {
        $response['success'] = false;
        $response['message'] = 'Invalid email or password!';
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Invalid email or password!';
}

$mysqli->close();

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
