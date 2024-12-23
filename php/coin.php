<?php
require 'constants.php';

$coin_id = isset($_GET['coin_id']) ? intval($_GET['coin_id']) : 0;

if ($coin_id <= 0) {
    die("Invalid coin ID.");
}

$conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT 
            cost, 
            value, 
            currency, 
            front_path, 
            back_path, 
            country, 
            year 
        FROM coin 
        WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $coin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $coin = $result->fetch_assoc();
} else {
    die("Coin not found.");
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coin Details</title>
    <link rel="stylesheet" href="../css/theme.css">
    <link rel="stylesheet" href="../css/coin.css">
    <link rel="stylesheet" href="../css/navbar.css">
</head>

<body>
    <?php include '../components/header.php'; ?>

    <div class="coin-container">
        <h1>Coin Details</h1>
        <div class="coin-details">
            <div class="image-section">
                <img src="<?= htmlspecialchars($coin['front_path']); ?>" alt="Coin Front" />
                <img src="<?= htmlspecialchars($coin['back_path']); ?>" alt="Coin Back" />
            </div>
            <div class="info-section">
                <p><strong>Cost:</strong> $<?= htmlspecialchars($coin['cost']); ?></p>
                <p><strong>Value:</strong> $<?= htmlspecialchars($coin['value']); ?></p>
                <p><strong>Currency:</strong> <?= htmlspecialchars($coin['currency']); ?></p>
                <p><strong>Country:</strong> <?= htmlspecialchars($coin['country']); ?></p>
                <p><strong>Year:</strong> <?= htmlspecialchars($coin['year']); ?></p>
            </div>
        </div>
    </div>
</body>

</html>