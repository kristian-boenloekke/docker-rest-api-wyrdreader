<?php

require_once("../utils/db.php");
require_once("../utils/cors.php");
require_once("../vendor/autoload.php");
require_once("../utils/allowedMethods.php");
allowedMethods(['GET', 'OPTIONS']);
header('Content-Type: application/json');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/..');
$dotenv->load();

$domainUrl = $_ENV['DOMAIN_URL'];

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
if (!$id) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Author ID is required.']);
    exit;
}

$sql = "
    SELECT 
        a.id,
        a.name,
        GROUP_CONCAT(CONCAT(COALESCE(b.id, ''), '|', COALESCE(b.title, ''), '|', COALESCE(b.image, ''))) AS books
    FROM authors a
    LEFT JOIN book_authors ba ON a.id = ba.author_id
    LEFT JOIN books b ON ba.book_id = b.id
    WHERE a.id = :id
    GROUP BY a.id
";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$authorData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$authorData) {
    http_response_code(404); // Not Found
    echo json_encode(['error' => 'Author not found.']);
    exit;
}

$author = [
    'id' => $authorData['id'],
    'name' => $authorData['name'],
    'books' => array_map(function ($book) {
        // Split the book string by '|' into id, title, and image
        list($id, $title, $image) = explode('|', $book); 
        return [
            'title' => $title,
            'image' => $image,
            'url' => "http://localhost/bookreview/books/" . urlencode($id)  // Book URL
        ];
    }, explode(',', $authorData['books'])) // Convert books string into an array and process each item
];

echo json_encode($author);
