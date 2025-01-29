<?php

include "./auth.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'constants.php';

$cost = $value = $currency = $country = $year = $coin_collection_id = '';
$errors = [];
$success = '';

$conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);

if ($conn->connect_error) {
    die("Connection failed: " . htmlspecialchars($conn->connect_error));
}

$collections = [];
$collection_sql = "SELECT `id`, `name` FROM `coin_collection` ORDER BY `name` ASC";
$collection_result = $conn->query($collection_sql);
if ($collection_result && $collection_result->num_rows > 0) {
    while ($row = $collection_result->fetch_assoc()) {
        $collections[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cost = trim($_POST['cost']);
    $value = trim($_POST['value']);
    $currency = trim($_POST['currency']);
    $country = trim($_POST['country']);
    $year = trim($_POST['year']);
    $coin_collection_id = trim($_POST['coin_collection_id']);

    if (empty($cost)) {
        $errors[] = "Cost is required.";
    } elseif (!is_numeric($cost) || $cost < 0) {
        $errors[] = "Cost must be a positive number.";
    }

    if (empty($value)) {
        $errors[] = "Value is required.";
    } elseif (!is_numeric($value) || $value < 0) {
        $errors[] = "Value must be a positive number.";
    }

    if (empty($currency)) {
        $errors[] = "Currency is required.";
    }

    if (empty($country)) {
        $errors[] = "Country is required.";
    }

    if (empty($year)) {
        $errors[] = "Year is required.";
    } elseif (!filter_var($year, FILTER_VALIDATE_INT)) {
        $errors[] = "Year must be an integer.";
    }

    if (empty($coin_collection_id)) {
        $errors[] = "Coin Collection is required.";
    } elseif (!filter_var($coin_collection_id, FILTER_VALIDATE_INT)) {
        $errors[] = "Invalid Coin Collection selected.";
    } else {
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

    if (!isset($_FILES['front_image']) || $_FILES['front_image']['error'] === UPLOAD_ERR_NO_FILE) {
        $errors[] = "Front image is required.";
    }

    if (!isset($_FILES['back_image']) || $_FILES['back_image']['error'] === UPLOAD_ERR_NO_FILE) {
        $errors[] = "Back image is required.";
    }

    if (empty($errors)) {
        $upload_dir = '../assets/images/';
        $front_upload_dir = $upload_dir . 'front/';
        $back_upload_dir = $upload_dir . 'back/';

        if (!is_dir($front_upload_dir)) {
            mkdir($front_upload_dir, 0755, true);
        }
        if (!is_dir($back_upload_dir)) {
            mkdir($back_upload_dir, 0755, true);
        }

        function upload_image($file, $upload_dir)
        {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 2 * 1024 * 1024; // 2MB

            if ($file['error'] !== UPLOAD_ERR_OK) {
                return ['error' => "Error uploading file: " . htmlspecialchars($file['name'])];
            }

            if (!in_array($file['type'], $allowed_types)) {
                return ['error' => "Invalid file type for " . htmlspecialchars($file['name']) . ". Allowed types: JPEG, PNG, GIF."];
            }

            if ($file['size'] > $max_size) {
                return ['error' => "File " . htmlspecialchars($file['name']) . " exceeds the maximum size of 2MB."];
            }

            $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $unique_name = uniqid('img_', true) . '.' . $file_ext;

            $destination = $upload_dir . $unique_name;
            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                return ['error' => "Failed to move uploaded file: " . htmlspecialchars($file['name'])];
            }

            return ['path' => $destination];
        }

        $front_upload = upload_image($_FILES['front_image'], $front_upload_dir);
        if (isset($front_upload['error'])) {
            $errors[] = $front_upload['error'];
        } else {
            $front_path = $front_upload['path'];
        }

        $back_upload = upload_image($_FILES['back_image'], $back_upload_dir);
        if (isset($back_upload['error'])) {
            $errors[] = $back_upload['error'];
        } else {
            $back_path = $back_upload['path'];
        }

        if (empty($errors)) {
            $stmt = $conn->prepare("INSERT INTO `coin` (`cost`, `value`, `currency`, `front_path`, `back_path`, `country`, `year`, `coin_collection_id`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("ddsssiii", $cost, $value, $currency, $front_path, $back_path, $country, $year, $coin_collection_id);

                if ($stmt->execute()) {
                    $success = "Coin added successfully!";
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

    $conn->close();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

include "../components/add_coin_page.php";
?>
