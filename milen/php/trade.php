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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_trade'])) {
    $trade_id = $_POST['trade_id'];
    $collection_id = $_POST['collection_id'];
    $current_user =  $_SESSION['username']; // Replace with your session user logic

    $stmt = $pdo->prepare("SELECT coin_id FROM trade WHERE id = ? AND status = 'pending'");
    $stmt->execute([$trade_id]);
    $trade = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($trade) {
        $coin_id = $trade['coin_id'];

        $stmt = $pdo->prepare("UPDATE coin SET collection_id = ?, owner = ? WHERE id = ?");
        $stmt->execute([$collection_id, $current_user, $coin_id]);

        $stmt = $pdo->prepare("UPDATE trade SET status = 'completed' WHERE id = ?");
        $stmt->execute([$trade_id]);

        echo json_encode(['success' => true]);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid trade or already completed.']);
        exit;
    }
}


$stmt = $pdo->prepare($query);
$stmt->execute($params);
$trades = $stmt->fetchAll(PDO::FETCH_ASSOC);

include "../components/trade_page.php";
?>