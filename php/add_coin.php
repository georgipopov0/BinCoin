<?php

include "./auth.php";
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

include "../components/add_coin_page.php";
?>
