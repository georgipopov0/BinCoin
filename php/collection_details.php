<?php

include "./auth.php";

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

$has_access = false;

if ($collection['access'] === 'public') {
    $has_access = true;
} elseif ($collection['access'] === 'private' && $collection['user_name'] === $_SESSION['username']) {
    $has_access = true;
} elseif ($collection['access'] === 'protected') {
    if ($collection['user_name'] === $_SESSION['username']) {
        $has_access = true;
    } else {
        $allowed_users_sql = "SELECT `user_name` FROM `access_control` WHERE `collection_id` = ?";
        $stmt_allowed = $conn->prepare($allowed_users_sql);
        if ($stmt_allowed) {
            $stmt_allowed->bind_param("i", $collection_id);
            $stmt_allowed->execute();
            $result_allowed = $stmt_allowed->get_result();
            $allowed_users = [];
            while ($row = $result_allowed->fetch_assoc()) {
                $allowed_users[] = $row['user_name'];
            }
            $stmt_allowed->close();

            if (in_array($_SESSION['username'], $allowed_users)) {
                $has_access = true;
            }
        }
    }
}

if (!$has_access) {
    $conn->close();
    die("You do not have permission to view this collection.");
}

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

$allowed_users = [];
if ($collection['access'] === 'protected') {
    $allowed_users_sql = "SELECT `user_name` FROM `access_control` WHERE `collection_id` = ?";
    $stmt_allowed_users = $conn->prepare($allowed_users_sql);
    if ($stmt_allowed_users) {
        $stmt_allowed_users->bind_param("i", $collection_id);
        $stmt_allowed_users->execute();
        $result_allowed_users = $stmt_allowed_users->get_result();
        while ($row = $result_allowed_users->fetch_assoc()) {
            $allowed_users[] = $row['user_name'];
        }
        $stmt_allowed_users->close();
    }
}

$conn->close();

include "../components/collection_details_page.php";
?>
