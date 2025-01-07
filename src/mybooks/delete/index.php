<?php
require_once("../../utils/db.php"); 
require_once("../../utils/cors.php");
require_once("../../utils/authenticateUser.php");
require_once("../../utils/allowedMethods.php");
allowedMethods(['DELETE', 'OPTIONS']);
header('Content-Type: application/json');
header('Authorization: Bearer <access-token>');

$userId = authenticateUser();  

if ($userId === null) {
    http_response_code(401); 
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);

    if ($data === null) { // Check if decoding failed
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON payload']);
        exit;
    }
    
    if (!isset($data['bookId']) || !is_numeric($data['bookId'])) { // Check if book_id is present and numeric
        http_response_code(400);
        echo json_encode(['error' => 'Invalid or missing bookId']);
        exit;
    }

    $bookId = $data['bookId'];

    // Check if book exists
    $stmt = $conn->prepare("SELECT * FROM books WHERE id = :bookId");
    $stmt->bindParam(":bookId", $bookId);
    $stmt->execute();
    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(["error" => "Book not found"]);
        exit;
    }

    // Check if user_id and book_id exist in user_books
    $stmt = $conn->prepare("SELECT * FROM user_books WHERE user_id = :userId AND book_id = :bookId");
    $stmt->bindParam(":userId", $userId);
    $stmt->bindParam(":bookId", $bookId);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(["error" => "This book is not in the user's list"]);
        exit;
    }

    // Delete from user_books
    $stmt = $conn->prepare("DELETE FROM user_books WHERE user_id = :userId AND book_id = :bookId");
    $stmt->bindParam(":userId", $userId);
    $stmt->bindParam(":bookId", $bookId);

    try {
        $stmt->execute();
        http_response_code(200); 
        echo json_encode(["success" => "Book removed from user's list"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => 'Failed to remove book: ' . $e->getMessage()]);
    }
}
