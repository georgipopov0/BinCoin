<?php

include "./auth.php";
require 'constants.php';

header('Content-Type: application/json');

$conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);

if ($conn->connect_error) {
    echo json_encode([]);
    exit;
}

$term = isset($_GET['term']) ? trim($_GET['term']) : '';
$field = isset($_GET['field']) ? trim($_GET['field']) : '';

$suggestions = [];

$allowed_fields = ['country', 'currency'];

if ($term !== '' && in_array($field, $allowed_fields)) {
    $sql = "SELECT DISTINCT `$field` FROM `coin` WHERE `$field` LIKE ? ORDER BY `$field` ASC LIMIT 10";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $like_term = '%' . $term . '%';
        $stmt->bind_param('s', $like_term);
        
        $stmt->execute();
        
        $stmt->bind_result($result_field);
        
        while ($stmt->fetch()) {
            $suggestions[] = htmlspecialchars($result_field);
        }
        
        $stmt->close();
    }
}

$conn->close();

echo json_encode($suggestions);
?>
