<?php
require 'constants.php';

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


$current_user = $_SESSION['username'];
$stmt = $pdo->prepare("SELECT id, name FROM coin_collection WHERE user_name = ?");
$stmt->execute([$current_user]);

header('Content-Type: application/json');
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Debugging: Print the result
error_log(print_r($result, true)); // Log to the server error log
echo json_encode($result);
?>
