<?php
require_once("../utils/db.php");
require_once("../utils/cors.php");
require_once("../vendor/autoload.php");
require_once("../utils/allowedMethods.php");
require_once("../utils/authenticateUser.php");
allowedMethods(['GET', 'OPTIONS']);
header("Content-Type: application/json");
header("Authorization: Bearer <access-token>");

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/..');
$dotenv->load();

$domainUrl = $_ENV['DOMAIN_URL'];

$userId = authenticateUser();  

if ($userId === null) {
    http_response_code(401); 
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

try {
    // SQL query to fetch books and authors for the logged-in user
    $sqlUsersBooks = "
        SELECT DISTINCT
            b.id AS book_id,
            b.title,
            b.image,
            GROUP_CONCAT(DISTINCT CONCAT(a.id, '|', a.name)) AS authors
        FROM 
            user_books ub
        JOIN 
            books b ON ub.book_id = b.id
        LEFT JOIN 
            book_authors ba ON b.id = ba.book_id
        LEFT JOIN 
            authors a ON ba.author_id = a.id
        WHERE 
            ub.user_id = :user_id
        GROUP BY 
            b.id
    ";

    $stmt = $conn->prepare($sqlUsersBooks);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();

    $booksData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the final result
    $books = [];
    foreach ($booksData as $book) {
        $bookId = $book['book_id'];

        // Split the authors field into an array of "id|name" pairs
        $authors = !empty($book['authors']) ? explode(',', $book['authors']) : [];

        $books[] = [
            'title' => $book['title'],
            'authors' => array_map(function ($author) {
                list($id, $name) = explode('|', $author);
                return [
                    'name' => $name,
                    'url' => "http://localhost/bookreview/authors/" . urlencode($id)
                ];
            }, $authors),
            'image' => $book['image'],
            'url' => "$domainUrl/books/" . urlencode($bookId),
        ];
    }

    

    http_response_code(200);
    echo json_encode($books);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to fetch books: " . $e->getMessage()]);
}
