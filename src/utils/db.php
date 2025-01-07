<?php

$servername = "mysql_server"; // Name of the MySQL service in docker-compose.yml
$username = "root";           // MYSQL_USER in docker-compose.yml
$password = "";               // Empty password as per the configuration
$dbname = "bookdb";           // MYSQL_DATABASE in docker-compose.yml

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "<script>console.log('Connected successfully')</script>";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
