<?php
require "constants.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST['username']); // Sanitize input

    $_SESSION['username'] = $username;

    header('Location: user_profile.php?user='.$username);
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
    <link rel="stylesheet" href="../css/navbar.css">
</head>
<body>
    <header>
        <div class="navbar">
            <div class="title">BinCoin</div>
            <div class="nav-buttons">
                <a href="list_coins.php">Home</a>
                <a href="trade.php">Trade</a>
                <a href="user_profile.php?user=<?php echo htmlspecialchars($CURRENTUSER); ?>">Profile</a>
            </div>
        </div>
    </header>
    <h1>Set Username</h1>
    <form method="POST">
        <label for="username">Enter your username:</label>
        <input type="text" id="username" name="username" required>
        <button type="submit">Submit</button>
    </form>
</body>
</html>
