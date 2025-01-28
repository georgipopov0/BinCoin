<?php
// coins_page.php

// Start the session at the very beginning
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include necessary files (assuming coins.php sets up the $coins array and handles filtering)

// Handle any backend logic here (e.g., fetching coins based on filters)
// Ensure $coins is populated based on the filters applied

// Determine the current page for pagination
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10; // Number of coins per page
$total = count($coins);
$total_pages = ceil($total / $per_page);

// Ensure the current page is within valid range
if ($page < 1) {
    $page = 1;
} elseif ($page > $total_pages) {
    $page = $total_pages;
}

// Slice the $coins array to get only the coins for the current page
$coins = array_slice($coins, ($page - 1) * $per_page, $per_page);

// Fetch unique countries and currencies for datalist
$unique_countries = array_unique(array_map(function($coin) {
    return $coin['country'];
}, $coins));

$unique_currencies = array_unique(array_map(function($coin) {
    return $coin['currency'];
}, $coins));

// Assume $errors is an array of error messages if any
$errors = $errors ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Coin Dashboard</title>
    <!-- Link to external CSS files -->
    <link rel="stylesheet" href="../css/theme.css">
    <link rel="stylesheet" href="../css/dashboard.css"> <!-- Our refactored CSS file -->

    <!-- Include Vanilla JS Autocomplete (Optional) -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const countryInput = document.getElementById('country');
            const currencyInput = document.getElementById('currency');

            const fetchSuggestions = async (field, query) => {
                try {
                    const response = await fetch(`autocomplete.php?field=${field}&term=${encodeURIComponent(query)}`);
                    if (response.ok) {
                        const suggestions = await response.json();
                        return suggestions;
                    }
                } catch (error) {
                    console.error('Error fetching suggestions:', error);
                }
                return [];
            };

            const setupAutocomplete = (inputElement) => {
                const listId = inputElement.getAttribute('list');
                const datalist = document.getElementById(listId);

                inputElement.addEventListener('input', async () => {
                    const query = inputElement.value;
                    const field = inputElement.name;
                    if (query.length < 2) {
                        datalist.innerHTML = '';
                        return;
                    }
                    const suggestions = await fetchSuggestions(field, query);
                    datalist.innerHTML = '';
                    suggestions.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item;
                        datalist.appendChild(option);
                    });
                });
            };

            setupAutocomplete(countryInput);
            setupAutocomplete(currencyInput);
        });
    </script>
</head>
<body>
    <?php include '../components/header.php'; ?>

    <main class="container">
        <h1>Dashboard</h1>

        <!-- Display Error Messages -->
        <?php if (!empty($errors)): ?>
            <div class="error-messages" role="alert" aria-live="assertive">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Search Form -->
        <section class="search-section">
            <form class="search-form" method="GET" action="coins_page.php" aria-label="Search Coins">
                <div class="search-fields">
                    <div class="form-group">
                        <label for="country">Country</label>
                        <input type="text" name="country" id="country" placeholder="Country"
                               value="<?= isset($_GET['country']) ? htmlspecialchars($_GET['country']) : ''; ?>"
                               aria-label="Country" list="country-list" autocomplete="off">
                        <datalist id="country-list">
                            <!-- Options populated by JavaScript -->
                        </datalist>
                    </div>

                    <div class="form-group">
                        <label for="year_from">Year From</label>
                        <input type="number" name="year_from" id="year_from" placeholder="Year From"
                               value="<?= isset($_GET['year_from']) ? htmlspecialchars($_GET['year_from']) : ''; ?>"
                               aria-label="Year From" min="0">
                    </div>

                    <div class="form-group">
                        <label for="year_to">Year To</label>
                        <input type="number" name="year_to" id="year_to" placeholder="Year To"
                               value="<?= isset($_GET['year_to']) ? htmlspecialchars($_GET['year_to']) : ''; ?>"
                               aria-label="Year To" min="0">
                    </div>

                    <div class="form-group">
                        <label for="currency">Currency</label>
                        <input type="text" name="currency" id="currency" placeholder="Currency"
                               value="<?= isset($_GET['currency']) ? htmlspecialchars($_GET['currency']) : ''; ?>"
                               aria-label="Currency" list="currency-list" autocomplete="off">
                        <datalist id="currency-list">
                            <!-- Options populated by JavaScript -->
                        </datalist>
                    </div>

                    <div class="form-group">
                        <label for="sort">Sort By</label>
                        <select name="sort" id="sort" aria-label="Sort By">
                            <option value="">Sort By</option>
                            <option value="year_asc"  <?= (isset($_GET['sort']) && $_GET['sort'] == 'year_asc')  ? 'selected' : ''; ?>>Year Ascending</option>
                            <option value="year_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'year_desc') ? 'selected' : ''; ?>>Year Descending</option>
                            <option value="value_asc"  <?= (isset($_GET['sort']) && $_GET['sort'] == 'value_asc')  ? 'selected' : ''; ?>>Value Low to High</option>
                            <option value="value_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'value_desc') ? 'selected' : ''; ?>>Value High to Low</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn search-button">Search</button>
                    </div>
                </div>
            </form>
        </section>

        <!-- Display Active Filters -->
        <?php
        $active_filters = [];
        if (!empty($_GET['country'])) {
            $active_filters[] = 'Country: ' . htmlspecialchars($_GET['country']);
        }
        if (!empty($_GET['year_from'])) {
            $active_filters[] = 'Year From: ' . htmlspecialchars($_GET['year_from']);
        }
        if (!empty($_GET['year_to'])) {
            $active_filters[] = 'Year To: ' . htmlspecialchars($_GET['year_to']);
        }
        if (!empty($_GET['currency'])) {
            $active_filters[] = 'Currency: ' . htmlspecialchars($_GET['currency']);
        }
        if (!empty($_GET['sort'])) {
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
            <section class="active-filters">
                <h3>Active Filters:</h3>
                <ul>
                    <?php foreach ($active_filters as $filter): ?>
                        <li><?= $filter; ?></li>
                    <?php endforeach; ?>
                </ul>
                <a href="coins_page.php" class="clear-filters">Clear Filters</a>
            </section>
        <?php endif; ?>

        <!-- Coins Display Section -->
        <section class="coins-cards-section">
            <h2 class="section-title">Coin Collection</h2> <!-- Hardcoded title for the coins -->
            <?php if (count($coins) > 0): ?>
                <div class="cards-grid">
                    <?php foreach ($coins as $coin): ?>
                        <div class="coin-card">
                            <div class="coin-card-image-container">
                                <img src="/<?= 
                                         $coin['front_path'] 
                                 ?>" 
                                alt="Coin Image" class="coin-card-image" loading="lazy">
                            </div>
                            <div class="coin-card-content">
                                <h3 class="coin-title"><?= htmlspecialchars($coin['country']); ?> Coin</h3>
                                <p><strong>Year:</strong> <?= htmlspecialchars($coin['year']); ?></p>
                                <p><strong>Currency:</strong> <?= htmlspecialchars($coin['currency']); ?></p>
                                <p><strong>Value:</strong> $<?= htmlspecialchars(number_format($coin['value'], 2)); ?></p>
                                <a href="coin.php?coin_id=<?= urlencode($coin['id']); ?>" class="btn view-button">View Details</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <nav class="pagination" aria-label="Page navigation">
                    <?php
                    // Determine the query parameters for pagination links
                    $query_params = $_GET;
                    unset($query_params['page']); // We'll add 'page' parameter separately

                    // Previous page link
                    if ($page > 1) {
                        $query_params['page'] = $page - 1;
                        $prev_link = '?' . http_build_query($query_params);
                        echo '<a href="' . htmlspecialchars($prev_link) . '" class="pagination-link">&laquo; Previous</a>';
                    } else {
                        echo '<span class="pagination-link disabled">&laquo; Previous</span>';
                    }

                    // Page number links with limited range
                    $range = 2; // Number of page links on either side of current page

                    // First page and ellipsis
                    if ($page > ($range + 1)) {
                        $query_params['page'] = 1;
                        echo '<a href="?' . http_build_query($query_params) . '" class="pagination-link">1</a>';
                        if ($page > ($range + 2)) {
                            echo '<span class="pagination-ellipsis">...</span>';
                        }
                    }

                    // Page links within range
                    for ($i = max(1, $page - $range); $i <= min($total_pages, $page + $range); $i++) {
                        if ($i == $page) {
                            echo '<span class="pagination-link current">' . $i . '</span>';
                        } else {
                            $query_params['page'] = $i;
                            $page_link = '?' . http_build_query($query_params);
                            echo '<a href="' . htmlspecialchars($page_link) . '" class="pagination-link">' . $i . '</a>';
                        }
                    }

                    // Last page and ellipsis
                    if ($page < ($total_pages - $range)) {
                        if ($page < ($total_pages - $range - 1)) {
                            echo '<span class="pagination-ellipsis">...</span>';
                        }
                        $query_params['page'] = $total_pages;
                        echo '<a href="?' . http_build_query($query_params) . '" class="pagination-link">' . $total_pages . '</a>';
                    }

                    // Next page link
                    if ($page < $total_pages) {
                        $query_params['page'] = $page + 1;
                        $next_link = '?' . http_build_query($query_params);
                        echo '<a href="' . htmlspecialchars($next_link) . '" class="pagination-link">Next &raquo;</a>';
                    } else {
                        echo '<span class="pagination-link disabled">Next &raquo;</span>';
                    }
                    ?>
                </nav>
            <?php else: ?>
                <p class="no-coins-message">No coins found matching your criteria.</p>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
