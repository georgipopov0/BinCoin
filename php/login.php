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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../css/theme.css">
    <link rel="stylesheet" href="../css/navbar.css">
</head>
<body>
    <?php include '../components/header.php'; ?>

    <div class="container">
        <h1>Login</h1>

        <!-- Display Error Messages -->
        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?= isset($name) ? htmlspecialchars($name) : ''; ?>" required>
            </div>

            <!-- Additional fields like password can be added here -->

            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>