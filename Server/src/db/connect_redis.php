<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
   
} catch (RedisException $e) {
    echo "Could not connect to Redis: " . $e->getMessage();
    error_log("Redis connection error: " . $e->getMessage());
}
?>