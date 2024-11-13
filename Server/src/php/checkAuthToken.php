<?php
// checkAuthToken.php
require_once '../db/connect_redis.php'; // Connects to Redis

function checkAuthToken()
{
    // Retrieve Authorization header
    $headers = apache_request_headers();
    if (!isset($headers['Authorization'])) {
        return ['success' => false, 'message' => 'Auth token missing'];
    }

    // Extract Bearer token
    $authHeader = $headers['Authorization'];
    if (strpos($authHeader, 'Bearer ') !== 0) {
        return ['success' => false, 'message' => 'Invalid token format'];
    }

    $authToken = substr($authHeader, 7); // Remove 'Bearer ' prefix

    // Check if the auth token exists in Redis
    global $redis; // Assume Redis connection in connect_redis.php
    if ($redis->exists('userdetails') && $redis->hExists('userdetails', $authToken)) {
        return ['success' => true, 'authToken' => $authToken];
    } else {
        return ['success' => false, 'message' => 'Auth token expired'];
    }
}
