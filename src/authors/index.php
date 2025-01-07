<?php

require_once("../utils/db.php");
require_once("../utils/cors.php");
require_once("../vendor/autoload.php");
require_once("../utils/allowedMethods.php");
allowedMethods(['GET', 'OPTIONS']);
header("Content-Type: application/json");


$sql = "
    SELECT 
        a.*,
        GROUP_CONCAT(DISTINCT b.title SEPARATOR ', ') AS books
    FROM 
        authors a
    LEFT JOIN 
        book_authors ba ON a.id = ba.author_id
    LEFT JOIN 
        books b ON ba.book_id = b.id
    GROUP BY 
        a.id
";

$stmt = $conn->prepare($sql);
$stmt->execute();
$authorsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

$authors = [];
foreach ($authorsData as $author) {
    $authors[] = [
        'id' => $author['id'],
        'name' => $author['name'],
        'books' => explode(',', $author['books'])
    ];
}

$result = [
    'authors' => $authors
];

echo json_encode($result);