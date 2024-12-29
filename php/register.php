<?php
// register.php

session_start();
require 'constants.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);

    // Validate name
    if (empty($name)) {
        $errors[] = "Name is required.";
    }

    // Additional validations (e.g., password) can be added here

    if (empty($errors)) {
        $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);

        if ($conn->connect_error) {
            die("Connection failed: " . htmlspecialchars($conn->connect_error));
        }

        // Check if user already exists
        $stmt = $conn->prepare("SELECT `name` FROM `user` WHERE `name` = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = "User already exists.";
        } else {
            // Insert new user
            $stmt_insert = $conn->prepare("INSERT INTO `user` (`name`) VALUES (?)");
            $stmt_insert->bind_param("s", $name);

            if ($stmt_insert->execute()) {
                $success = "Registration successful! You can now <a href='login.php'>login</a>.";
            } else {
                $errors[] = "Registration failed: " . htmlspecialchars($stmt_insert->error);
            }

            $stmt_insert->close();
        }

        $stmt->close();
        $conn->close();
    }
}

include '../components/register_page.php'
?>
