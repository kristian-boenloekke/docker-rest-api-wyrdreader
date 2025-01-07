<?php
require_once("../../utils/db.php"); 
require_once("../../utils/cors.php");
require_once("../../utils/authenticateUser.php");
require_once("../../utils/allowedMethods.php");
allowedMethods(['POST', 'OPTIONS']);
header('Content-Type: application/json');
header('Authorization: Bearer <access-token>');

$userId = authenticateUser();  

if ($userId === null) {
    http_response_code(401); 
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

// Parse the entire request body as JSON
$requestData = json_decode(file_get_contents("php://input"), true);

if (!$requestData) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid JSON input"]);
    exit;
}

// Validate and retrieve the required fields
if (!isset($requestData['review']) || empty($requestData['review'])) {
    http_response_code(400);
    echo json_encode(["error" => "Review text is required"]);
    exit;
}

if (!isset($requestData['bookId'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing 'bookId' in JSON input"]);
    exit;
}

$review = $requestData['review'];
$bookId = $requestData['bookId'];

// Insert the review into the database
$insertStmt = $conn->prepare("
    INSERT INTO reviews (review, book_id, user_id)
    VALUES (?, ?, ?)
");
$success = $insertStmt->execute([$review, $bookId, $userId]);

if ($success) {
    http_response_code(201);
    echo json_encode(["message" => "Review submitted successfully"]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Failed to submit review"]);
}

?>
