<?php
// connect_mongo.php
require_once '../../vendor/autoload.php'; // Adjust path as needed

// Suppress deprecated warnings temporarily
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

try {
    // Connect to MongoDB using MongoDB\Client
    $mongo = new MongoDB\Client("mongodb+srv://karthikajagadeesan11:karthikajagadeesan@cluster0.bh6vi.mongodb.net/");
    $db = $mongo->selectDatabase('Guvi_Task'); // Replace with your database name

    // Optional: you can set a read preference as well
    // $readPreference = new MongoDB\Driver\ReadPreference('primary');
    // $db->withOptions(['readPreference' => $readPreference]);

} catch (MongoDB\Exception\Exception $e) {
    // Log the error instead of echoing it
    error_log("MongoDB connection error: " . $e->getMessage());

    // Return a JSON response with an error message
    header('Content-Type: application/json');
    echo json_encode([
        "success" => false,
        "message" => "Could not connect to MongoDB"
    ]);
    exit();
}

// If connection is successful, you can return a JSON response
// header('Content-Type: application/json');
// echo json_encode(["success" => true, "message" => "Connected to MongoDB Atlas successfully!"]);
