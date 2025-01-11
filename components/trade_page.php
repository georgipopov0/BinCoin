<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Trade Entries</title>
	<link rel="stylesheet" href="../css/trade.css">
	<link rel="stylesheet" href="../css/theme.css">
	<!-- <link rel="stylesheet" href="../css/navbar.css"> -->
</head>
<body>
	<?php include '../components/header.php'; ?>
	<div class="container">
		<h1>Trade offers</h1>

		<div class="actions">
			<div class="toggle">
				<a href="?view=sent">
					<button class="<?= $view_type === 'sent' ? 'active' : '' ?>">Sent Offers</button>
				</a>
				<a href="?view=received">
					<button class="<?= $view_type === 'received' ? 'active' : '' ?>">Received Offers</button>
				</a>
			</div>
			<form method="GET" class="filter-form" onchange="this.submit();">
				<input type="hidden" name="view" value="<?= htmlspecialchars($view_type) ?>">
				<select name="status">
					<option value="">All</option>
					<option value="pending" <?= $filter_status === 'pending' ? 'selected' : '' ?>>Pending</option>
					<option value="completed" <?= $filter_status === 'completed' ? 'selected' : '' ?>>Completed</option>
					<option value="cancelled" <?= $filter_status === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
				</select>
			</form>
		</div>

		<table>
			<thead>
				<tr>
					<th>ID</th>
					<th>Seller Name</th>
					<th>Buyer Name</th>
					<th>Status</th>
					<th>Coin ID</th>
					<?php if ($view_type === 'received'): ?>
						<th>Actions</th>
					<?php endif; ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($trades as $trade): ?>
				<tr class="<?= $view_type === 'received' ? 'narrow' : '' ?>">
					<td><?= htmlspecialchars($trade['id']) ?></td>
					<td><?= htmlspecialchars($trade['seller_name']) ?></td>
					<td><?= htmlspecialchars($trade['buyer_name']) ?></td>
					<td><?= htmlspecialchars($trade['status']) ?></td>
					<td><?= htmlspecialchars($trade['coin_id']) ?></td>
					<?php if ($view_type === 'received'): ?>
						<td class="action-buttons">
							<a href="#" onclick="openCollectionPopup(<?= htmlspecialchars($trade['id']) ?>, <?= htmlspecialchars($trade['coin_id']) ?>)">
								<button class="confirm">Confirm</button>
							</a>
							<div id="collectionPopup" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2); z-index: 1000;">
								<h3>Select a Collection</h3>
								<form id="collectionForm" method="POST" action="handle_collection.php">
									<input type="hidden" name="trade_id" id="popupTradeId">
									<input type="hidden" name="coin_id" id="popupCoinId">
									<select name="collection_id" id="collectionSelect" required>
										<!-- Collections will be loaded dynamically -->
									</select>
									<br><br>
									<button type="submit">Confirm</button>
									<button type="button" onclick="closeCollectionPopup()">Cancel</button>
								</form>
							</div>
							<div id="popupBackdrop" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 999;" onclick="closeCollectionPopup()"></div>

							<a href="?action=cancel&id=<?= htmlspecialchars($trade['id']) ?>">
								<button class="cancel">Cancel</button>
							</a>
						</td>
					<?php endif; ?>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<script src="../js/trade.js"></script>
</body>
</html>