<?php
include "./auth.php";

header('Content-Type: application/json');
require_once 'constants.php'; // Ensure constants are included

if (!isset($_SESSION['username'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit;
}

if (!isset($_POST['coin_id'])) {
    echo json_encode(["status" => "error", "message" => "Missing coin ID"]);
    exit;
}

$buyer_name = $_SESSION['username']; // Buyer is the current logged-in user
$coin_id = $_POST['coin_id'];

$conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// Fetch the seller name (owner of the coin)
$seller_query = "SELECT user_name FROM coin_collection WHERE id = (SELECT coin_collection_id FROM coin WHERE id = ? LIMIT 1) LIMIT 1";
$stmt = $conn->prepare($seller_query);
$stmt->bind_param("i", $coin_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Seller not found"]);
    exit;
}
$seller_row = $result->fetch_assoc();
$seller_name = $seller_row['user_name'];
$stmt->close();

// Insert trade request
$stmt = $conn->prepare("INSERT INTO trade (seller_name, buyer_name, status, coin_id) VALUES (?, ?, ?, ?)");
$status = "pending";
$stmt->bind_param("sssi", $seller_name, $buyer_name, $status, $coin_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Trade initiated successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Trade initiation failed"]);
}

$stmt->close();
$conn->close();
?>
