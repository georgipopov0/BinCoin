<?php
// collection_details.php

session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page or display an error
    header("Location: login.php");
    exit();
}

require 'constants.php';

// Initialize variables
$collection_id = isset($_GET['collection_id']) ? intval($_GET['collection_id']) : 0;

// Validate collection_id
if ($collection_id <= 0) {
    die("Invalid Collection ID.");
}

// Establish database connection
$conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);

// Check for connection errors
if ($conn->connect_error) {
    // Log the error and display a generic message
    error_log("Connection failed: " . $conn->connect_error);
    die("An unexpected error occurred. Please try again later.");
}

// Fetch collection details
$collection_sql = "SELECT `id`, `name`, `user_name`, `access`, `created_at` FROM `coin_collection` WHERE `id` = ?";
$stmt_collection = $conn->prepare($collection_sql);
if (!$stmt_collection) {
    error_log("Prepare failed: " . $conn->error);
    die("An unexpected error occurred. Please try again later.");
}

$stmt_collection->bind_param("i", $collection_id);
$stmt_collection->execute();
$result_collection = $stmt_collection->get_result();

if ($result_collection->num_rows === 0) {
    // No collection found with the given ID
    $stmt_collection->close();
    $conn->close();
    die("Collection not found.");
}

$collection = $result_collection->fetch_assoc();
$stmt_collection->close();

// Access Control: Check if the collection is public or owned by the current user
$has_access = false;
if ($collection['access'] === 'public') {
    $has_access = true;
} elseif ($collection['access'] === 'private' && $collection['user_name'] === $_SESSION['username']) {
    $has_access = true;
}

if (!$has_access) {
    $conn->close();
    die("You do not have permission to view this collection.");
}

// Fetch coins in the collection
$coins_sql = "SELECT `id`, `cost`, `value`, `currency`, `front_path`, `back_path`, `country`, `year` FROM `coin` WHERE `coin_collection_id` = ?";
$stmt_coins = $conn->prepare($coins_sql);
if (!$stmt_coins) {
    error_log("Prepare failed: " . $conn->error);
    die("An unexpected error occurred. Please try again later.");
}

$stmt_coins->bind_param("i", $collection_id);
$stmt_coins->execute();
$result_coins = $stmt_coins->get_result();
$coins = $result_coins->fetch_all(MYSQLI_ASSOC);
$stmt_coins->close();

// Optional: Fetch collection tags
$tags_sql = "SELECT `name` FROM `collection_tag` WHERE `collection_id` = ?";
$stmt_tags = $conn->prepare($tags_sql);
if ($stmt_tags) {
    $stmt_tags->bind_param("i", $collection_id);
    $stmt_tags->execute();
    $result_tags = $stmt_tags->get_result();
    $tags = $result_tags->fetch_all(MYSQLI_ASSOC);
    $stmt_tags->close();
} else {
    $tags = [];
}

// Optional: Fetch associated periods
$periods_sql = "SELECT p.`name`, p.`country`, p.`from`, p.`to` FROM `period` p
               JOIN `coin_period` cp ON p.`id` = cp.`period_id`
               JOIN `coin` c ON cp.`coin_id` = c.`id`
               WHERE c.`coin_collection_id` = ?";
$stmt_periods = $conn->prepare($periods_sql);
if ($stmt_periods) {
    $stmt_periods->bind_param("i", $collection_id);
    $stmt_periods->execute();
    $result_periods = $stmt_periods->get_result();
    $periods = $result_periods->fetch_all(MYSQLI_ASSOC);
    $stmt_periods->close();
} else {
    $periods = [];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Collection Details - <?= htmlspecialchars($collection['name']); ?></title>
    <!-- Link to external CSS files -->
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/collections.css">
    <link rel="stylesheet" href="css/navbar.css">
    <!-- Inline CSS for Additional Styling (Optional) -->
    <style>
        .collection-details-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .collection-header {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        .collection-header h1 {
            margin-bottom: 10px;
        }

        .collection-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-bottom: 30px;
        }

        .collection-meta div {
            flex: 1 1 200px;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .collection-meta div h3 {
            margin-top: 0;
            color: #555;
        }

        .collection-meta div p {
            margin: 5px 0 0 0;
            color: #777;
        }

        .collection-tags {
            text-align: center;
            margin-bottom: 30px;
        }

        .collection-tags .tag {
            display: inline-block;
            background-color: #007BFF;
            color: #fff;
            padding: 5px 10px;
            margin: 5px;
            border-radius: 4px;
            font-size: 14px;
        }

        .coins-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .coin-card {
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 15px;
            background-color: #fafafa;
            transition: box-shadow 0.3s;
        }

        .coin-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .coin-card img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
        }

        .coin-details {
            margin-top: 10px;
        }

        .coin-details p {
            margin: 5px 0;
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
            .collection-meta {
                flex-direction: column;
                align-items: center;
            }

            .collection-meta div {
                flex: 1 1 100%;
                max-width: 400px;
            }
        }

        /* Edit Button Styling */
        .edit-button {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #ffc107;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .edit-button:hover {
            background-color: #e0a800;
        }
    </style>
</head>

<body>
    <?php include 'components/header.php'; ?>

    <div class="collection-details-container">
        <div class="collection-header">
            <h1><?= htmlspecialchars($collection['name']); ?></h1>
            <p>Owned by: <?= htmlspecialchars($collection['user_name']); ?></p>
            <p>Access Level: <?= ucfirst(htmlspecialchars($collection['access'])); ?></p>
            <p>Created At: <?= htmlspecialchars($collection['created_at']); ?></p>

            <?php
            // Check if the current user is the owner of the collection
            if ($collection['user_name'] === $_SESSION['username']) {
                // Display the "Edit Collection" button
                echo '<a href="edit_collection.php?collection_id=' . urlencode($collection['id']) . '" class="edit-button">Edit Collection</a>';
            }
            ?>
        </div>

        <!-- Display Tags -->
        <?php if (!empty($tags)): ?>
            <div class="collection-tags">
                <?php foreach ($tags as $tag): ?>
                    <span class="tag"><?= htmlspecialchars($tag['name']); ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Display Associated Periods -->
        <?php if (!empty($periods)): ?>
            <div class="collection-meta">
                <div>
                    <h3>Associated Periods</h3>
                    <?php foreach ($periods as $period): ?>
                        <p><?= htmlspecialchars($period['name']) . " (" . htmlspecialchars($period['country']) . ", " . htmlspecialchars($period['from']) . " - " . htmlspecialchars($period['to']) . ")"; ?></p>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Display Coins -->
        <?php if (count($coins) > 0): ?>
            <h2>Coins in this Collection</h2>
            <div class="coins-list">
                <?php foreach ($coins as $coin): ?>
                    <div class="coin-card">
                        <!-- Display Front Image -->
                        <?php
                        $front_image = !empty($coin['front_path']) && file_exists($coin['front_path']) ? $coin['front_path'] : 'assets/images/placeholder.png';
                        ?>
                        <img src="<?= htmlspecialchars($front_image); ?>" alt="Front Image of <?= htmlspecialchars($coin['country']); ?> Coin">

                        <!-- Display Back Image (Optional) -->
                        <?php
                        $back_image = !empty($coin['back_path']) && file_exists($coin['back_path']) ? $coin['back_path'] : 'assets/images/placeholder.png';
                        ?>
                        <img src="<?= htmlspecialchars($back_image); ?>" alt="Back Image of <?= htmlspecialchars($coin['country']); ?> Coin">

                        <div class="coin-details">
                            <p><strong>Country:</strong> <?= htmlspecialchars($coin['country']); ?></p>
                            <p><strong>Year:</strong> <?= htmlspecialchars($coin['year']); ?></p>
                            <p><strong>Cost:</strong> <?= htmlspecialchars($coin['currency']) . " " . htmlspecialchars(number_format($coin['cost'], 2)); ?></p>
                            <p><strong>Value:</strong> <?= htmlspecialchars($coin['currency']) . " " . htmlspecialchars(number_format($coin['value'], 2)); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No coins found in this collection.</p>
        <?php endif; ?>
    </div>

    <!-- Optional: Add Footer Component -->
    <?php // include 'components/footer.php'; ?>

    <!-- Optional: Add JavaScript for Enhanced Functionality -->
</body>

</html>
