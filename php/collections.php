<?php
// public_collections.php

session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page or display an error
    header("Location: login.php");
    exit();
}

require 'constants.php';

// Initialize variables
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$show_my_collections = isset($_GET['show_my_collections']) ? boolval($_GET['show_my_collections']) : false;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$results_per_page = 10;
$offset = ($page - 1) * $results_per_page;

// Establish database connection
$conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . htmlspecialchars($conn->connect_error));
}

// Build the WHERE clause based on the search filter
$where_clauses = ["`access` = 'public'"];
$params = [];
$types = '';

if ($search !== '') {
    $where_clauses[] = "`name` LIKE ?";
    $params[] = '%' . $search . '%';
    $types .= 's';
}


if ($show_my_collections) {
    $where_clauses[] = "`user_name` = ?";
    $params[] = $_SESSION['username'];
    $types .= 's';
}

$where_sql = "WHERE " . implode(" AND ", $where_clauses);

// Get total number of public collections for pagination
$count_sql = "SELECT COUNT(*) FROM `coin_collection` $where_sql";
$stmt_count = $conn->prepare($count_sql);
if ($stmt_count) {
    if ($types !== '') {
        $stmt_count->bind_param($types, ...$params);
    }
    $stmt_count->execute();
    $stmt_count->bind_result($total_collections);
    $stmt_count->fetch();
    $stmt_count->close();
} else {
    die("Failed to prepare count statement: " . htmlspecialchars($conn->error));
}

$total_pages = ceil($total_collections / $results_per_page);

// Fetch public collections based on filters and pagination
$sql = "SELECT `id`, `name`, `access`, `created_at` FROM `coin_collection` $where_sql ORDER BY `created_at` DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    if ($types !== '') {
        // Add 'ii' for LIMIT and OFFSET
        $types_with_pagination = $types . 'ii';
        $params_with_pagination = array_merge($params, [$results_per_page, $offset]);
        $stmt->bind_param($types_with_pagination, ...$params_with_pagination);
    } else {
        // Only LIMIT and OFFSET
        $stmt->bind_param('ii', $results_per_page, $offset);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $collections = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    die("Failed to prepare collection fetch statement: " . htmlspecialchars($conn->error));
}

$conn->close();

include "../components/collections_page.php"
?>