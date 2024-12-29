<?php
// collections_page.php

// Ensure this file is included after `public_collections.php` which sets up the $collections array
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Collections</title>
    <!-- Link to external CSS files -->
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/collections.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <!-- Inline CSS for Additional Styling (Optional) -->
    <style>
        .collections-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }

        .toggle-container {
            margin-bottom: 20px;
            text-align: center;
        }

        .toggle-container form {
            display: inline-block;
        }

        .toggle-container input[type="checkbox"] {
            display: none;
        }

        .toggle-container label {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007BFF;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .toggle-container label:hover {
            background-color: #0056b3;
        }

        .toggle-container input[type="checkbox"]:checked+label {
            background-color: #28a745;
        }

        .collections-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
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

        .collection-details {
            margin-top: 10px;
        }

        .collection-details p {
            margin: 5px 0;
            color: #555;
        }

        /* View Button Styling */
        .view-button {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 16px;
            background-color: #17a2b8;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .view-button:hover {
            background-color: #138496;
        }

        /* Pagination Styles */
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
            .collections-list {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
        }
    </style>
</head>

<body>

    <?php include '../components/header.php'; ?>

    <div class="collections-container">
        <div class="toggle-container">
            <form method="GET" action="collections.php">
                <!-- Preserve search term in the form -->
                <input type="hidden" name="search" value="<?= htmlspecialchars($search); ?>">
                <input type="hidden" name="page" value="1"> <!-- Reset to first page on toggle -->
                <input type="checkbox" id="show_my_collections" name="show_my_collections" value="1"
                    <?= $show_my_collections ? 'checked' : ''; ?> onchange="this.form.submit()">
                <label for="show_my_collections">Only my collections</label>
            </form>
        </div>

        <?php if (!empty($search)): ?>
            <h2>Search Results for "<?= htmlspecialchars($search); ?>"</h2>
        <?php else: ?>
            <h2>Collections</h2>
        <?php endif; ?>

        <?php if ($show_my_collections): ?>
            <h3>Your Collections</h3>
        <?php endif; ?>

        <?php if (count($collections) > 0): ?>
            <div class="collections-list">
                <?php foreach ($collections as $collection): ?>
                    <div class="collection-card">
                        <h3><?= htmlspecialchars($collection['name']); ?></h3>
                        <p><strong>Owner:</strong> <?= htmlspecialchars($collection['user_name']); ?></p>
                        <p><strong>Access:</strong> <?= ucfirst(htmlspecialchars($collection['access'])); ?></p>
                        <p><strong>Created At:</strong> <?= htmlspecialchars($collection['created_at']); ?></p>
                        <a href="collection_details.php?collection_id=<?= urlencode($collection['id']); ?>"
                            class="view-button">View Details</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No collections found.</p>
        <?php endif; ?>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php
                // Previous Page Link
                if ($page > 1) {
                    echo '<a href="?page=' . ($page - 1) . '&search=' . urlencode($search) . '&show_my_collections=' . ($show_my_collections ? '1' : '0') . '">Previous</a>';
                } else {
                    echo '<span class="disabled">Previous</span>';
                }

                // Page Number Links
                for ($i = 1; $i <= $total_pages; $i++) {
                    if ($i == $page) {
                        echo '<span class="current">' . $i . '</span>';
                    } else {
                        echo '<a href="?page=' . $i . '&search=' . urlencode($search) . '&show_my_collections=' . ($show_my_collections ? '1' : '0') . '">' . $i . '</a>';
                    }
                }

                // Next Page Link
                if ($page < $total_pages) {
                    echo '<a href="?page=' . ($page + 1) . '&search=' . urlencode($search) . '&show_my_collections=' . ($show_my_collections ? '1' : '0') . '">Next</a>';
                } else {
                    echo '<span class="disabled">Next</span>';
                }
                ?>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>