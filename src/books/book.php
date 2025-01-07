<?php
/**
 * @OA\Get(
 *     path="/books/{id}",
 *     summary="Get details of a specific book by ID",
 *     description="This endpoint retrieves detailed information about a specific book, including authors, genres, ratings, and reviews.",
 *     tags={"Books"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="The unique ID of the book to retrieve.",
 *         @OA\Schema(
 *             type="integer",
 *             example=1
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Details of the book, including authors, ratings, genres, and reviews.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="id",
 *                 type="integer",
 *                 description="The unique ID of the book."
 *             ),
 *             @OA\Property(
 *                 property="title",
 *                 type="string",
 *                 description="The title of the book."
 *             ),
 *             @OA\Property(
 *                 property="authors",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(
 *                         property="name",
 *                         type="string",
 *                         description="Author's name."
 *                     ),
 *                     @OA\Property(
 *                         property="url",
 *                         type="string",
 *                         description="Author's URL."
 *                     )
 *                 ),
 *                 description="List of authors of the book."
 *             ),
 *             @OA\Property(
 *                 property="first_published",
 *                 type="string",
 *                 description="The date the book was first published."
 *             ),
 *             @OA\Property(
 *                 property="pages",
 *                 type="integer",
 *                 description="The total number of pages in the book."
 *             ),
 *             @OA\Property(
 *                 property="image",
 *                 type="string",
 *                 description="The URL of the book's cover image."
 *             ),
 *             @OA\Property(
 *                 property="average_rating",
 *                 type="number",
 *                 format="float",
 *                 description="The average rating of the book."
 *             ),
 *             @OA\Property(
 *                 property="rating_count",
 *                 type="integer",
 *                 description="The number of ratings the book has received."
 *             ),
 *             @OA\Property(
 *                 property="genres",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(
 *                         property="name",
 *                         type="string",
 *                         description="The genre of the book."
 *                     ),
 *                     @OA\Property(
 *                         property="url",
 *                         type="string",
 *                         description="The URL of the genre."
 *                     )
 *                 ),
 *                 description="List of genres associated with the book."
 *             ),
 *             @OA\Property(
 *                 property="description",
 *                 type="string",
 *                 description="A description of the book."
 *             ),
 *             @OA\Property(
 *                 property="reviews",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(
 *                         property="id",
 *                         type="integer",
 *                         description="The unique ID of the review."
 *                     ),
 *                     @OA\Property(
 *                         property="review",
 *                         type="string",
 *                         description="The review text."
 *                     ),
 *                     @OA\Property(
 *                         property="submitted_by",
 *                         type="object",
 *                         @OA\Property(
 *                             property="name",
 *                             type="string",
 *                             description="Name of the user who submitted the review."
 *                         ),
 *                         @OA\Property(
 *                             property="url",
 *                             type="string",
 *                             description="URL to the user's profile."
 *                         )
 *                     ),
 *                     @OA\Property(
 *                         property="created_at",
 *                         type="string",
 *                         format="date-time",
 *                         description="The timestamp when the review was created."
 *                     ),
 *                     @OA\Property(
 *                         property="updated_at",
 *                         type="string",
 *                         format="date-time",
 *                         description="The timestamp when the review was last updated."
 *                     )
 *                 ),
 *                 description="List of reviews for the book."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request, missing book ID.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 description="Error message explaining the invalid request."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Book not found.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 description="Error message explaining that the book was not found."
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
    'average_rating' => isset($bookData['avg_rating']) && is_numeric($bookData['avg_rating'])
        ? round($bookData['avg_rating'], 2)
        : null,
    'rating_count' => $bookData['rating_count'],
    'genres' => $genresLookup,
    'description' => $bookData['description'],
    'reviews' => $reviewsLookup
];

echo json_encode($book, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

