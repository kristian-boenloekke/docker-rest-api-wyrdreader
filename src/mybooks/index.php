<?php
/**
 * @OA\Tag(
 *     name="Books",
 *     description="Endpoints related to managing books for the authenticated user."
 * )
 */

/**
 * @OA\Get(
 *     path="/mybooks/",
 *     summary="Get the list of books for the authenticated user",
 *     description="Fetches the list of books associated with the authenticated user, along with their authors and details.",
 *     tags={"Books"},
 *     security={
 *         {"BearerAuth": {}}
 *     },
 *     @OA\Response(
 *         response=200,
 *         description="A list of books associated with the user",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="title", type="string", description="Book title"),
 *                 @OA\Property(property="authors", type="array", 
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="name", type="string", description="Author's name"),
 *                         @OA\Property(property="url", type="string", description="URL for the author")
 *                     )
 *                 ),
 *                 @OA\Property(property="image", type="string", description="Image URL of the book"),
 *                 @OA\Property(property="url", type="string", description="URL for the book")
 *             )
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

/**
 * @OA\Post(
 *     path="/mybooks/",
 *     summary="Add a book to the user's list",
 *     description="Adds a book to the authenticated user's personal list. Requires a valid book ID in the request body.",
 *     tags={"Books"},
 *     security={
 *         {"BearerAuth": {}}
 *     },
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"bookId"},
 *             @OA\Property(property="bookId", type="integer", description="ID of the book to be added")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Book successfully added to the user's list",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="string", description="Confirmation message")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Missing bookId or invalid data"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Book not found"
 *     ),
 *     @OA\Response(
 *         response=409,
 *         description="The book is already in the user's list"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error"
 *     )
 * )
 */

/**
 * @OA\Delete(
 *     path="/mybooks/",
 *     summary="Remove a book from the user's list",
 *     description="Removes a book from the authenticated user's personal list. Requires a valid book ID in the request body.",
 *     tags={"Books"},
 *     security={
 *         {"BearerAuth": {}}
 *     },
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"bookId"},
 *             @OA\Property(property="bookId", type="integer", description="ID of the book to be removed")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Book successfully removed from the user's list",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="string", description="Confirmation message")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid or missing bookId"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Book not found or not in user's list"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error"
 *     )
 * )
 */
require_once("../utils/db.php");
require_once("../utils/cors.php");
require_once("../vendor/autoload.php");
require_once("../utils/allowedMethods.php");
require_once("../utils/authenticateUser.php");
allowedMethods(['GET', 'POST', 'DELETE', 'OPTIONS']);
header("Content-Type: application/json");
header("Authorization: Bearer <access-token>");

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$domainUrl = $_ENV['DOMAIN_URL'];

$userId = authenticateUser();

if ($userId === null) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

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
        echo json_encode($books, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Failed to fetch books: " . $e->getMessage()]);
    }

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
