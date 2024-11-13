<?php
require_once '../../vendor/autoload.php'; // Include Composer's autoloader
use \Firebase\JWT\JWT;
use \Firebase\JWT\ExpiredException;

class JWTHandler
{
    private static $secretKey = 'karthika'; // Replace with your actual secret key
    private static $algorithm = 'HS512'; // Algorithm for JWT

    // Function to generate JWT
    public static function generateJWT($payload)
    {
        $payload['iat'] = time(); // Issued at
        $payload['exp'] = time() + 3600; // Expiration time (1 hour)

        return JWT::encode($payload, self::$secretKey, self::$algorithm);
    }

    // Function to decode JWT
    public static function decodeJWT($token)
    {
        try {
            return JWT::decode($token, self::$secretKey, [self::$algorithm]);
        } catch (ExpiredException $e) {
            return ['error' => 'Token has expired'];
        } catch (Exception $e) {
            return ['error' => 'Invalid token'];
        }
    }
}
