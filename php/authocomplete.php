<?php
// autocomplete.php

include "./auth.php";
require 'constants.php';

// Set the response content type to JSON
header('Content-Type: application/json');

// Establish database connection
$conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);

// Check for connection errors
if ($conn->connect_error) {
    echo json_encode([]);
    exit;
}

// Retrieve and sanitize GET parameters
$term = isset($_GET['term']) ? trim($_GET['term']) : '';
$field = isset($_GET['field']) ? trim($_GET['field']) : '';

// Initialize suggestions array
$suggestions = [];

// Whitelist fields to prevent SQL injection
$allowed_fields = ['country', 'currency'];

// Proceed only if field is allowed and term is not empty
if ($term !== '' && in_array($field, $allowed_fields)) {
    // Prepare SQL statement with placeholders
    $sql = "SELECT DISTINCT `$field` FROM `coin` WHERE `$field` LIKE ? ORDER BY `$field` ASC LIMIT 10";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        // Bind parameters (s for string)
        $like_term = '%' . $term . '%';
        $stmt->bind_param('s', $like_term);
        
        // Execute the statement
        $stmt->execute();
        
        // Bind result variables
        $stmt->bind_result($result_field);
        
        // Fetch values and add to suggestions array
        while ($stmt->fetch()) {
            $suggestions[] = htmlspecialchars($result_field);
        }
        
        // Close the statement
        $stmt->close();
    }
}

// Close the database connection
$conn->close();

// Return the suggestions as JSON
echo json_encode($suggestions);
?>
