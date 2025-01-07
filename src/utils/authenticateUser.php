<?php

require_once __DIR__."/../vendor/autoload.php"; // Adjust the path to your autoload.php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..'); // path to .env
$dotenv->load();

function authenticateUser() {
    // Get the Authorization header
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? null;

    if (!$authHeader) {
        http_response_code(401);
        echo json_encode(["error" => "Authorization header is missing"]);
        exit;
    }

    // Extract the token from the Bearer string
    if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $jwt = $matches[1];
    } else {
        http_response_code(401);
        echo json_encode(["error" => "Invalid Authorization header format"]);
        exit;
    }

    // Secret key used to sign the token during login
    $secretKey = $_ENV['SECRET_KEY'];

    try {
        // Decode the token
        $decoded = JWT::decode($jwt, new Key($secretKey, 'HS256'));
        $payload = (array)$decoded;

        // Extract the user ID (sub) from the token payload
        $userId = $payload['sub'] ?? null;

        if (!$userId) {
            http_response_code(401);
            echo json_encode(["error" => "Invalid token payload"]);
            exit;
        }

        // Return the user ID
        return $userId;
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(["error" => "Invalid or expired token", "details" => $e->getMessage()]);
        exit;
    }
}
?>
