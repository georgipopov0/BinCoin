<?php

require './auth.php'; // Ensure 'auth.php' handles session_start() and authentication

require 'constants.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$show_my_collections = isset($_GET['show_my_collections']) ? boolval($_GET['show_my_collections']) : false;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$results_per_page = 10;
$offset = ($page - 1) * $results_per_page;

$conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);

if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    die("An unexpected error occurred. Please try again later.");
}

$where_clauses = [];
$params = [];
$types = '';

if ($show_my_collections) {
    $where_clauses[] = "`user_name` = ?";
    $params[] = $_SESSION['username'];
    $types .= 's';
} else {
    $where_clauses[] = "(`access` = 'public' OR 
                         (`access` = 'protected' AND EXISTS (
                             SELECT 1 FROM `access_control` ac 
                             WHERE ac.`collection_id` = `coin_collection`.`id` 
                               AND ac.`user_name` = ?
                         )) OR 
                         (`user_name` = ?))";
    $params[] = $_SESSION['username']; // For the EXISTS subquery
    $params[] = $_SESSION['username']; // For the user's own collections
    $types .= 'ss';
}

if ($search !== '') {
    if ($show_my_collections) {
        $where_clauses[] = "`name` LIKE ?";
    } else {
        $where_clauses[] = "`name` LIKE ?";
    }
    $params[] = '%' . $search . '%';
    $types .= 's';
}

$where_sql = "WHERE " . implode(" AND ", $where_clauses);

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
    error_log("Prepare failed (COUNT): " . $conn->error);
    die("An unexpected error occurred. Please try again later.");
}

$total_pages = ceil($total_collections / $results_per_page);

$sql = "SELECT `id`, `name`, `access`, `user_name` 
        FROM `coin_collection` 
        $where_sql 
        ORDER BY `name` DESC 
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $types_with_pagination = $types . 'ii';
    $params_with_pagination = array_merge($params, [$results_per_page, $offset]);
    $stmt->bind_param($types_with_pagination, ...$params_with_pagination);
    $stmt->execute();
    $result = $stmt->get_result();
    $collections = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    error_log("Prepare failed (FETCH): " . $conn->error);
    die("An unexpected error occurred. Please try again later.");
}

$conn->close();

include "../components/collections_page.php";
?>
