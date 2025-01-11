<?php
// public_collections.php

// Include the authentication check
require './auth.php'; // Ensure 'auth.php' handles session_start() and authentication

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
    // Log the error and display a generic message to the user
    error_log("Connection failed: " . $conn->connect_error);
    die("An unexpected error occurred. Please try again later.");
}

// Start building the WHERE clause
$where_clauses = [];
$params = [];
$types = '';

// If the toggle is enabled, only show the user's own collections
if ($show_my_collections) {
    $where_clauses[] = "`user_name` = ?";
    $params[] = $_SESSION['username'];
    $types .= 's';
} else {
    // Otherwise, show public collections, protected collections the user is allowed to see, and the user's own collections
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

// If there's a search term, add it to the WHERE clause
if ($search !== '') {
    // Add a separate condition for the search, ensuring it applies within the appropriate scope
    if ($show_my_collections) {
        // When showing only user's collections, add the search within that scope
        $where_clauses[] = "`name` LIKE ?";
    } else {
        // When showing all relevant collections, ensure the search applies to all
        $where_clauses[] = "`name` LIKE ?";
    }
    $params[] = '%' . $search . '%';
    $types .= 's';
}

// Combine all WHERE clauses with AND conditions
$where_sql = "WHERE " . implode(" AND ", $where_clauses);

// Get total number of collections for pagination
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
    // Log the error and display a generic message
    error_log("Prepare failed (COUNT): " . $conn->error);
    die("An unexpected error occurred. Please try again later.");
}

$total_pages = ceil($total_collections / $results_per_page);

// Fetch collections based on filters and pagination
$sql = "SELECT `id`, `name`, `access`, `user_name` 
        FROM `coin_collection` 
        $where_sql 
        ORDER BY `name` DESC 
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
if ($stmt) {
    // Bind parameters for the main query
    // Add 'ii' for LIMIT and OFFSET
    $types_with_pagination = $types . 'ii';
    $params_with_pagination = array_merge($params, [$results_per_page, $offset]);
    $stmt->bind_param($types_with_pagination, ...$params_with_pagination);
    $stmt->execute();
    $result = $stmt->get_result();
    $collections = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    // Log the error and display a generic message
    error_log("Prepare failed (FETCH): " . $conn->error);
    die("An unexpected error occurred. Please try again later.");
}

$conn->close();

// Include the collections display page
include "../components/collections_page.php";
?>
