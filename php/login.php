<?php
// login.php

session_start();
require 'constants.php';

$errors = [];

// Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form inputs
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : ''; // Passwords should not be trimmed or altered

    // Validate Name
    if (empty($name)) {
        $errors[] = "Name is required.";
    } elseif (strlen($name) < 3 || strlen($name) > 255) {
        $errors[] = "Name must be between 3 and 255 characters.";
    }

    // Validate Password
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    // Proceed if no validation errors
    if (empty($errors)) {
        // Establish database connection
        $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);

        // Check for connection errors
        if ($conn->connect_error) {
            // Log the error and add a generic message to errors
            error_log("Connection failed: " . $conn->connect_error);
            $errors[] = "An unexpected error occurred. Please try again later.";
        } else {
            // Prepare a statement to retrieve the hashed password for the given name
            // **Important:** Ensure that the table name is correct. Previously, it was `users`, not `user`.
            $stmt = $conn->prepare("SELECT `password` FROM `user` WHERE `name` = ?");
            if ($stmt) {
                $stmt->bind_param("s", $name);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows === 1) {
                    // User exists, retrieve the hashed password
                    $stmt->bind_result($hashed_password);
                    $stmt->fetch();

                    // Verify the entered password against the hashed password
                    if (password_verify($password, $hashed_password)) {
                        // Password is correct, set session variables
                        $_SESSION['username'] = $name;
                        // Regenerate session ID to prevent session fixation attacks
                        session_regenerate_id(true);

                        // Close statement and connection
                        $stmt->close();
                        $conn->close();

                        // Redirect to the collections page
                        header("Location: dashboard.php");
                        exit();
                    } else {
                        // Password is incorrect
                        $errors[] = "Incorrect password. Please try again.";
                    }
                } else {
                    // User does not exist
                    $errors[] = "User does not exist. Please <a href='register_form.php'>register</a>.";
                }

                $stmt->close();
            } else {
                // Log the error and add a generic message to errors
                error_log("Prepare failed: " . $conn->error);
                $errors[] = "An unexpected error occurred. Please try again later.";
            }

            // Close the database connection
            $conn->close();
        }
    }

    // If there are errors, store them in the session and redirect back to the login form
    if (!empty($errors)) {
        $_SESSION['error_messages'] = $errors;
    }
} 

include '../components/login_page.php'
?>
