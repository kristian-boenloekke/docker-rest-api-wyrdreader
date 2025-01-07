<?php
/**
 * @OA\Tag(
 *     name="Ratings",
 *     description="Endpoints related to rating books by users."
 * )
 */

/**
 * @OA\Post(
 *     path="/rate/",  
 *     summary="Submit or update a rating for a book",
 *     description="Submit a rating (1-5) for a specific book, or update an existing rating if the user has already rated the book.",
 *     tags={"Ratings"},
 *     security={
 *         {"BearerAuth": {}}
 *     },
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"bookId", "rating"},
 *             @OA\Property(property="bookId", type="integer", description="ID of the book being rated"),
 *             @OA\Property(property="rating", type="integer", description="Rating for the book (between 1 and 5)")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Rating updated successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", description="Success message")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Rating submitted successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", description="Success message")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request, missing or invalid parameters",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", description="Error message")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized access - Invalid or missing token"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error"
 *     )
 * )
 */
require_once("../utils/db.php"); 
require_once("../utils/cors.php");
require_once("../utils/authenticateUser.php");
require_once("../utils/allowedMethods.php");
allowedMethods(['POST', 'PATCH', 'OPTIONS']);
header('Content-Type: application/json');
header('Authorization: Bearer <access-token>');


$userId = authenticateUser();  

if ($userId === null) {
    http_response_code(401); 
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

// Get the raw POST data (the PATCH request body)
$requestData = json_decode(file_get_contents("php://input"), true);

// Check if book_id and rating are set in the JSON payload
if (!isset($requestData['bookId']) || !isset($requestData['rating'])) {
    http_response_code(400);  // Bad request
    echo json_encode(["error" => "Missing bookId or rating"]);
    exit;
}

$bookId = $requestData['bookId'];
$rating = $requestData['rating'];

// Validate rating value (should be between 1 and 5)
if ($rating < 1 || $rating > 5) {
    http_response_code(400);  // Bad request
    echo json_encode(["error" => "Rating must be between 1 and 5"]);
    exit;
}

// Check if the user has already rated this book
$stmt = $conn->prepare("SELECT * FROM book_ratings WHERE user_id = :userId AND book_id = :bookId");
$stmt->bindParam(":userId", $userId);
$stmt->bindParam(":bookId", $bookId);
$stmt->execute();

// If a rating exists, update it
if ($stmt->rowCount() > 0) {
    // Update the rating
    $updateStmt = $conn->prepare("UPDATE book_ratings SET rating = :rating WHERE user_id = :userId AND book_id = :bookId");
    $updateStmt->bindParam(":rating", $rating);
    $updateStmt->bindParam(":userId", $userId);
    $updateStmt->bindParam(":bookId", $bookId);

    try {
        $updateStmt->execute();
        http_response_code(200);  // Success
        echo json_encode(["message" => "Rating updated successfully"]);
    } catch (PDOException $e) {
        http_response_code(500);  // Server error
        echo json_encode(["error" => "Failed to update rating: " . $e->getMessage()]);
    }
} else {
    // If no existing rating, insert a new rating
    $insertStmt = $conn->prepare("INSERT INTO book_ratings (user_id, book_id, rating) VALUES (:userId, :bookId, :rating)");
    $insertStmt->bindParam(":userId", $userId);
    $insertStmt->bindParam(":bookId", $bookId);
    $insertStmt->bindParam(":rating", $rating);

    try {
        $insertStmt->execute();
        http_response_code(201);  // Created (new rating added)
        echo json_encode(["message" => "Rating submitted successfully"]);
    } catch (PDOException $e) {
        http_response_code(500);  // Server error
        echo json_encode(["error" => "Failed to submit rating: " . $e->getMessage()]);
    }
}
?>
