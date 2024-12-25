<?php
// add_coin.php

// Start session if not already started (useful for CSRF tokens, user authentication, etc.)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database constants
require 'constants.php';

// Initialize variables and error messages
$cost = $value = $currency = $country = $year = $coin_collection_id = '';
$errors = [];
$success = '';

// Establish database connection
$conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . htmlspecialchars($conn->connect_error));
}

// Fetch available collections for the dropdown
$collections = [];
$collection_sql = "SELECT `id`, `name` FROM `coin_collection` ORDER BY `name` ASC";
$collection_result = $conn->query($collection_sql);
if ($collection_result && $collection_result->num_rows > 0) {
    while ($row = $collection_result->fetch_assoc()) {
        $collections[] = $row;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and assign POST data
    $cost = trim($_POST['cost']);
    $value = trim($_POST['value']);
    $currency = trim($_POST['currency']);
    $country = trim($_POST['country']);
    $year = trim($_POST['year']);
    $coin_collection_id = trim($_POST['coin_collection_id']);

    // Validate Cost
    if (empty($cost)) {
        $errors[] = "Cost is required.";
    } elseif (!is_numeric($cost) || $cost < 0) {
        $errors[] = "Cost must be a positive number.";
    }

    // Validate Value
    if (empty($value)) {
        $errors[] = "Value is required.";
    } elseif (!is_numeric($value) || $value < 0) {
        $errors[] = "Value must be a positive number.";
    }

    // Validate Currency
    if (empty($currency)) {
        $errors[] = "Currency is required.";
    }

    // Validate Country
    if (empty($country)) {
        $errors[] = "Country is required.";
    }

    // Validate Year
    if (empty($year)) {
        $errors[] = "Year is required.";
    } elseif (!filter_var($year, FILTER_VALIDATE_INT)) {
        $errors[] = "Year must be an integer.";
    }

    // Validate Coin Collection
    if (empty($coin_collection_id)) {
        $errors[] = "Coin Collection is required.";
    } elseif (!filter_var($coin_collection_id, FILTER_VALIDATE_INT)) {
        $errors[] = "Invalid Coin Collection selected.";
    } else {
        // Check if the selected collection exists
        $stmt = $conn->prepare("SELECT `id` FROM `coin_collection` WHERE `id` = ?");
        if ($stmt) {
            $stmt->bind_param("i", $coin_collection_id);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows === 0) {
                $errors[] = "Selected Coin Collection does not exist.";
            }
            $stmt->close();
        } else {
            $errors[] = "Failed to prepare statement for Coin Collection validation.";
        }
    }

    // Validate File Uploads
    // Front Image
    if (!isset($_FILES['front_image']) || $_FILES['front_image']['error'] === UPLOAD_ERR_NO_FILE) {
        $errors[] = "Front image is required.";
    }

    // Back Image
    if (!isset($_FILES['back_image']) || $_FILES['back_image']['error'] === UPLOAD_ERR_NO_FILE) {
        $errors[] = "Back image is required.";
    }

    // If no errors so far, proceed with file uploads
    if (empty($errors)) {
        // Define upload directories
        $upload_dir = '../assets/images/';
        $front_upload_dir = $upload_dir . 'front/';
        $back_upload_dir = $upload_dir . 'back/';

        // Create directories if they don't exist
        if (!is_dir($front_upload_dir)) {
            mkdir($front_upload_dir, 0755, true);
        }
        if (!is_dir($back_upload_dir)) {
            mkdir($back_upload_dir, 0755, true);
        }

        // Function to handle image uploads
        function upload_image($file, $upload_dir)
        {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 2 * 1024 * 1024; // 2MB

            // Check for upload errors
            if ($file['error'] !== UPLOAD_ERR_OK) {
                return ['error' => "Error uploading file: " . htmlspecialchars($file['name'])];
            }

            // Validate file type
            if (!in_array($file['type'], $allowed_types)) {
                return ['error' => "Invalid file type for " . htmlspecialchars($file['name']) . ". Allowed types: JPEG, PNG, GIF."];
            }

            // Validate file size
            if ($file['size'] > $max_size) {
                return ['error' => "File " . htmlspecialchars($file['name']) . " exceeds the maximum size of 2MB."];
            }

            // Generate a unique filename
            $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $unique_name = uniqid('img_', true) . '.' . $file_ext;

            // Move the uploaded file
            $destination = $upload_dir . $unique_name;
            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                return ['error' => "Failed to move uploaded file: " . htmlspecialchars($file['name'])];
            }

            // Return the relative path to store in the database
            return ['path' => $destination];
        }

        // Upload Front Image
        $front_upload = upload_image($_FILES['front_image'], $front_upload_dir);
        if (isset($front_upload['error'])) {
            $errors[] = $front_upload['error'];
        } else {
            $front_path = $front_upload['path'];
        }

        // Upload Back Image
        $back_upload = upload_image($_FILES['back_image'], $back_upload_dir);
        if (isset($back_upload['error'])) {
            $errors[] = $back_upload['error'];
        } else {
            $back_path = $back_upload['path'];
        }

        // If file uploads were successful, insert data into the database
        if (empty($errors)) {
            $stmt = $conn->prepare("INSERT INTO `coin` (`cost`, `value`, `currency`, `front_path`, `back_path`, `country`, `year`, `coin_collection_id`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("ddsssiii", $cost, $value, $currency, $front_path, $back_path, $country, $year, $coin_collection_id);

                if ($stmt->execute()) {
                    $success = "Coin added successfully!";
                    // Reset form values
                    $cost = $value = $currency = $country = $year = $coin_collection_id = '';
                } else {
                    $errors[] = "Database insertion failed: " . htmlspecialchars($stmt->error);
                }

                $stmt->close();
            } else {
                $errors[] = "Failed to prepare statement for coin insertion.";
            }
        }
    }

    // Close the database connection
    $conn->close();

    // Generate a CSRF token if not set
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Coin</title>
    <!-- Link to external CSS files -->
    <link rel="stylesheet" href="../css/theme.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <!-- Inline CSS for Autocomplete and Additional Styling -->
    <style>
        .container {
            max-width: 600px;
            width: 100%;
            margin: 40px auto;
            padding: 20px;
            box-sizing: border-box;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #555;
        }

        input[type="text"],
        input[type="number"],
        select,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #bbb;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        select:focus,
        input[type="file"]:focus {
            border-color: #007BFF;
            outline: none;
        }

        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .error-messages {
            background-color: #f8d7da;
            color: #842029;
            padding: 15px;
            border: 1px solid #f5c2c7;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .error-messages ul {
            list-style-type: disc;
            padding-left: 20px;
            margin: 0;
        }

        .success-message {
            background-color: #d1e7dd;
            color: #0f5132;
            padding: 15px;
            border: 1px solid #badbcc;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        /* Autocomplete Suggestions Styles */
        .suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 1000;
            background-color: #fff;
            border: 1px solid #ccc;
            border-top: none;
            max-height: 200px;
            overflow-y: auto;
            display: none;
            /* Hidden by default */
        }

        .suggestions div {
            padding: 10px;
            cursor: pointer;
            font-size: 16px;
        }

        .suggestions div:hover,
        .suggestion-active {
            background-color: #f0f0f0;
        }

        /* Responsive Adjustments */
        @media (max-width: 600px) {
            .container {
                padding: 15px;
                margin: 20px auto;
            }

            input[type="submit"] {
                font-size: 16px;
            }

            .suggestions div {
                font-size: 14px;
                padding: 8px;
            }
        }
    </style>
</head>

<body>
    <?php include '../components/header.php'; ?>

    <div class="container">
        <h1>Add New Coin</h1>

        <!-- Display Success Message -->
        <?php if ($success): ?>
            <div class="success-message">
                <?= htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <!-- Display Error Messages -->
        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="add_coin.php" method="POST" enctype="multipart/form-data">
            <!-- Cost -->
            <div class="form-group">
                <label for="cost">Cost (e.g., 10.50)</label>
                <input type="text" id="cost" name="cost" value="<?= htmlspecialchars($cost); ?>" required>
            </div>

            <!-- Value -->
            <div class="form-group">
                <label for="value">Value (e.g., 15.75)</label>
                <input type="text" id="value" name="value" value="<?= htmlspecialchars($value); ?>" required>
            </div>

            <!-- Currency with Autocomplete -->
            <div class="form-group autocomplete">
                <label for="currency">Currency</label>
                <input type="text" id="currency" name="currency" placeholder="Currency"
                    value="<?= htmlspecialchars($currency); ?>" autocomplete="off" required aria-autocomplete="list"
                    aria-controls="currency-suggestions" aria-expanded="false">
                <div id="currency-suggestions" class="suggestions" role="listbox">
                    <!-- Suggestions will be populated dynamically via JavaScript -->
                </div>
            </div>

            <!-- Country with Autocomplete -->
            <div class="form-group autocomplete">
                <label for="country">Country</label>
                <input type="text" id="country" name="country" placeholder="Country"
                    value="<?= htmlspecialchars($country); ?>" autocomplete="off" required aria-autocomplete="list"
                    aria-controls="country-suggestions" aria-expanded="false">
                <div id="country-suggestions" class="suggestions" role="listbox">
                    <!-- Suggestions will be populated dynamically via JavaScript -->
                </div>
            </div>

            <!-- Year -->
            <div class="form-group">
                <label for="year">Year</label>
                <input type="number" id="year" name="year" value="<?= htmlspecialchars($year); ?>" required>
            </div>

            <!-- Coin Collection -->
            <div class="form-group">
                <label for="coin_collection_id">Coin Collection</label>
                <select id="coin_collection_id" name="coin_collection_id" required>
                    <option value="">-- Select Collection --</option>
                    <?php foreach ($collections as $collection): ?>
                        <option value="<?= htmlspecialchars($collection['id']); ?>"
                            <?= ($collection['id'] == $coin_collection_id) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($collection['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Front Image -->
            <div class="form-group">
                <label for="front_image">Front Image (JPEG, PNG, GIF | Max: 2MB)</label>
                <input type="file" id="front_image" name="front_image" accept="image/jpeg, image/png, image/gif"
                    required>
            </div>

            <!-- Back Image -->
            <div class="form-group">
                <label for="back_image">Back Image (JPEG, PNG, GIF | Max: 2MB)</label>
                <input type="file" id="back_image" name="back_image" accept="image/jpeg, image/png, image/gif" required>
            </div>

            <!-- Submit Button -->
            <input type="submit" value="Add Coin">
        </form>
    </div>

    <!-- Vanilla JavaScript for Autocomplete -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            /**
             * Initialize autocomplete functionality for a given input field.
             * @param {string} inputId - The ID of the input element.
             * @param {string} suggestionsId - The ID of the suggestions container.
             */
            function initAutocomplete(inputId, suggestionsId) {
                const input = document.getElementById(inputId);
                const suggestionsContainer = document.getElementById(suggestionsId);

                let currentFocus = -1;
                let debounceTimer;

                // Event listener for user input
                input.addEventListener('input', function () {
                    const value = this.value.trim();
                    if (!value) {
                        suggestionsContainer.innerHTML = '';
                        suggestionsContainer.style.display = 'none';
                        input.setAttribute('aria-expanded', 'false');
                        return false;
                    }

                    // Debounce the API call by 300ms
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        fetch(`autocomplete.php?field=${encodeURIComponent(inputId)}&term=${encodeURIComponent(value)}`)
                            .then(response => response.json())
                            .then(data => {
                                suggestionsContainer.innerHTML = '';
                                if (data.length === 0) {
                                    // Optionally display "No results found"
                                    const noResult = document.createElement('div');
                                    noResult.textContent = 'No results found';
                                    noResult.style.color = '#999';
                                    suggestionsContainer.appendChild(noResult);
                                    suggestionsContainer.style.display = 'block';
                                    input.setAttribute('aria-expanded', 'true');
                                    return false;
                                }

                                data.forEach(item => {
                                    const suggestionItem = document.createElement('div');
                                    // Highlight the matching part
                                    const regex = new RegExp(`(${value})`, 'gi');
                                    const highlighted = item.replace(regex, '<strong>$1</strong>');
                                    suggestionItem.innerHTML = highlighted;
                                    suggestionItem.setAttribute('role', 'option');
                                    suggestionItem.addEventListener('click', function () {
                                        input.value = item;
                                        suggestionsContainer.innerHTML = '';
                                        suggestionsContainer.style.display = 'none';
                                        input.setAttribute('aria-expanded', 'false');
                                    });
                                    suggestionsContainer.appendChild(suggestionItem);
                                });

                                suggestionsContainer.style.display = 'block';
                                input.setAttribute('aria-expanded', 'true');
                            })
                            .catch(error => {
                                console.error('Error fetching autocomplete suggestions:', error);
                                suggestionsContainer.style.display = 'none';
                                input.setAttribute('aria-expanded', 'false');
                            });
                    }, 300); // 300ms debounce delay
                });

                // Event listener for keyboard navigation
                input.addEventListener('keydown', function (e) {
                    const items = suggestionsContainer.getElementsByTagName('div');
                    if (e.key === 'ArrowDown') { // Down key
                        currentFocus++;
                        addActive(items);
                    } else if (e.key === 'ArrowUp') { // Up key
                        currentFocus--;
                        addActive(items);
                    } else if (e.key === 'Enter') { // Enter key
                        e.preventDefault();
                        if (currentFocus > -1) {
                            if (items[currentFocus]) {
                                items[currentFocus].click();
                            }
                        }
                    }
                });

                /**
                 * Adds active class to the currently focused suggestion.
                 * @param {HTMLCollection} items - Collection of suggestion items.
                 */
                function addActive(items) {
                    if (!items) return false;
                    removeActive(items);
                    if (currentFocus >= items.length) currentFocus = 0;
                    if (currentFocus < 0) currentFocus = items.length - 1;
                    items[currentFocus].classList.add('suggestion-active');
                    // Scroll into view if necessary
                    items[currentFocus].scrollIntoView({ block: 'nearest' });
                }

                /**
                 * Removes active class from all suggestion items.
                 * @param {HTMLCollection} items - Collection of suggestion items.
                 */
                function removeActive(items) {
                    for (let item of items) {
                        item.classList.remove('suggestion-active');
                    }
                }

                // Close suggestions when clicking outside
                document.addEventListener('click', function (e) {
                    if (e.target !== input) {
                        suggestionsContainer.innerHTML = '';
                        suggestionsContainer.style.display = 'none';
                        input.setAttribute('aria-expanded', 'false');
                    }
                });
            }

            // Initialize autocomplete for 'country' and 'currency'
            initAutocomplete('country', 'country-suggestions');
            initAutocomplete('currency', 'currency-suggestions');
        });
    </script>
</body>

</html>