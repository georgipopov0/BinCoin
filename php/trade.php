<?php

require 'constants.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = "mysql:host=" . SERVERNAME;
$dbname = ";dbname=" . DBNAME;

try {
	$pdo = new PDO("$host$dbname", USERNAME, PASSWORD);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	die("Connection failed: " . $e->getMessage());
}

// Handle form submission for adding a new trade entry
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$seller_name = $_POST['seller_name'];
	$buyer_name = $_POST['buyer_name'];
	$status = 'pending';
	$coin_id = $_POST['coin_id'];

	$stmt = $pdo->prepare("INSERT INTO trade (seller_name, buyer_name, status, coin_id) VALUES (?, ?, ?, ?)");
	$stmt->execute([$seller_name, $buyer_name, $status, $coin_id]);

	// Refresh the page to show the new entry
	header("Location: " . $_SERVER['PHP_SELF']);
	exit;
}

$filter_status = $_GET['status'] ?? '';
$current_user = $_SESSION['username'];
$query = "SELECT * FROM trade";
$params = [];
if (!empty($filter_status)) {
	if ($filter_status === 'my_trades') {
		$query .= " WHERE seller_name = ? OR buyer_name = ?";
		$params[] = $current_user;
		$params[] = $current_user;
	} else {
		$query .= " WHERE status = ?";
		$params[] = $filter_status;
	}
}
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$trades = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Trade Entries</title>
	<link rel="stylesheet" href="../css/trade.css">
    <link rel="stylesheet" href="../css/theme.css">
    <link rel="stylesheet" href="../css/navbar.css">
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
</head>
<body>
    <?php include '../components/header.php'; ?>
	<div class="container">
		<h1>Trade Entries</h1>

		<div class="actions">
		<form method="GET" class="filter-form" onchange="this.submit();">
			<select name="status">
				<option value="">All</option>
				<option value="pending" <?= $filter_status === 'pending' ? 'selected' : '' ?>>Pending</option>
				<option value="completed" <?= $filter_status === 'completed' ? 'selected' : '' ?>>Completed</option>
				<option value="cancelled" <?= $filter_status === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
				<option value="my_trades" <?= $filter_status === 'my_trades' ? 'selected' : '' ?>>My Trades</option>
			</select>
		</form>
		<button onclick="document.getElementById('add-trade-form').scrollIntoView();">New Trade</button>
		</div>

		<table>
			<thead>
				<tr>
					<th>ID</th>
					<th>Seller Name</th>
					<th>Buyer Name</th>
					<th>Status</th>
					<th>Coin ID</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($trades as $trade): ?>
				<tr>
					<td><?= htmlspecialchars($trade['id']) ?></td>
					<td><?= htmlspecialchars($trade['seller_name']) ?></td>
					<td><?= htmlspecialchars($trade['buyer_name']) ?></td>
					<td><?= htmlspecialchars($trade['status']) ?></td>
					<td><?= htmlspecialchars($trade['coin_id']) ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

	</div>
    <div id="add-trade-form" class="form-container">
        <h2>Add New Trade</h2>
        <form method="POST">
            <label for="seller_name">Seller Name:</label>
            <input type="text" id="seller_name" name="seller_name" required>

            <label for="buyer_name">Buyer Name:</label>
            <input type="text" id="buyer_name" name="buyer_name" required>

            <label for="coin_id">Coin ID:</label>
            <input type="number" id="coin_id" name="coin_id" required>

            <button type="submit">Add Trade</button>
        </form>
    </div>
</body>
</html>
