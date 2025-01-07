<?php
/**
 * @OA\Get(
 *     path="/genres",
 *     summary="Get a list of all genres",
 *     description="This endpoint retrieves a list of all genres available in the database along with their IDs and URLs.",
 *     tags={"Genres"},
 *     @OA\Response(
 *         response=200,
 *         description="A list of genres with their IDs, names, and URLs.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="count",
 *                 type="integer",
 *                 description="The total number of genres."
 *             ),
 *             @OA\Property(
 *                 property="genres",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(
 *                         property="id",
 *                         type="integer",
 *                         description="The unique identifier of the genre."
 *                     ),
 *                     @OA\Property(
 *                         property="name",
 *                         type="string",
 *                         description="The name of the genre."
 *                     ),
 *                     @OA\Property(
 *                         property="url",
 *                         type="string",
 *                         description="The URL of the genre resource."
 *                     )
 *                 ),
 *                 description="An array of genre objects."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error when fetching genres.",
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
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);