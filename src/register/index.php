<?php
/**
 * @OA\Post(
 *     path="/register/",  // Trailing slash included
 *     summary="Register a new user",
 *     description="Registers a new user by providing username, email, and password. The password will be hashed and salted before storing in the database.",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"username", "email", "password"},
 *             @OA\Property(property="username", type="string", description="Username of the new user"),
 *             @OA\Property(property="email", type="string", format="email", description="Email of the new user"),
 *             @OA\Property(property="password", type="string", format="password", description="Password of the new user")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User created successfully",
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
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", description="Error message")
 *         )
 *     )
 * )
 */

require_once("../utils/db.php");
require_once("../utils/cors.php");
require_once("../utils/allowedMethods.php");
allowedMethods(['POST', 'OPTIONS']);

if (!empty($_POST)) {
    $cols = "username, email, password, salt";
    $values = ":username, :email, :password, :salt";
    $stmt = $conn->prepare("INSERT INTO users ($cols) VALUES($values)");
    $stmt->bindParam(":username", $_POST["username"]);
    $stmt->bindParam(":email", $_POST["email"]);

    $salt = bin2hex(random_bytes(16));
    $hash = password_hash($_POST["password"] . $salt, PASSWORD_BCRYPT);

    $stmt->bindParam(":password", $hash);
    $stmt->bindParam(":salt", $salt);
    
    try {
        $stmt->execute();
        echo json_encode(["success" => "User created successfully"]);
    } catch (PDOException $error) {
        echo json_encode(["error"=> $error->getMessage()]);
    }

}