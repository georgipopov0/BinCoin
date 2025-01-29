<?php

include "./auth.php";
require 'constants.php';

$coin_id = isset($_GET['coin_id']) ? intval($_GET['coin_id']) : 0;

if ($coin_id <= 0) {
    die("Invalid coin ID.");
}

$conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);

if ($conn->connect_error) {
    die("Connection failed: " . htmlspecialchars($conn->connect_error));
}

$sql = "SELECT 
            cost, 
            value, 
            currency, 
            front_path, 
            back_path, 
            country, 
            year 
        FROM coin 
        WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $coin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $coin = $result->fetch_assoc();
} else {
    die("Coin not found.");
}

$stmt->close();
$conn->close();


include "../components/coin_page.php"
?>

