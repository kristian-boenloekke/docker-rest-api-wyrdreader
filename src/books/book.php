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

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$id) {
    http_response_code(400); 
    echo json_encode(['error' => 'Book ID is required.']);
    exit;
}

$sqlBookDetails = "
    SELECT DISTINCT
        b.id,
        b.title,
        b.first_published,
        b.pages,
        b.image,
        b.description,
        AVG(br.rating) AS avg_rating,
        COUNT(br.rating) AS rating_count
    FROM 
        books b
    LEFT JOIN
        book_ratings br ON b.id = br.book_id
    WHERE 
        b.id = :id
    GROUP BY 
        b.id
";

$stmt = $conn->prepare($sqlBookDetails);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$bookData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$bookData) {
    http_response_code(404); // Not Found
    echo json_encode(['error' => 'Book not found.']);
    exit;
}

// Book authors
$sqlAuthors = "
    SELECT 
        a.id,
        a.name
    FROM 
        authors a
    JOIN 
        book_authors ba ON a.id = ba.author_id
    WHERE 
        ba.book_id = :id
";

$stmt = $conn->prepare($sqlAuthors);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$authors = $stmt->fetchAll(PDO::FETCH_ASSOC);

$authorsLookup = [];
foreach ($authors as $author) {
    $authorsLookup[] = [
        'name' => $author['name'],
        'url' => "$domainUrl/authors/" . urlencode($author['id'])
        ];
}

// Book genres
$sqlGenres = "
    SELECT 
        bg.book_id,
        g.id AS genre_id,
        g.name AS genre_name
    FROM 
        book_genres bg
    LEFT JOIN 
        genres g ON bg.genre_id = g.id
    WHERE 
        bg.book_id = :id
";


$stmt = $conn->prepare($sqlGenres);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$genres = $stmt->fetchAll(PDO::FETCH_ASSOC);

$genresLookup = [];
foreach ($genres as $genre) {
    $genresLookup[] = [
        'name' => $genre['genre_name'],
        'url' => "$domainUrl/genres/" . urlencode($genre['genre_id'])        
    ];
}

// Book reviews
$sqlReviews = "
    SELECT r.id, r.review, r.created_at, r.updated_at, u.username, r.user_id
    FROM reviews r
    LEFT JOIN users u ON r.user_id = u.id
    WHERE r.book_id = :id
";

$stmt = $conn->prepare($sqlReviews);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Format reviews response
$reviewsLookup = [];
foreach ($reviews as $review) {
    $reviewsLookup[] = [
        'id' => $review['id'],
        'review' => $review['review'],
        'submitted_by' => [
            'name' => $review['username'], 
            'url' =>"$domainUrl/users/" . urlencode($review['user_id'])
        ],
        'created_at' => $review['created_at'],
        'updated_at' => $review['updated_at']
        
    ];
}

// Format the final response
$book = [
    'id' => $bookData['id'],
    'title' => $bookData['title'],
    'authors' => $authorsLookup,
    'first_published' => $bookData['first_published'],
    'pages' => $bookData['pages'],
    'image' => $bookData['image'],
    'average_rating' => round($bookData['avg_rating'], 2),
    'rating_count' => $bookData['rating_count'],
    'genres' => $genresLookup,
    'description' => $bookData['description'],
    'reviews' => $reviewsLookup
];

echo json_encode($book);
?>
