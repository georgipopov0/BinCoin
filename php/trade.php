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

// Handle status update requests
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = $_GET['id'];

    if ($action === 'confirm') {
        $stmt = $pdo->prepare("UPDATE trade SET status = 'completed' WHERE id = ? AND status = 'pending'");
        $stmt->execute([$id]);
    } elseif ($action === 'cancel') {
        $stmt = $pdo->prepare("UPDATE trade SET status = 'cancelled' WHERE id = ? AND status = 'pending'");
        $stmt->execute([$id]);
    }

    header("Location: " . $_SERVER['PHP_SELF'] . "?view=received");
    exit;
}

// Retrieve all trade entries based on filters
$filter_status = $_GET['status'] ?? '';
$view_type = $_GET['view'] ?? 'sent'; // Default to "sent"
$current_user = $_SESSION['username']; // Replace with actual current user logic
$query = "SELECT * FROM trade";
$params = [];

if ($view_type === 'received') {
	$query .= " WHERE seller_name = ?";
	$params[] = $current_user;
} else {
	$query .= " WHERE buyer_name = ?";
	$params[] = $current_user;
}

if (!empty($filter_status)) {
	$query .= " AND status = ?";
	$params[] = $filter_status;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$trades = $stmt->fetchAll(PDO::FETCH_ASSOC);

include "../components/trade_page.php";
?>

