<?php
// coin_details.php

require 'constants.php';

// Sanitize and validate the 'coin_id' parameter
$coin_id = isset($_GET['coin_id']) ? intval($_GET['coin_id']) : 0;

if ($coin_id <= 0) {
    die("Invalid coin ID.");
}

// Establish database connection
$conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);

if ($conn->connect_error) {
    die("Connection failed: " . htmlspecialchars($conn->connect_error));
}

// Prepare the SQL statement to prevent SQL injection
$sql = "SELECT 
            cost, 
            value, 
            currency, 
            front_path, 
            back_path, 
            country, 
            year 
        FROM coin 
        WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $coin_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if the coin exists
if ($result->num_rows > 0) {
    $coin = $result->fetch_assoc();
} else {
    die("Coin not found.");
}

$stmt->close();
$conn->close();

// Function to verify if the image exists
function verify_image($path)
{
    // Assuming 'assets' is in the same directory as 'coin_details.php'
    // Adjust the path if your structure is different
    return file_exists($path) ? htmlspecialchars($path) : 'assets/images/placeholder.png';
}

include "../components/coin_page.php"
?>

