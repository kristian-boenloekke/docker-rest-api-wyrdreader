<?php
require_once("../../utils/db.php"); 
require_once("../../utils/cors.php");
require_once("../../utils/authenticateUser.php");
require_once("../../utils/allowedMethods.php");
allowedMethods(['POST', 'OPTIONS']);
header('Content-Type: application/json');
header('Authorization: Bearer <access-token>');

// if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
//     header('Allow: POST, OPTIONS'); 
//     http_response_code(200); 
//     exit;
// }

// if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
//     header('Allow: POST, OPTIONS');
//     http_response_code(405); 
//     echo json_encode(["error" => "Only POST method is allowed"]);
//     exit;
// }

$userId = authenticateUser();  

if ($userId === null) {
    http_response_code(401); 
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['bookId'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing bookId']);
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

    // Check if user_id and book_id already exist in user_books
    $stmt = $conn->prepare("SELECT * FROM user_books WHERE user_id = :userId AND book_id = :bookId");
    $stmt->bindParam(":userId", $userId);
    $stmt->bindParam(":bookId", $bookId);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        http_response_code(409);
        echo json_encode(["error" => "This book is already in the user's list"]);
        exit;
    }

    // Insert into user_books
    $stmt = $conn->prepare("INSERT INTO user_books (user_id, book_id) VALUES (:userId, :bookId)");
    $stmt->bindParam(":userId", $userId);
    $stmt->bindParam(":bookId", $bookId);

    try {
        $stmt->execute();
        http_response_code(201);
        echo json_encode(["success" => "Book added to user's list"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => 'Failed to add book: ' . $e->getMessage()]);
    }
}
