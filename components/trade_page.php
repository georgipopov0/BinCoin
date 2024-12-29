
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Trade Entries</title>
	<link rel="stylesheet" href="../css/trade.css">
	<link rel="stylesheet" href="../css/theme.css">
	<link rel="stylesheet" href="../css/navbar.css">
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
							<a href="?action=confirm&id=<?= htmlspecialchars($trade['id']) ?>">
								<button class="confirm">Confirm</button>
							</a>
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
</body>
</html>