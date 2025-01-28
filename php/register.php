<?php
// register.php

// Start the session to handle messages between redirects
session_start();

// Include database configuration constants
require 'constants.php'; // Ensure this file defines SERVERNAME, USERNAME, PASSWORD, DBNAME

// Function to sanitize user input
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Initialize variables and error array
$errors = [];
$success = "";

// Check if the form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form inputs
    $username = isset($_POST['username']) ? sanitize_input($_POST['username']) : '';
    $password = isset($_POST['password']) ? sanitize_input($_POST['password']) : '';
    $confirm_password = isset($_POST['confirm_password']) ? sanitize_input($_POST['confirm_password']) : '';

    // Validate Username
    if (empty($username)) {
        $errors[] = "Username is required.";
    } elseif (strlen($username) < 3 || strlen($username) > 255) {
        $errors[] = "Username must be between 3 and 255 characters.";
    }

    // Validate Password
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    // Validate Confirm Password
    if (empty($confirm_password)) {
        $errors[] = "Confirm Password is required.";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
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
            // Prepare a statement to check if the username already exists
            $stmt = $conn->prepare("SELECT name FROM user WHERE name = ?");
            if ($stmt) {
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $stmt->store_result();

                // Check if username already exists
                if ($stmt->num_rows > 0) {
                    $errors[] = "Username is already taken. Please choose another.";
                }
                $stmt->close();
            } else {
                // Log the error and add a generic message to errors
                error_log("Prepare failed: " . $conn->error);
                $errors[] = "An unexpected error occurred. Please try again later.";
            }

            // If no errors, proceed to insert the new user
            if (empty($errors)) {
                // Hash the password using PASSWORD_DEFAULT algorithm
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Prepare the INSERT statement
                $insert_stmt = $conn->prepare("INSERT INTO user (name, password) VALUES (?, ?)");
                if ($insert_stmt) {
                    $insert_stmt->bind_param("ss", $username, $hashed_password);
                    if ($insert_stmt->execute()) {
                        // Registration successful
                        $success = "Registration successful! You can now log in.";
                        $insert_stmt->close();

                        // Redirect to login page with success message
                        $_SESSION['success_message'] = $success;
                        // header("Location: login.php");
                    } else {
                        // Log the error and add a generic message to errors
                        error_log("Execute failed: " . $insert_stmt->error);
                        $errors[] = "Registration failed. Please try again.";
                        $insert_stmt->close();
                    }
                } else {
                    // Log the error and add a generic message to errors
                    error_log("Prepare failed: " . $conn->error);
                    $errors[] = "An unexpected error occurred. Please try again later.";
                }
            }

            // Close the database connection
            $conn->close();
        }
    }
} 

include "../components/register_page.php"
?>
