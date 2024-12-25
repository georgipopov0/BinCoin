<?php
require 'constants.php';

// Create a connection
$conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables for search and pagination
$country = '';
$year_from = '';
$year_to = '';
$currency = '';
$sort = '';

$errors = [];

$where_clauses = [];
$types = '';
$params = [];

// Fetch distinct countries
$country_sql = "SELECT DISTINCT country FROM coin ORDER BY country ASC";
$country_result = $conn->query($country_sql);
$countries = [];
if ($country_result->num_rows > 0) {
    while ($row = $country_result->fetch_assoc()) {
        $countries[] = $row['country'];
    }
}

// Fetch distinct currencies
$currency_sql = "SELECT DISTINCT currency FROM coin ORDER BY currency ASC";
$currency_result = $conn->query($currency_sql);
$currencies = [];
if ($currency_result->num_rows > 0) {
    while ($row = $currency_result->fetch_assoc()) {
        $currencies[] = $row['currency'];
    }
}

// Handle search inputs
if (isset($_GET['country']) && trim($_GET['country']) !== '') {
    $country = trim($_GET['country']);
    $where_clauses[] = "country = ?";
    $params[] = $country;
    $types .= 's';
}

if (isset($_GET['year_from']) && $_GET['year_from'] !== '') {
    if (is_numeric($_GET['year_from'])) {
        $year_from = (int) $_GET['year_from'];
        $where_clauses[] = "year >= ?";
        $params[] = $year_from;
        $types .= 'i';
    } else {
        $errors[] = "Year From must be a number.";
    }
}

if (isset($_GET['year_to']) && $_GET['year_to'] !== '') {
    if (is_numeric($_GET['year_to'])) {
        $year_to = (int) $_GET['year_to'];
        $where_clauses[] = "year <= ?";
        $params[] = $year_to;
        $types .= 'i';
    } else {
        $errors[] = "Year To must be a number.";
    }
}

if ($year_from && $year_to && $year_from > $year_to) {
    $errors[] = "Year From cannot be greater than Year To.";
}

if (isset($_GET['currency']) && trim($_GET['currency']) !== '') {
    $currency = trim($_GET['currency']);
    $where_clauses[] = "currency = ?";
    $params[] = $currency;
    $types .= 's';
}

// Handle sort input
if (isset($_GET['sort']) && trim($_GET['sort']) !== '') {
    $sort = trim($_GET['sort']);
    switch ($sort) {
        case 'year_asc':
            $order_by = 'year';
            $order_dir = 'ASC';
            break;
        case 'year_desc':
            $order_by = 'year';
            $order_dir = 'DESC';
            break;
        case 'value_asc':
            $order_by = 'value';
            $order_dir = 'ASC';
            break;
        case 'value_desc':
            $order_by = 'value';
            $order_dir = 'DESC';
            break;
        default:
            // Default sorting
            $order_by = 'id';
            $order_dir = 'ASC';
            break;
    }
} else {
    // Default sorting
    $order_by = 'id';
    $order_dir = 'ASC';
}

// Pagination settings
$limit = 10; // Number of records per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Build the SQL query with search and sorting
$sql = "SELECT id, country, year, currency, value FROM coin";

if (count($where_clauses) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}

$sql .= " ORDER BY $order_by $order_dir";

// Get total records for pagination
$count_sql = "SELECT COUNT(*) FROM coin";
if (count($where_clauses) > 0) {
    $count_sql .= " WHERE " . implode(" AND ", $where_clauses);
}

$stmt_count = $conn->prepare($count_sql);
if ($types !== '') {
    $stmt_count->bind_param($types, ...$params);
}
$stmt_count->execute();
$stmt_count->bind_result($total_records);
$stmt_count->fetch();
$stmt_count->close();

$total_pages = ceil($total_records / $limit);

// Append LIMIT clause for pagination
$sql .= " LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);

// Bind parameters
if ($types !== '') {
    $types_with_limit = $types . 'ii';
    $params_with_limit = array_merge($params, [$limit, $offset]);
    $stmt->bind_param($types_with_limit, ...$params_with_limit);
} else {
    $stmt->bind_param('ii', $limit, $offset);
}

// Execute and fetch results
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $coins = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $coins = [];
}

$stmt->close();
$conn->close();
?>


<?php include '../components/coins_page.php'; ?>