<?php

session_start();
require 'constants.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : ''; // Passwords should not be trimmed or altered

    if (empty($name)) {
        $errors[] = "Name is required.";
    } elseif (strlen($name) < 3 || strlen($name) > 255) {
        $errors[] = "Name must be between 3 and 255 characters.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if (empty($errors)) {
        $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);

        if ($conn->connect_error) {
            error_log("Connection failed: " . $conn->connect_error);
            $errors[] = "An unexpected error occurred. Please try again later.";
        } else {
            $stmt = $conn->prepare("SELECT `password` FROM `user` WHERE `name` = ?");
            if ($stmt) {
                $stmt->bind_param("s", $name);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows === 1) {
                    $stmt->bind_result($hashed_password);
                    $stmt->fetch();

                    if (password_verify($password, $hashed_password)) {
                        $_SESSION['username'] = $name;
                        session_regenerate_id(true);

                        $stmt->close();
                        $conn->close();

                        header("Location: dashboard.php");
                        exit();
                    } else {
                        $errors[] = "Incorrect password. Please try again.";
                    }
                } else {
                    $errors[] = "User does not exist. Please register.";
                }

                $stmt->close();
            } else {
                error_log("Prepare failed: " . $conn->error);
                $errors[] = "An unexpected error occurred. Please try again later.";
            }

            $conn->close();
        }
    }

    if (!empty($errors)) {
        $_SESSION['error_messages'] = $errors;
    }
} 

include '../components/login_page.php'
?>
