<?php
/**
 * @OA\Post(
 *     path="/review/",  // Trailing slash included
 *     summary="Submit a new review",
 *     description="Allows authenticated users to submit a new review for a specific book.",
 *     tags={"Reviews"},
 *     security={
 *         {"BearerAuth": {}}
 *     },
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"review", "bookId"},
 *             @OA\Property(property="review", type="string", description="Review text for the book"),
 *             @OA\Property(property="bookId", type="integer", description="ID of the book being reviewed")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Review submitted successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", description="Success message")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request, invalid or missing parameters",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", description="Error message")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", description="Error message")
 *         )
 *     )
 * )
 * 
 * @OA\Patch(
 *     path="/review/",  // Trailing slash included
 *     summary="Update an existing review",
 *     description="Allows authenticated users to update their previously submitted review for a specific book.",
 *     tags={"Reviews"},
 *     security={
 *         {"BearerAuth": {}}
 *     },
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"review", "reviewId"},
 *             @OA\Property(property="review", type="string", description="Updated review text for the book"),
 *             @OA\Property(property="reviewId", type="integer", description="ID of the review to be updated")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Review updated successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", description="Success message")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request, invalid or missing parameters",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", description="Error message")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Review not found or not authorized to update",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", description="Error message")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", description="Error message")
 *         )
 *     )
 * )
 * 
 * @OA\Delete(
 *     path="/review/",  // Trailing slash included
 *     summary="Delete an existing review",
 *     description="Allows authenticated users to delete their previously submitted review for a specific book.",
 *     tags={"Reviews"},
 *     security={
 *         {"BearerAuth": {}}
 *     },
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"reviewId"},
 *             @OA\Property(property="reviewId", type="integer", description="ID of the review to be deleted")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Review deleted successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", description="Success message")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request, invalid or missing parameters",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", description="Error message")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Review not found or not authorized to delete",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", description="Error message")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", description="Error message")
 *         )
 *     )
 * )
 */
require_once("../utils/db.php");
require_once("../utils/cors.php");
require_once("../utils/authenticateUser.php");
require_once("../utils/allowedMethods.php");
allowedMethods(['POST', 'PATCH', 'DELETE', 'OPTIONS']);
header('Content-Type: application/json');
header('Authorization: Bearer <access-token>');

$userId = authenticateUser();

if ($userId === null) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod === 'POST') {
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
    $insertStmt = $conn->prepare("INSERT INTO reviews (review, book_id, user_id) VALUES (?, ?, ?)");
    $success = $insertStmt->execute([$review, $bookId, $userId]);

    if ($success) {
        http_response_code(201);
        echo json_encode(["message" => "Review submitted successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to submit review"]);
    }

}

if ($requestMethod === 'PATCH') {
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
        http_response_code(404); 
        echo json_encode(["error" => "Review not found or not authorized to update"]);
        exit;
    }

    // Update the review in the database
    $updateStmt = $conn->prepare("UPDATE reviews SET review = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
    $success = $updateStmt->execute([$review, $reviewId, $userId]);

    if ($success) {
        http_response_code(200);
        echo json_encode(["message" => "Review updated successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to update review"]);
    }

}

if ($requestMethod === 'DELETE') {
    $requestData = json_decode(file_get_contents("php://input"), true);

    if (!$requestData) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid JSON input"]);
        exit;
    }

    // Validate and retrieve the required fields
    if (!isset($requestData['reviewId'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing 'reviewId' in JSON input"]);
        exit;
    }

    $reviewId = $requestData['reviewId'];

    // Check if the review exists and belongs to the user
    $stmt = $conn->prepare("SELECT * FROM reviews WHERE id = ? AND user_id = ?");
    $stmt->execute([$reviewId, $userId]);
    $existingReview = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$existingReview) {
        http_response_code(404); // Not Found
        echo json_encode(["error" => "Review not found or not authorized to delete"]);
        exit;
    }

    // Delete the review from the database
    $deleteStmt = $conn->prepare("DELETE FROM reviews WHERE id = ? AND user_id = ?");
    $success = $deleteStmt->execute([$reviewId, $userId]);

    if ($success) {
        http_response_code(200);
        echo json_encode(["message" => "Review deleted successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to delete review"]);
    }

}