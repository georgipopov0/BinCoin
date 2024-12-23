<?php
require "constants.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST['username']); // Sanitize input

    $_SESSION['username'] = $username;

    header('Location: user_profile.php?user=' . $username);
    $CURRENTUSER = $username;
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Username</title>
    <link rel="stylesheet" href="../css/theme.css">
    <link rel="stylesheet" href="../css/navbar.css">
</head>

<body>
    <?php include '../components/header.php'; ?>

    <h1>Set Username</h1>
    <form method="POST">
        <label for="username">Enter your username:</label>
        <input type="text" id="username" name="username" required>
        <button type="submit">Submit</button>
    </form>
</body>

</html>