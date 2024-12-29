<?php

require 'constants.php';
include "./auth.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = "mysql:host=" . SERVERNAME;
$dbname = ";dbname=" . DBNAME;

try {
	$pdo = new PDO("$host$dbname", USERNAME, PASSWORD);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	die("Connection failed: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $trade_id = $_POST['trade_id'];
    $coin_id = $_POST['coin_id'];
    $collection_id = $_POST['collection_id'];

    // Update the coin's collection
    $stmt = $pdo->prepare("UPDATE coin SET coin_collection_id = ? WHERE id = ?");
    $stmt->execute([$collection_id, $coin_id]);

    // Update the trade status to "completed"
    $stmt = $pdo->prepare("UPDATE trade SET status = 'completed' WHERE id = ?");
    $stmt->execute([$trade_id]);

    header("Location: trade.php"); // Redirect back to the trade page
    exit;
}
?>
