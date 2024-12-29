<?php
// coins_page.php

// Ensure this file is included after `coins.php` which sets up the $coins array
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>All Coins</title>
    <!-- Link to external CSS files -->
    <link rel="stylesheet" href="../css/theme.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <!-- Inline CSS for Additional Styling (Optional) -->
    <style>
        .container {
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            padding: 20px;
            box-sizing: border-box;
        }

        /* Pagination Styles */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination a,
        .pagination span {
            margin: 0 5px;
            padding: 8px 12px;
            text-decoration: none;
            border: 1px solid #ddd;
            color: #333;
        }

        .pagination a:hover {
            background-color: #f0f0f0;
        }

        .pagination .current {
            background-color: #333;
            color: #fff;
            border-color: #333;
        }

        /* Search Form Styles */
        .search-form {
            margin-bottom: 20px;
            text-align: center;
        }

        .search-fields {
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .search-fields input[type="text"],
        .search-fields input[type="number"],
        .search-fields select {
            padding: 8px;
            width: 200px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .search-fields input[type="submit"] {
            padding: 8px 16px;
            border: none;
            background-color: #333;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
        }

        .search-fields input[type="submit"]:hover {
            background-color: #555;
        }

        .active-filters {
            background-color: #f9f9f9;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .active-filters h3 {
            margin-top: 0;
        }

        .active-filters ul {
            list-style: none;
            padding-left: 0;
        }

        .active-filters li {
            display: inline-block;
            margin-right: 10px;
        }

        .active-filters a {
            display: inline-block;
            margin-top: 10px;
            padding: 6px 12px;
            background-color: #333;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
        }

        .active-filters a:hover {
            background-color: #555;
        }

        .error-messages {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        /* Optional: Table Styles for Better Appearance */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th,
        table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
            vertical-align: middle;
        }

        table th {
            background-color: #f4f4f4;
        }

        table tr:nth-child(even) {
            background-color: #fafafa;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .search-fields input[type="text"],
            .search-fields input[type="number"],
            .search-fields select {
                width: 80%;
            }

            table th,
            table td {
                padding: 8px;
            }

            .collection-images img {
                width: 100px;
                height: auto;
            }
        }

        /* Visually hidden label for accessibility */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            border: 0;
        }

        /* Image Styling */
        .coin-image {
            width: 100px;
            height: auto;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        /* View Button Styling */
        .view-button {
            display: inline-block;
            padding: 6px 12px;
            background-color: #17a2b8;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .view-button:hover {
            background-color: #138496;
        }
    </style>
    <!-- jQuery and jQuery UI -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script>
        $(function() {
            // Initialize autocomplete for country
            $("#country").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: "autocomplete.php",
                        dataType: "json",
                        data: {
                            field: 'country',
                            term: request.term
                        },
                        success: function(data) {
                            response(data);
                        }
                    });
                },
                minLength: 2
            });

            // Initialize autocomplete for currency
            $("#currency").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: "autocomplete.php",
                        dataType: "json",
                        data: {
                            field: 'currency',
                            term: request.term
                        },
                        success: function(data) {
                            response(data);
                        }
                    });
                },
                minLength: 2
            });
        });
    </script>
</head>

<body>
    <?php include '../components/header.php'; ?>

    <div class="container">
        <h1>Available Coins</h1>

        <!-- Display Errors -->
        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Search Form -->
        <form class="search-form" method="GET" action="coins.php">
            <div class="search-fields">
                <label for="country" class="sr-only">Country</label>
                <input type="text" name="country" id="country" placeholder="Country" value="<?= htmlspecialchars($search); ?>">

                <label for="year_from" class="sr-only">Year From</label>
                <input type="number" name="year_from" id="year_from" placeholder="Year From" value="<?= isset($_GET['year_from']) ? htmlspecialchars($_GET['year_from']) : ''; ?>">

                <label for="year_to" class="sr-only">Year To</label>
                <input type="number" name="year_to" id="year_to" placeholder="Year To" value="<?= isset($_GET['year_to']) ? htmlspecialchars($_GET['year_to']) : ''; ?>">

                <label for="currency" class="sr-only">Currency</label>
                <input type="text" name="currency" id="currency" placeholder="Currency" value="<?= isset($_GET['currency']) ? htmlspecialchars($_GET['currency']) : ''; ?>">

                <label for="sort" class="sr-only">Sort By</label>
                <select name="sort" id="sort">
                    <option value="">Sort By</option>
                    <option value="year_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'year_asc') ? 'selected' : ''; ?>>Year Ascending</option>
                    <option value="year_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'year_desc') ? 'selected' : ''; ?>>Year Descending</option>
                    <option value="value_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'value_asc') ? 'selected' : ''; ?>>Value Low to High</option>
                    <option value="value_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'value_desc') ? 'selected' : ''; ?>>Value High to Low</option>
                </select>

                <input type="submit" value="Search">
            </div>
        </form>

        <!-- Display Active Filters -->
        <?php
        $active_filters = [];
        if (isset($_GET['country']) && $_GET['country'] !== '') {
            $active_filters[] = 'Country: ' . htmlspecialchars($_GET['country']);
        }
        if (isset($_GET['year_from']) && $_GET['year_from'] !== '') {
            $active_filters[] = 'Year From: ' . htmlspecialchars($_GET['year_from']);
        }
        if (isset($_GET['year_to']) && $_GET['year_to'] !== '') {
            $active_filters[] = 'Year To: ' . htmlspecialchars($_GET['year_to']);
        }
        if (isset($_GET['currency']) && $_GET['currency'] !== '') {
            $active_filters[] = 'Currency: ' . htmlspecialchars($_GET['currency']);
        }
        if (isset($_GET['sort']) && $_GET['sort'] !== '') {
            $sort_options = [
                'year_asc' => 'Year Ascending',
                'year_desc' => 'Year Descending',
                'value_asc' => 'Value Low to High',
                'value_desc' => 'Value High to Low'
            ];
            $active_filters[] = 'Sort By: ' . htmlspecialchars($sort_options[$_GET['sort']] ?? $_GET['sort']);
        }
        ?>
        <?php if (!empty($active_filters)): ?>
            <div class="active-filters">
                <h3>Active Filters:</h3>
                <ul>
                    <?php foreach ($active_filters as $filter): ?>
                        <li><?= $filter; ?></li>
                    <?php endforeach; ?>
                </ul>
                <a href="coins.php">Clear Filters</a>
            </div>
        <?php endif; ?>

        <?php if (count($coins) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Country</th>
                        <th>Year</th>
                        <th>Currency</th>
                        <th>Value</th>
                        <th>Front Image</th>
                        <th>Back Image</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($coins as $coin): ?>
                        <tr>
                            <td><?= htmlspecialchars($coin['country']); ?></td>
                            <td><?= htmlspecialchars($coin['year']); ?></td>
                            <td><?= htmlspecialchars($coin['currency']); ?></td>
                            <td><?= htmlspecialchars(number_format($coin['value'], 2)); ?></td>
                            <td>
                                <?php
                                $front_image = !empty($coin['front_image_path']) && file_exists($coin['front_image_path']) ? $coin['front_image_path'] : 'assets/images/placeholder.png';
                                ?>
                                <img src="<?= htmlspecialchars($front_image); ?>" alt="Front Image of <?= htmlspecialchars($coin['country']); ?> Coin" class="coin-image">
                            </td>
                            <td>
                                <?php
                                $back_image = !empty($coin['back_image_path']) && file_exists($coin['back_image_path']) ? $coin['back_image_path'] : 'assets/images/placeholder.png';
                                ?>
                                <img src="<?= htmlspecialchars($back_image); ?>" alt="Back Image of <?= htmlspecialchars($coin['country']); ?> Coin" class="coin-image">
                            </td>
                            <td>
                                <a href="coin_details.php?coin_id=<?= urlencode($coin['id']); ?>" class="view-button">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Pagination Links -->
            <div class="pagination">
                <?php
                // Determine the query parameters for pagination links
                $query_params = $_GET;
                unset($query_params['page']); // We'll add 'page' parameter separately

                // Previous page link
                if ($page > 1) {
                    $query_params['page'] = $page - 1;
                    $prev_link = '?' . http_build_query($query_params);
                    echo '<a href="' . htmlspecialchars($prev_link) . '">&laquo; Previous</a>';
                } else {
                    echo '<span>&laquo; Previous</span>';
                }

                // Page number links with limited range
                $range = 2; // Number of page links on either side of current page

                // First page and ellipsis
                if ($page > ($range + 1)) {
                    $query_params['page'] = 1;
                    echo '<a href="' . htmlspecialchars('?' . http_build_query($query_params)) . '">1</a>';
                    if ($page > ($range + 2)) {
                        echo '<span>...</span>';
                    }
                }

                // Page links within range
                for ($i = max(1, $page - $range); $i <= min($total_pages, $page + $range); $i++) {
                    if ($i == $page) {
                        echo '<span class="current">' . $i . '</span>';
                    } else {
                        $query_params['page'] = $i;
                        $page_link = '?' . http_build_query($query_params);
                        echo '<a href="' . htmlspecialchars($page_link) . '">' . $i . '</a>';
                    }
                }

                // Last page and ellipsis
                if ($page < ($total_pages - $range)) {
                    if ($page < ($total_pages - $range - 1)) {
                        echo '<span>...</span>';
                    }
                    $query_params['page'] = $total_pages;
                    echo '<a href="' . htmlspecialchars('?' . http_build_query($query_params)) . '">' . $total_pages . '</a>';
                }

                // Next page link
                if ($page < $total_pages) {
                    $query_params['page'] = $page + 1;
                    $next_link = '?' . http_build_query($query_params);
                    echo '<a href="' . htmlspecialchars($next_link) . '">Next &raquo;</a>';
                } else {
                    echo '<span>Next &raquo;</span>';
                }
                ?>
            </div>
        <?php else: ?>
            <p>No coins found in the database.</p>
        <?php endif; ?>
    </div>
</body>

</html>
