<?php
/**
 * @OA\Get(
 *     path="/genres/{id}",
 *     summary="Get details of a specific genre and its books",
 *     description="This endpoint retrieves details of a specific genre along with its associated books, including book authors, images, and pagination for the books.",
 *     tags={"Genres"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="The unique ID of the genre to retrieve.",
 *         @OA\Schema(
 *             type="integer",
 *             example=1
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="offset",
 *         in="query",
 *         description="The offset for pagination.",
 *         @OA\Schema(
 *             type="integer",
 *             default=0
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="limit",
 *         in="query",
 *         description="The limit for the number of books to return.",
 *         @OA\Schema(
 *             type="integer",
 *             default=20
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Details of the genre along with its associated books.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="id",
 *                 type="integer",
 *                 description="The unique ID of the genre."
 *             ),
 *             @OA\Property(
 *                 property="name",
 *                 type="string",
 *                 description="The name of the genre."
 *             ),
 *             @OA\Property(
 *                 property="description",
 *                 type="string",
 *                 description="A description of the genre."
 *             ),
 *             @OA\Property(
 *                 property="books",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(
 *                         property="id",
 *                         type="integer",
 *                         description="The unique ID of the book."
 *                     ),
 *                     @OA\Property(
 *                         property="title",
 *                         type="string",
 *                         description="The title of the book."
 *                     ),
 *                     @OA\Property(
 *                         property="url",
 *                         type="string",
 *                         description="The URL of the book."
 *                     ),
 *                     @OA\Property(
 *                         property="authors",
 *                         type="array",
 *                         @OA\Items(
 *                             type="object",
 *                             @OA\Property(
 *                                 property="name",
 *                                 type="string",
 *                                 description="The name of the author."
 *                             ),
 *                             @OA\Property(
 *                                 property="url",
 *                                 type="string",
 *                                 description="The URL to the author's details."
 *                             )
 *                         )
 *                     ),
 *                     @OA\Property(
 *                         property="image",
 *                         type="string",
 *                         description="The image URL of the book."
 *                     )
 *                 ),
 *                 description="A list of books belonging to this genre."
 *             ),
 *             @OA\Property(
 *                 property="pagination",
 *                 type="object",
 *                 @OA\Property(
 *                     property="next",
 *                     type="string",
 *                     description="URL to the next page of results, if available."
 *                 ),
 *                 @OA\Property(
 *                     property="previous",
 *                     type="string",
 *                     description="URL to the previous page of results, if available."
 *                 )
 *             ),
 *             @OA\Property(
 *                 property="total_books",
 *                 type="integer",
 *                 description="The total number of books in this genre."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad Request, missing or invalid genre ID.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 description="Error message."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Genre not found.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 description="Error message."
 *             )
 *         )
 *     )
 * )
 */
require_once("../utils/db.php");
require_once("../utils/cors.php");
require_once("../vendor/autoload.php");
require_once("../utils/allowedMethods.php");
allowedMethods(['GET', 'OPTIONS']);
header("Content-Type: application/json");

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/..');
$dotenv->load();

$domainUrl = $_ENV['DOMAIN_URL'];

// Validate and get the genre ID
$genreId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;

// Ensure a valid genre ID is provided
if (!$genreId) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Genre ID is required.']);
    exit;
}

// Fetch genre details
$sqlGenre = "SELECT id, name, description FROM genres WHERE id = :id";
$stmt = $conn->prepare($sqlGenre);
$stmt->bindParam(":id", $genreId, PDO::PARAM_INT);
$stmt->execute();
$genre = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$genre) {
    http_response_code(404); // Not Found
    echo json_encode(['error' => 'Genre not found.']);
    exit;
}

// Fetch books in this genre with authors
$sqlGenreBooks = "
    SELECT 
        b.id AS book_id,
        b.title,
        b.image
       
    FROM books b
    JOIN book_genres bg ON b.id = bg.book_id
  
    WHERE bg.genre_id = :genreId
    GROUP BY b.id
    LIMIT :limit OFFSET :offset
";
$stmt = $conn->prepare($sqlGenreBooks);
$stmt->bindParam(":genreId", $genreId, PDO::PARAM_INT);
$stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
$stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
$stmt->execute();
$booksData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Format books with authors foreach
$books = [];
foreach ($booksData as $book) {
    // Fetch authors for the current book
    $stmt = $conn->prepare("
        SELECT 
            a.id,
            a.name
        FROM 
            authors a
        JOIN 
            book_authors ba ON a.id = ba.author_id
        WHERE 
            ba.book_id = :bookId
    ");
    $stmt->bindParam(':bookId', $book['book_id'], PDO::PARAM_INT);
    $stmt->execute();
    $authors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format authors for the current book
    $authorsLookup = [];
    foreach ($authors as $author) {
        $authorsLookup[] = [
            'name' => $author['name'],
            'url' => "$domainUrl/authors/" . urlencode($author['id']),
        ];
    }

    // Add the book with its authors to the books array
    $books[] = [
        'id' => $book['book_id'],
        'title' => $book['title'],
        'url' => "$domainUrl/books/" . urlencode($book["book_id"]),
        'authors' => $authorsLookup,
        'image' => $book['image'],
    ];
}

// Calculate pagination
$sqlBookCount = "
    SELECT COUNT(DISTINCT b.id) AS total_books
    FROM books b
    JOIN book_genres bg ON b.id = bg.book_id
    WHERE bg.genre_id = :genreId
";
$stmt = $conn->prepare($sqlBookCount);
$stmt->bindParam(":genreId", $genreId, PDO::PARAM_INT);
$stmt->execute();
$totalBooks = $stmt->fetch(PDO::FETCH_ASSOC)['total_books'];

$nextOffset = $offset + $limit;
$previousOffset = $offset - $limit;

$pagination = [
    'next' => ($nextOffset < $totalBooks) ? "$domainUrl/genres/$genreId?offset=$nextOffset&limit=$limit" : null,
    'previous' => ($previousOffset >= 0) ? "$domainUrl/genres/$genreId?offset=$previousOffset&limit=$limit" : null
];

// Final result
$result = [
    'id' => $genre['id'],
    'name' => $genre['name'],
    'description' => $genre['description'],
    'books' => $books,
    'pagination' => $pagination,
    'total_books' => $totalBooks
];

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
