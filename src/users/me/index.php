<?php
/**
 * @OA\Get(
 *     path="/users/me/",  // Trailing slash included
 *     summary="Get current user details",
 *     description="Retrieve the details (email, username) of the authenticated user.",
 *     tags={"Users"},
 *     security={
 *         {"BearerAuth": {}}
 *     },
 *     @OA\Response(
 *         response=200,
 *         description="User details retrieved successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="user", type="object", 
 *                 @OA\Property(property="id", type="integer", description="User ID"),
 *                 @OA\Property(property="email", type="string", description="User email"),
 *                 @OA\Property(property="username", type="string", description="User username")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized - User authentication failed",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", description="Error message")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", description="Error message")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", description="Error message")
 *         )
 *     )
 * )
 * 
 * @OA\Patch(
 *     path="/users/me/",  // Trailing slash included
 *     summary="Update current user details",
 *     description="Allows the authenticated user to update their email and/or username.",
 *     tags={"Users"},
 *     security={
 *         {"BearerAuth": {}}
 *     },
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="email", type="string", description="New email of the user"),
 *             @OA\Property(property="username", type="string", description="New username of the user")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User updated successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", description="Success message")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request, invalid or missing parameters",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", description="Error message")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", description="Error message")
 *         )
 *     )
 * )
 * 
 * @OA\Patch(
 *     path="/users/me/",  // Trailing slash included
 *     summary="Update current user password",
 *     description="Allows the authenticated user to update their password.",
 *     tags={"Users"},
 *     security={
 *         {"BearerAuth": {}}
 *     },
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"current_password", "new_password"},
 *             @OA\Property(property="current_password", type="string", description="Current password"),
 *             @OA\Property(property="new_password", type="string", description="New password")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Password updated successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="string", description="Success message")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request, invalid or missing parameters",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", description="Error message")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", description="Error message")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", description="Error message")
 *         )
 *     )
 * )
 * 
 * @OA\Delete(
 *     path="/users/me/",  // Trailing slash included
 *     summary="Delete current user",
 *     description="Allows the authenticated user to delete their account.",
 *     tags={"Users"},
 *     security={
 *         {"BearerAuth": {}}
 *     },
 *     @OA\Response(
 *         response=200,
 *         description="User deleted successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="string", description="Success message")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized - User authentication failed",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", description="Error message")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", description="Error message")
 *         )
 *     )
 * )
 */
require_once("../../utils/db.php"); 
require_once("../../utils/cors.php");
require_once("../../utils/authenticateUser.php");
require_once("../../utils/allowedMethods.php");

allowedMethods(['GET', 'PATCH', 'DELETE', 'OPTIONS']); 
header('Content-Type: application/json');
header('Authorization: Bearer <access-token>'); 

// Authenticate the user
$userId = authenticateUser();

if ($userId === null) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}


$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod === 'GET') {
   
    getCurrentUser($conn, $userId);
} elseif ($requestMethod === 'PATCH') {
    $requestData = json_decode(file_get_contents("php://input"), true);
    
    // Check if the request includes password update
    if (isset($requestData['current_password'])) {
        updatePassword($conn, $userId, $requestData);
    } else {
        // Call the user info update function (email, username)
        updateUser($conn, $userId, $requestData);
    }
} elseif ($requestMethod === 'DELETE') {
    // Delete the current user
    deleteUser($conn, $userId);
} else {
    // Handle unsupported methods
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Method not allowed"]);
    exit;
}


function getCurrentUser($conn, $userId) {
    $stmt = $conn->prepare("SELECT id, email, username FROM users WHERE id = :id");
    $stmt->bindParam(":id", $userId, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        echo json_encode(["error" => "User not found"]);
        return;
    }

    echo json_encode(["user" => $user]);
}

// Update users email and/or username
function updateUser($conn, $userId, $requestData) {
    $email = $requestData['email'] ?? null;
    $username = $requestData['username'] ?? null;

    if (!$email && !$username) {
        http_response_code(400);
        echo json_encode(["error" => "No valid fields provided for update. Must include 'email' and/or 'username'"]);
        exit;
    }

    $fieldsToUpdate = [];
    $params = [];

    if ($email) {
        $fieldsToUpdate[] = "email = :email";
        $params[':email'] = $email;
    }

    if ($username) {
        $fieldsToUpdate[] = "username = :username";
        $params[':username'] = $username;
    }

    $params[':id'] = $userId;
    $sql = "UPDATE users SET " . implode(", ", $fieldsToUpdate) . " WHERE id = :id";
    $stmt = $conn->prepare($sql);

    if ($stmt->execute($params)) {
        http_response_code(200);
        echo json_encode(["message" => "User updated successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to update user"]);
    }
}


function updatePassword($conn, $userId, $requestData) {
    $currentPassword = $requestData['current_password'] ?? null;
    $newPassword = $requestData['new_password'] ?? null;

    if (!$currentPassword || !$newPassword) {
        http_response_code(400);
        echo json_encode(["error" => "Both 'current_password' and 'new_password' are required"]);
        exit;
    }

    // Retrieve the stored hash and salt for the current user
    $stmt = $conn->prepare("SELECT password, salt FROM users WHERE id = :id");
    $stmt->bindParam(":id", $userId, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        echo json_encode(["error" => "User not found"]);
        exit;
    }

    // Check if the current password matches the stored hash
    $hashedPassword = $user['password'];
    $salt = $user['salt'];

    if (!password_verify($currentPassword . $salt, $hashedPassword)) {
        http_response_code(400);
        echo json_encode(["error" => "Current password is incorrect"]);
        exit;
    }

    // Hash the new password with a new salt
    $newSalt = bin2hex(random_bytes(16));  // Generate a new salt
    $newHashedPassword = password_hash($newPassword . $newSalt, PASSWORD_BCRYPT);

    // Update the password and salt in the database
    $updateStmt = $conn->prepare("UPDATE users SET password = :password, salt = :salt WHERE id = :id");
    $updateStmt->bindParam(":password", $newHashedPassword);
    $updateStmt->bindParam(":salt", $newSalt);
    $updateStmt->bindParam(":id", $userId, PDO::PARAM_INT);

    if ($updateStmt->execute()) {
        http_response_code(200);
        echo json_encode(["success" => "Password updated successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to update password"]);
    }
}


function deleteUser($conn, $userId) {
    $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
    $stmt->bindParam(":id", $userId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        http_response_code(200); 
        echo json_encode(["success" => "User deleted successfully"]);
    } else {
        http_response_code(500); 
        echo json_encode(["error" => "Failed to delete user"]);
    }
}
?>
