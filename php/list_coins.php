<?php
require 'constants.php';

$conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, country, year, currency, value FROM coin";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $coins = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $coins = [];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Coins</title>
    <link rel="stylesheet" href="../css/list_coins.css">
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
    <div class="container">
        <h1>Available Coins</h1>
        <?php if (count($coins) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Country</th>
                        <th>Year</th>
                        <th>Currency</th>
                        <th>Value</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($coins as $coin): ?>
                        <tr>
                            <td><?= htmlspecialchars($coin['id']); ?></td>
                            <td><?= htmlspecialchars($coin['country']); ?></td>
                            <td><?= htmlspecialchars($coin['year']); ?></td>
                            <td><?= htmlspecialchars($coin['currency']); ?></td>
                            <td><?= htmlspecialchars($coin['value']); ?></td>
                            <td>
                                <a href="coin.php?coin_id=<?= htmlspecialchars($coin['id']); ?>">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No coins found in the database.</p>
        <?php endif; ?>
    </div>
</body>
</html>
