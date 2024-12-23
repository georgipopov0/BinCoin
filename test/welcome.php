<?php
// Start the session
session_start();

// Check if the username is set in the session
if (!isset($_SESSION['username'])) {
    echo "No username found in the session. <a href='index.php'>Set Username</a>";
    exit;
}

// Display the stored username
$username = htmlspecialchars($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
</head>
<body>
    <h1>Welcome, <?php echo $username; ?>!</h1>
    <a href="logout.php">Logout</a>
</body>
</html>
