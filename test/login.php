<?php
// Start the session
session_start();

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the username from the form
    $username = htmlspecialchars($_POST['username']); // Sanitize input

    // Store the username in the session
    $_SESSION['username'] = $username;

    // Redirect to another page or display a success message
    header('Location: welcome.php'); // Redirect to welcome page
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Username</title>
</head>
<body>
    <h1>Set Username</h1>
    <form method="POST">
        <label for="username">Enter your username:</label>
        <input type="text" id="username" name="username" required>
        <button type="submit">Submit</button>
    </form>
</body>
</html>
