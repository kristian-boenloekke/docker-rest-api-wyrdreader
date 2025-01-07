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

$stmt = $conn->prepare("SELECT COUNT(*) AS genre_count FROM genres");
$stmt->execute();
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$genresCount = $stmt->fetch()['genre_count'];


$stmt = $conn->prepare("SELECT id, name FROM genres");
$stmt->execute();
$genresData = $stmt->fetchAll(PDO::FETCH_ASSOC);

$genres = [];
foreach ($genresData as $genre) {
    $genres[] = [
        'id' => $genre['id'],
        'name' => $genre['name'],
        'url' => "$domainUrl/genres/" . urlencode($genre['id'])
        ];
}


$result = [
    'count' => $genresCount,
    'genres' => $genres
];


header("Content-Type: application/json");
echo json_encode($result);