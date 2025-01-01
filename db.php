<?php
// $servername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "ayoqr";

$servername = "tellafarrestaurant.com";
$username = "u257031014_ayo";
$password = "OnGod@@123";
$dbname = "u257031014_ayo";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
