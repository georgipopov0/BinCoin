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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coin Collections</title>
    <!-- Link to external CSS files -->
    <link rel="stylesheet" href="../css/theme.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <!-- Inline CSS for Additional Styling -->
    <style>
        .collections-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .collections-container h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        .filters {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
            justify-content: space-between;
            align-items: center;
        }

        .filters .search-box {
            flex: 1 1 300px;
        }

        .filters .toggle-box {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .collections-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .collection-card {
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 15px;
            background-color: #fafafa;
            transition: box-shadow 0.3s;
        }

        .collection-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .collection-card h2 {
            margin-top: 0;
            color: #007BFF;
        }

        .collection-card p {
            color: #555;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 30px;
            gap: 5px;
        }

        .pagination a,
        .pagination span {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
            transition: background-color 0.3s;
        }

        .pagination a:hover {
            background-color: #f0f0f0;
        }

        .pagination .current {
            background-color: #007BFF;
            color: #fff;
            border-color: #007BFF;
        }

        .pagination .disabled {
            color: #999;
            border-color: #ccc;
            cursor: not-allowed;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .filters {
                flex-direction: column;
                align-items: flex-start;
            }

            .filters .toggle-box {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <?php include '../components/header.php'; ?>

    <div class="collections-container">
        <h1>Coin Collections</h1>

        <div class="filters">
            <!-- Search Filter -->
            <div class="search-box">
                <form action="collections.php" method="GET">
                    <input type="text" name="search" placeholder="Search Collections..."
                        value="<?= htmlspecialchars($search); ?>"
                        style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                </form>
            </div>

            <!-- Toggle to Show Only My Collections -->
            <div class="toggle-box">
                <form action="collections.php" method="GET">
                    <?php
                    // Preserve existing GET parameters except 'show_my_collections' and 'page'
                    $query_params = $_GET;
                    unset($query_params['show_my_collections']);
                    unset($query_params['page']);
                    ?>
                    <?php foreach ($query_params as $key => $value): ?>
                        <input type="hidden" name="<?= htmlspecialchars($key); ?>" value="<?= htmlspecialchars($value); ?>">
                    <?php endforeach; ?>

                    <label for="show_my_collections">Show Only My Collections</label>
                    <input type="checkbox" id="show_my_collections" name="show_my_collections" value="1"
                        <?= $show_my_collections ? 'checked' : ''; ?> onchange="this.form.submit()">
                </form>
            </div>
        </div>

        <!-- Display Collections -->
        <?php if (count($collections) > 0): ?>
            <div class="collections-list">
                <?php foreach ($collections as $collection): ?>
                    <div class="collection-card">
                        <h2><?= htmlspecialchars($collection['name']); ?></h2>
                        <p><?= nl2br(htmlspecialchars($collection['access'])); ?></p>
                        <p><strong>Created At:</strong> <?= htmlspecialchars($collection['created_at']); ?></p>
                        <a href="collection_details.php?collection_id=<?= htmlspecialchars($collection['id']); ?>">View
                            Collection</a>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php
                    // Build query parameters excluding 'page'
                    $query_params = $_GET;
                    unset($query_params['page']);

                    // Previous page link
                    if ($page > 1) {
                        $query_params['page'] = $page - 1;
                        $prev_link = 'collections.php?' . http_build_query($query_params);
                        echo '<a href="' . htmlspecialchars($prev_link) . '">&laquo; Previous</a>';
                    } else {
                        echo '<span class="disabled">&laquo; Previous</span>';
                    }

                    // Page number links
                    for ($i = 1; $i <= $total_pages; $i++) {
                        if ($i == $page) {
                            echo '<span class="current">' . $i . '</span>';
                        } else {
                            $query_params['page'] = $i;
                            $page_link = 'collections.php?' . http_build_query($query_params);
                            echo '<a href="' . htmlspecialchars($page_link) . '">' . $i . '</a>';
                        }
                    }

                    // Next page link
                    if ($page < $total_pages) {
                        $query_params['page'] = $page + 1;
                        $next_link = 'collections.php?' . http_build_query($query_params);
                        echo '<a href="' . htmlspecialchars($next_link) . '">Next &raquo;</a>';
                    } else {
                        echo '<span class="disabled">Next &raquo;</span>';
                    }
                    ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <p>No collections found.</p>
        <?php endif; ?>
    </div>

    <!-- Optional: Add Footer Component -->
    <?php // include 'components/footer.php'; ?>

    <!-- Optional: Add JavaScript for Enhanced Functionality -->
</body>

</html>