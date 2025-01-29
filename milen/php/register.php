<?php

session_start();

require 'constants.php'; // Ensure this file defines SERVERNAME, USERNAME, PASSWORD, DBNAME

function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST['username']) ? sanitize_input($_POST['username']) : '';
    $password = isset($_POST['password']) ? sanitize_input($_POST['password']) : '';
    $confirm_password = isset($_POST['confirm_password']) ? sanitize_input($_POST['confirm_password']) : '';

    if (empty($username)) {
        $errors[] = "Username is required.";
    } elseif (strlen($username) < 3 || strlen($username) > 255) {
        $errors[] = "Username must be between 3 and 255 characters.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if (empty($confirm_password)) {
        $errors[] = "Confirm Password is required.";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);

        if ($conn->connect_error) {
            error_log("Connection failed: " . $conn->connect_error);
            $errors[] = "An unexpected error occurred. Please try again later.";
        } else {
            $stmt = $conn->prepare("SELECT name FROM user WHERE name = ?");
            if ($stmt) {
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $errors[] = "Username is already taken. Please choose another.";
                }
                $stmt->close();
            } else {
                error_log("Prepare failed: " . $conn->error);
                $errors[] = "An unexpected error occurred. Please try again later.";
            }

            if (empty($errors)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $insert_stmt = $conn->prepare("INSERT INTO user (name, password) VALUES (?, ?)");
                if ($insert_stmt) {
                    $insert_stmt->bind_param("ss", $username, $hashed_password);
                    if ($insert_stmt->execute()) {
                        $success = "Registration successful! You can now log in.";
                        $insert_stmt->close();

                        $_SESSION['success_message'] = $success;
                    } else {
                        error_log("Execute failed: " . $insert_stmt->error);
                        $errors[] = "Registration failed. Please try again.";
                        $insert_stmt->close();
                    }
                } else {
                    error_log("Prepare failed: " . $conn->error);
                    $errors[] = "An unexpected error occurred. Please try again later.";
                }
            }

            $conn->close();
        }
    }
} 

include "../components/register_page.php"
?>
