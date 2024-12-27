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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="../css/theme.css">
    <link rel="stylesheet" href="../css/navbar.css">
</head>
<body>
    <?php include '../components/header.php'; ?>

    <div class="container">
        <h1>Register</h1>

        <!-- Display Success Message -->
        <?php if ($success): ?>
            <div class="success-message">
                <?= $success; ?>
            </div>
        <?php endif; ?>

        <!-- Display Error Messages -->
        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?= isset($name) ? htmlspecialchars($name) : ''; ?>" required>
            </div>

            <!-- Additional fields like password can be added here -->

            <input type="submit" value="Register">
        </form>
    </div>
</body>
</html>
