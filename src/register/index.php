<?php

require_once("../utils/db.php");
require_once("../utils/cors.php");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Allow: POST, OPTIONS'); 
    http_response_code(200); 
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST, OPTIONS');
    http_response_code(405); 
    echo json_encode(["error" => "Only POST method is allowed"]);
    exit;
}

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
        echo "Du er nu registreret";
    } catch (PDOException $error) {
        echo "Noget gik galt: " . $error->getMessage();
    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>register</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h2>Sign up</h2>
    <form action="" method="POST">
        <label> Username
            <input type="text" name="username">
        </label>
        <label> Email
            <input type="email" name="email">
        </label>


        <label> Password
            <input type="password" name="password">
        </label>

        <button>Opret</button>

    </form>
</body>

</html>