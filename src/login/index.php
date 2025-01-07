<?php
// $debug = [
//     'current_working_directory' => getcwd(),
//     'script_filename' => $_SERVER['SCRIPT_FILENAME'],
//     'document_root' => $_SERVER['DOCUMENT_ROOT'],
//     'attempted_path' => '../../vendor/autoload.php',
//     'realpath_result' => realpath('../../vendor/autoload.php'),
//     'file_exists' => file_exists('../../vendor/autoload.php'),
//     'directory_contents' => scandir('../..')
// ];

// echo json_encode($debug, JSON_PRETTY_PRINT);
// exit;

require_once("../utils/db.php");
require_once("../utils/cors.php");
require __DIR__ . '/../vendor/autoload.php';
require_once('../utils/allowedMethods.php');
allowedMethods(['POST','OPTIONS']);
header('Content-Type: application/json');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/..');
$dotenv->load();

use Firebase\JWT\JWT;

if (!empty($_POST)) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(":email", $_POST["email"]);
    
    $stmt->execute();
    $results = $stmt->fetchAll();

    if (!empty($results)) {
        if (password_verify($_POST["password"] . $results[0]["salt"], $results[0]['password'])) {
            // User is authenticated
            $userId = $results[0]['id'];

            // Create JWT payload
            $payload = [
                'iss' => 'your-domain.com',      // Issuer
                'aud' => 'your-domain.com',      // Audience
                'iat' => time(),                 // Issued at
                'exp' => time() + 30 * 24 * 60 * 60,        // Expiry (30 days)
                'sub' => $userId,                // Subject (User ID)
            ];

            // Secret key for signing the token
            $secretKey = $_ENV['SECRET_KEY'];
            
            // Generate JWT
            $jwt = JWT::encode($payload, $secretKey, 'HS256');

            // Return the token
            echo json_encode([
                'token' => $jwt
            ]);
        } else {
            // Password is incorrect
            http_response_code(400);
            echo json_encode(["error" => "Invalid username or password"]);
        }
    } else {
        // No user found
        http_response_code(400);
        echo json_encode(["error" => "Invalid username or password"]);
    }
}
