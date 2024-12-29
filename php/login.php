<?php
// login.php

session_start();
require 'constants.php';

$errors = [];

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

        // Check if user exists
        $stmt = $conn->prepare("SELECT `name` FROM `user` WHERE `name` = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            // User exists, set session
            $_SESSION['username'] = $name;
            header("Location: collections.php");
            exit();
        } else {
            $errors[] = "User does not exist. Please <a href='register.php'>register</a>.";
        }

        $stmt->close();
        $conn->close();
    }
}

include '../components/login_page.php'
?>