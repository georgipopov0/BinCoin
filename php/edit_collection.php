<?php

include "./auth.php";

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require 'constants.php';

$collection_id = isset($_GET['collection_id']) ? intval($_GET['collection_id']) : 0;

if ($collection_id <= 0) {
    die("Invalid Collection ID.");
}

$conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);

if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    die("An unexpected error occurred. Please try again later.");
}

$collection_sql = "SELECT `id`, `name`, `user_name`, `access` FROM `coin_collection` WHERE `id` = ?";
$stmt_collection = $conn->prepare($collection_sql);
if (!$stmt_collection) {
    error_log("Prepare failed: " . $conn->error);
    die("An unexpected error occurred. Please try again later.");
}

$stmt_collection->bind_param("i", $collection_id);
$stmt_collection->execute();
$result_collection = $stmt_collection->get_result();

if ($result_collection->num_rows === 0) {
    $stmt_collection->close();
    $conn->close();
    die("Collection not found.");
}

$collection = $result_collection->fetch_assoc();
$stmt_collection->close();

if ($collection['user_name'] !== $_SESSION['username']) {
    $conn->close();
    die("You do not have permission to edit this collection.");
}

$new_name = $collection['name'];
$new_access = $collection['access'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token.");
    }

    $new_name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $new_access = isset($_POST['access']) ? trim($_POST['access']) : 'private';

    if (empty($new_name)) {
        $errors[] = "Collection name cannot be empty.";
    }

    if (!in_array($new_access, ['public', 'private', 'protected'])) {
        $errors[] = "Invalid access level selected.";
    }

    $allowed_users = [];
    if ($new_access === 'protected') {
        $allowed_users = isset($_POST['allowed_users']) ? $_POST['allowed_users'] : [];
        $allowed_users = array_map('trim', $allowed_users); // Trim whitespace
        $allowed_users = array_filter($allowed_users); // Remove empty entries

        if (!empty($allowed_users)) {
            $placeholders = implode(',', array_fill(0, count($allowed_users), '?'));
            $user_validation_sql = "SELECT `name` FROM `user` WHERE `name` IN ($placeholders)";
            $stmt_validate = $conn->prepare($user_validation_sql);
            if ($stmt_validate) {
                $stmt_validate->bind_param(str_repeat('s', count($allowed_users)), ...$allowed_users);
                $stmt_validate->execute();
                $result_validate = $stmt_validate->get_result();
                $valid_users = [];
                while ($row = $result_validate->fetch_assoc()) {
                    $valid_users[] = $row['name'];
                }
                $stmt_validate->close();

                $invalid_users = array_diff($allowed_users, $valid_users);
                if (!empty($invalid_users)) {
                    $errors[] = "The following users do not exist: " . implode(', ', $invalid_users);
                }

                if (in_array($_SESSION['username'], $valid_users)) {
                    $errors[] = "You cannot add yourself to the allowed users list.";
                }
            } else {
                error_log("Prepare failed: " . $conn->error);
                $errors[] = "An unexpected error occurred. Please try again later.";
            }
        }
    }

    if (empty($errors)) {
        $conn->begin_transaction();

        try {
            $update_sql = "UPDATE `coin_collection` SET `name` = ?, `access` = ? WHERE `id` = ?";
            $stmt_update = $conn->prepare($update_sql);
            if (!$stmt_update) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $stmt_update->bind_param("ssi", $new_name, $new_access, $collection_id);
            if (!$stmt_update->execute()) {
                throw new Exception("Execute failed: " . $stmt_update->error);
            }
            $stmt_update->close();

            if ($new_access === 'protected') {
                $delete_sql = "DELETE FROM `access_control` WHERE `collection_id` = ?";
                $stmt_delete = $conn->prepare($delete_sql);
                if (!$stmt_delete) {
                    throw new Exception("Prepare failed: " . $conn->error);
                }
                $stmt_delete->bind_param("i", $collection_id);
                if (!$stmt_delete->execute()) {
                    throw new Exception("Execute failed: " . $stmt_delete->error);
                }
                $stmt_delete->close();

                if (!empty($allowed_users)) {
                    $insert_sql = "INSERT INTO `access_control` (`user_name`, `collection_id`) VALUES (?, ?)";
                    $stmt_insert = $conn->prepare($insert_sql);
                    if (!$stmt_insert) {
                        throw new Exception("Prepare failed: " . $conn->error);
                    }

                    foreach ($allowed_users as $user) {
                        $stmt_insert->bind_param("si", $user, $collection_id);
                        if (!$stmt_insert->execute()) {
                            throw new Exception("Execute failed: " . $stmt_insert->error);
                        }
                    }
                    $stmt_insert->close();
                }
            } else {
                $delete_sql = "DELETE FROM `access_control` WHERE `collection_id` = ?";
                $stmt_delete = $conn->prepare($delete_sql);
                if (!$stmt_delete) {
                    throw new Exception("Prepare failed: " . $conn->error);
                }
                $stmt_delete->bind_param("i", $collection_id);
                if (!$stmt_delete->execute()) {
                    throw new Exception("Execute failed: " . $stmt_delete->error);
                }
                $stmt_delete->close();
            }

            $conn->commit();

            header("Location: collection_details.php?collection_id=" . urlencode($collection_id));
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            error_log("Transaction failed: " . $e->getMessage());
            $errors[] = "Failed to update the collection. Please try again.";
        }
    }
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$allowed_users = [];
if ($collection['access'] === 'protected') {
    $allowed_sql = "SELECT `user_name` FROM `access_control` WHERE `collection_id` = ?";
    $stmt_allowed = $conn->prepare($allowed_sql);
    if ($stmt_allowed) {
        $stmt_allowed->bind_param("i", $collection_id);
        $stmt_allowed->execute();
        $result_allowed = $stmt_allowed->get_result();
        while ($row = $result_allowed->fetch_assoc()) {
            $allowed_users[] = $row['user_name'];
        }
        $stmt_allowed->close();
    }
}

$conn->close();

include '../components/edit_collection_page.php';
?>