<?php
require 'constants.php';

$conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userName = isset($_GET['user']) ? $_GET['user'] : 'Alice'; // Default to Alice
$userQuery = $conn->prepare("SELECT * FROM `user` WHERE `name` = ?");
$userQuery->bind_param("s", $userName);
$userQuery->execute();
$userResult = $userQuery->get_result();
$user = $userResult->fetch_assoc();

if (!$user) {
    die("User not found.");
}

$collectionsQuery = $conn->prepare("
    SELECT cc.id AS collection_id, cc.access, ct.name AS tag 
    FROM `coin_collection` cc 
    LEFT JOIN `collection_tag` ct ON cc.id = ct.collection_id 
    WHERE cc.user_name = ?
");
$collectionsQuery->bind_param("s", $userName);
$collectionsQuery->execute();
$collectionsResult = $collectionsQuery->get_result();

$collections = [];
while ($row = $collectionsResult->fetch_assoc()) {
    $collections[$row['collection_id']]['access'] = $row['access'];
    $collections[$row['collection_id']]['tags'][] = $row['tag'];
}

foreach ($collections as $collectionId => &$collection) {
    $coinsQuery = $conn->prepare("
        SELECT c.id AS coin_id, c.cost, c.value, c.currency, c.country, c.year, p.name AS period 
        FROM `coin` c 
        LEFT JOIN `coin_period` cp ON c.id = cp.coin_id 
        LEFT JOIN `period` p ON cp.period_id = p.id 
        WHERE c.coin_collection_id = ?
    ");
    $coinsQuery->bind_param("i", $collectionId);
    $coinsQuery->execute();
    $coinsResult = $coinsQuery->get_result();

    $collection['coins'] = [];
    while ($row = $coinsResult->fetch_assoc()) {
        $collection['coins'][] = $row;
    }
}

if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header("Content-Type: text/csv");
    header("Content-Disposition: attachment; filename=coins.csv");
    $output = fopen("php://output", "w");
    fputcsv($output, ['ID', 'Cost', 'Value', 'Currency', 'Front Path', 'Back Path', 'Country', 'Year', 'Coin Collection ID']);

    $result = $conn->query("SELECT * FROM `coin`");
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit;
}

// Export to XLS
if (isset($_GET['export']) && $_GET['export'] === 'xls') {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=coins.xls");

    echo "<table border='1'>";
    echo "<tr style='background-color: #007BFF; color: #FFFFFF; font-weight: bold;'>";
    echo "<th>ID</th><th>Cost</th><th>Value</th><th>Currency</th><th>Front Path</th><th>Back Path</th><th>Country</th><th>Year</th><th>Coin Collection ID</th>";
    echo "</tr>";

    $result = $conn->query("SELECT * FROM `coin`");
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['cost']}</td>";
        echo "<td>{$row['value']}</td>";
        echo "<td>{$row['currency']}</td>";
        echo "<td>{$row['front_path']}</td>";
        echo "<td>{$row['back_path']}</td>";
        echo "<td>{$row['country']}</td>";
        echo "<td>{$row['year']}</td>";
        echo "<td>{$row['coin_collection_id']}</td>";
        echo "</tr>";
    }

    echo "</table>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="../css/theme.css">
    <link rel="stylesheet" href="../css/user_profile.css">
    <link rel="stylesheet" href="../css/navbar.css">
</head>

<body>
    <?php include '../components/header.php'; ?>

    <div class="profile-container">
        <h2>User: <?php echo htmlspecialchars($user['name']); ?></h2>
        <div class="user-details">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
        </div>
        <?php foreach ($collections as $collectionId => $collection): ?>
            <div class="collection">
                <h3>Collection ID: <?php echo htmlspecialchars($collectionId); ?></h3>
                <p><strong>Access:</strong> <?php echo htmlspecialchars($collection['access']); ?></p>
                <div class="tags">
                    <?php foreach ($collection['tags'] as $tag): ?>
                        <span class="tag"><?php echo htmlspecialchars($tag); ?></span>
                    <?php endforeach; ?>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Coin ID</th>
                                <th>Cost</th>
                                <th>Value</th>
                                <th>Country</th>
                                <th>Year</th>
                                <th>Period</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($collection['coins'] as $coin): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($coin['coin_id']); ?></td>
                                    <td><?php echo htmlspecialchars($coin['cost']); ?></td>
                                    <td><?php echo htmlspecialchars($coin['value']); ?></td>
                                    <td><?php echo htmlspecialchars($coin['country']); ?></td>
                                    <td><?php echo htmlspecialchars($coin['year']); ?></td>
                                    <td><?php echo htmlspecialchars($coin['period']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="export-container">
        <div class="dropdown">
            <button class="export-btn">Export</button>
            <div class="dropdown-content">
                <a href="user_profile.php?export=csv">To .CSV</a>
                <a href="user_profile.php?export=xls">To .XLS</a>
                </div>
            </div>
        </div>
        <a href="logout.php">Logout</a>
    </div>
</body>

</html>