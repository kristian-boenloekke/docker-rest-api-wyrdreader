<?php
require_once("../utils/db.php");
require_once("../utils/cors.php");
require_once("../vendor/autoload.php");
require_once("../utils/allowedMethods.php");
allowedMethods(['GET', 'OPTIONS']);
header("Content-Type: application/json");

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/..');
$dotenv->load();

$domainUrl = $_ENV['DOMAIN_URL'];

// Get offset and limit from the query parameters, with default values
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;


// Ensure the offset and limit are non-negative and positive
$offset = max($offset, 0);
$limit = max($limit, 1);

// Get total book count for pagination info
$sqlBookCount = "
    SELECT COUNT(*) AS book_count
    FROM books
";

$stmt = $conn->prepare($sqlBookCount);
$stmt->execute();
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$bookCount = $stmt->fetch()['book_count'];

// Get books with pagination (limit and offset)
$sqlBooks = "
    SELECT
        b.id,
        b.title,
        b.image,
        AVG(br.rating) AS avg_rating,
        COUNT(br.rating) AS rating_count
    FROM 
        books b
    LEFT JOIN
        book_ratings br ON b.id = br.book_id
    GROUP BY 
        b.id
    LIMIT :limit OFFSET :offset
";

$stmt = $conn->prepare($sqlBooks);
$stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
$stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
$stmt->execute();
$booksData = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sqlAuthors = "
    SELECT 
        a.id, 
        a.name, 
        ba.book_id 
    FROM 
        authors a
    JOIN 
        book_authors ba ON a.id = ba.author_id
";

$stmt = $conn->prepare($sqlAuthors);
$stmt->execute();
$authorsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

$authors = [];
foreach ($authorsData as $author) {
    $authors[$author["book_id"]][] = [
        'name' => $author['name'],
        'url' => "$domainUrl/authors/" . urlencode($author['id'])
        ];
}

// Format the final result
$books = [];
foreach ($booksData as $book) {

    $books[] = [
        'id' => $book['id'],
        'title' => $book['title'],
        'url' => "$domainUrl/books/" . urlencode($book["id"]),
        'authors' => $authors[$book['id']],
        'image' => $book['image'],
        'average_rating' => isset($book['avg_rating']) && is_numeric($book['avg_rating'])
        ? round($book['avg_rating'], 2)
        : null,
        'rating_count' => $book['rating_count']
    ];
}

// Pagination metadata
$totalPages = ceil($bookCount / $limit);  // Calculate the total number of pages

// Calculate next and previous offset links
$nextOffset = $offset + $limit;
$previousOffset = $offset - $limit;

$pagination = [
    'next' => ($nextOffset < $bookCount) ? "$domainUrl/books?offset=$nextOffset&limit=$limit" : null,
    'previous' => ($previousOffset >= 0) ? "$domainUrl/books?offset=$previousOffset&limit=$limit" : null
];

$result = [
    'count' => $bookCount,
    'next' => $pagination['next'],
    'previous' => $pagination['previous'],
    'books' => $books
];

echo json_encode($result);
