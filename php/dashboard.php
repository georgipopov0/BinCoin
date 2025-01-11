<?php

// include("./auth.php");

// coins.php

// Start session if not already started (useful for CSRF tokens, user authentication, etc.)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database constants
require 'constants.php';

// Initialize variables and error messages
$country = isset($_GET['country']) ? trim($_GET['country']) : '';
$year_from = isset($_GET['year_from']) && is_numeric($_GET['year_from']) ? (int) $_GET['year_from'] : null;
$year_to = isset($_GET['year_to']) && is_numeric($_GET['year_to']) ? (int) $_GET['year_to'] : null;
$currency = isset($_GET['currency']) ? trim($_GET['currency']) : '';
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : '';
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;

$errors = [];

// Define number of results per page
$results_per_page = 10;

// Initialize the coins array
$coins = [];

// Establish database connection using mysqli
$conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . htmlspecialchars($conn->connect_error));
}

// Build the WHERE clause based on user inputs
$where_clauses = [];
$params = [];
$types = '';

// Filter by Country
if ($country !== '') {
    $where_clauses[] = "country LIKE ?";
    $params[] = '%' . $country . '%';
    $types .= 's';
}

// Filter by Year From
if ($year_from !== null) {
    $where_clauses[] = "year >= ?";
    $params[] = $year_from;
    $types .= 'i';
}

// Filter by Year To
if ($year_to !== null) {
    $where_clauses[] = "year <= ?";
    $params[] = $year_to;
    $types .= 'i';
}

// Validate Year Range
if ($year_from !== null && $year_to !== null && $year_from > $year_to) {
    $errors[] = "Year From cannot be greater than Year To.";
}

// Filter by Currency
if ($currency !== '') {
    $where_clauses[] = "currency LIKE ?";
    $params[] = '%' . $currency . '%';
    $types .= 's';
}

// Determine the ORDER BY clause based on sorting
$order_by = "ORDER BY id ASC"; // Default sorting
switch ($sort) {
    case 'year_asc':
        $order_by = "ORDER BY year ASC";
        break;
    case 'year_desc':
        $order_by = "ORDER BY year DESC";
        break;
    case 'value_asc':
        $order_by = "ORDER BY value ASC";
        break;
    case 'value_desc':
        $order_by = "ORDER BY value DESC";
        break;
    default:
        $order_by = "ORDER BY id ASC";
        break;
}

// Pagination calculations
$start_from = ($page - 1) * $results_per_page;

// Build the SQL query with search and sorting
$sql = "SELECT id, country, year, currency, value FROM coin";

if (count($where_clauses) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}

$sql .= " $order_by";

// Get total records for pagination
$count_sql = "SELECT COUNT(*) FROM coin";
if (count($where_clauses) > 0) {
    $count_sql .= " WHERE " . implode(" AND ", $where_clauses);
}

$stmt_count = $conn->prepare($count_sql);
if ($stmt_count) {
    if ($types !== '') {
        $stmt_count->bind_param($types, ...$params);
    }
    $stmt_count->execute();
    $stmt_count->bind_result($total_records);
    $stmt_count->fetch();
    $stmt_count->close();
} else {
    $errors[] = "Failed to prepare count statement: " . htmlspecialchars($conn->error);
    $total_records = 0;
}

$total_pages = ceil($total_records / $results_per_page);

// Append LIMIT clause for pagination
$sql .= " LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);

if ($stmt) {
    if ($types !== '') {
        // Add types for LIMIT and OFFSET
        $types_with_limit = $types . 'ii';
        $params_with_limit = array_merge($params, [$results_per_page, $start_from]);
        $stmt->bind_param($types_with_limit, ...$params_with_limit);
    } else {
        // Only LIMIT and OFFSET
        $stmt->bind_param('ii', $results_per_page, $start_from);
    }

    // Execute and fetch results
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $coins = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            $coins = [];
        }
    } else {
        $errors[] = "Failed to execute search query: " . htmlspecialchars($stmt->error);
    }

    $stmt->close();
} else {
    $errors[] = "Failed to prepare search statement: " . htmlspecialchars($conn->error);
}

$conn->close();

// Fetch distinct countries and currencies for the search form
$conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);
if ($conn->connect_error) {
    die("Connection failed: " . htmlspecialchars($conn->connect_error));
}

// Fetch distinct countries
$country_sql = "SELECT DISTINCT country FROM coin ORDER BY country ASC";
$country_result = $conn->query($country_sql);
$countries = [];
if ($country_result && $country_result->num_rows > 0) {
    while ($row = $country_result->fetch_assoc()) {
        $countries[] = $row['country'];
    }
}

// Fetch distinct currencies
$currency_sql = "SELECT DISTINCT currency FROM coin ORDER BY currency ASC";
$currency_result = $conn->query($currency_sql);
$currencies = [];
if ($currency_result && $currency_result->num_rows > 0) {
    while ($row = $currency_result->fetch_assoc()) {
        $currencies[] = $row['currency'];
    }
}

$conn->close();

// Include the coins_page.php component to display the results
// Pass variables to the included file
include '../components/dashboard.php';
?>
