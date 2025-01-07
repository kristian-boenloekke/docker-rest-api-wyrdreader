<?php

require_once("../db.php");
require_once("../cors.php");
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$domainUrl = $_ENV['DOMAIN_URL'];
function fetchBooksWithAuthors(PDO $conn, string $baseQuery, array $filterConditions, array $filterParams): array
{
    global $domainUrl;

    // Start building the SQL query
    $query = "
        SELECT 
            b.id AS book_id,
            b.title,
            b.image,
            COALESCE(AVG(br.rating), 0) AS avg_rating, 
            COUNT(br.rating) AS rating_count
        FROM 
            books b
        LEFT JOIN
            book_ratings br ON b.id = br.book_id
    ";

    // Add filter conditions if provided
    if (!empty($filterConditions)) {
        $query .= " WHERE " . implode(" AND ", $filterConditions);
    }

    $query .= " GROUP BY b.id";

    // Prepare the query
    $stmt = $conn->prepare($query);

    // Bind the parameters
    foreach ($filterParams as $param => $value) {
        $stmt->bindValue($param, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }

    $stmt->execute();
    $booksData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format books with their authors
    $books = [];
    foreach ($booksData as $book) {
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
        $stmt->bindValue(':bookId', $book['book_id'], PDO::PARAM_INT);
        $stmt->execute();
        $authors = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $authorsLookup = array_map(function ($author) use ($domainUrl) {
            return [
                'name' => $author['name'],
                'url' => "$domainUrl/authors/" . urlencode($author['id']),
            ];
        }, $authors);

        $books[] = [
            'id' => $book['book_id'],
            'title' => $book['title'],
            'url' => "$domainUrl/books/" . urlencode($book["book_id"]),
            'authors' => $authorsLookup,
            'image' => $book['image'],
            'average_rating' => round((float) $book['avg_rating'], 2),
            'rating_count' => (int) $book['rating_count'],
        ];
    }

    return $books;
}


