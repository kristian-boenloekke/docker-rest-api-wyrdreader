<?php
require_once("../../utils/db.php"); 
require_once("../../utils/cors.php");
require_once("../../utils/authenticateUser.php");
require_once("../../utils/allowedMethods.php");
allowedMethods(['GET', 'OPTIONS']); 
header('Content-Type: application/json');
header('Authorization: Bearer <access-token>'); 

function getCurrentUser($conn) {
    $userId = authenticateUser();  

    if ($userId === null) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return null;
    }

    // Retrieve the user from the database using the userId
    $stmt = $conn->prepare("SELECT id, email, username FROM users WHERE id = :id");
    $stmt->bindParam(":id", $userId, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        echo json_encode(["error" => "User not found"]);
        return null;
    }

    return $user;
}

// Call the getCurrentUser function and output the user details
$currentUser = getCurrentUser($conn);
if ($currentUser) {
    echo json_encode(["user" => $currentUser]);
}
?>
