<?php
require_once("../../utils/db.php"); 
require_once("../../utils/cors.php");
require_once("../../utils/authenticateUser.php");
require_once("../../utils/allowedMethods.php");
allowedMethods(['PATCH', 'OPTIONS']);
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

if (!isset($requestData['reviewId'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing 'reviewId' in JSON input"]);
    exit;
}

$review = $requestData['review'];
$reviewId = $requestData['reviewId'];

// Fetch the review from the database to ensure it exists and belongs to the user
$stmt = $conn->prepare("SELECT * FROM reviews WHERE id = ? AND user_id = ?");
$stmt->execute([$reviewId, $userId]);
$existingReview = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$existingReview) {
    http_response_code(404); // Not Found
    echo json_encode(["error" => "Review not found or not authorized to update"]);
    exit;
}

// Update the review in the database
$updateStmt = $conn->prepare("
    UPDATE reviews 
    SET review = ?, updated_at = NOW()
    WHERE id = ? AND user_id = ?
");
$success = $updateStmt->execute([$review, $reviewId, $userId]);

if ($success) {
    http_response_code(200);
    echo json_encode(["message" => "Review updated successfully"]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Failed to update review"]);
}

?>
