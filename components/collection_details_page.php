<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Collection Details - <?= htmlspecialchars($collection['name']); ?></title>
    <link rel="stylesheet" href="../css/theme.css">
    <link rel="stylesheet" href="../css/collections.css">
    <style>
        .collection-details-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .collection-header {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        .collection-header h1 {
            margin-bottom: 10px;
        }

        .collection-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-bottom: 30px;
        }

        .collection-meta div {
            flex: 1 1 200px;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .collection-meta div h3 {
            margin-top: 0;
            color: #555;
        }

        .collection-meta div p {
            margin: 5px 0 0 0;
            color: #777;
        }

        .collection-tags {
            text-align: center;
            margin-bottom: 30px;
        }

        .collection-tags .tag {
            display: inline-block;
            background-color: #007BFF;
            color: #fff;
            padding: 5px 10px;
            margin: 5px;
            border-radius: 4px;
            font-size: 14px;
        }

        .allowed-users {
            margin-top: 20px;
            padding: 15px;
            background-color: #f1f1f1;
            border-radius: 6px;
        }

        .allowed-users h3 {
            margin-top: 0;
            color: #555;
        }

        .allowed-users ul {
            list-style: none;
            padding: 0;
        }

        .allowed-users ul li {
            padding: 5px 0;
            border-bottom: 1px solid #ddd;
        }

        .allowed-users ul li:last-child {
            border-bottom: none;
        }

        .coins-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .coin-card {
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 15px;
            background-color: #fafafa;
            transition: box-shadow 0.3s;
        }

        .coin-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .coin-card img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
        }

        .coin-details {
            margin-top: 10px;
        }

        .coin-details p {
            margin: 5px 0;
            color: #555;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 30px;
            gap: 5px;
        }

        .pagination a,
        .pagination span {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
            transition: background-color 0.3s;
        }

        .pagination a:hover {
            background-color: #f0f0f0;
        }

        .pagination .current {
            background-color: #007BFF;
            color: #fff;
            border-color: #007BFF;
        }

        .pagination .disabled {
            color: #999;
            border-color: #ccc;
            cursor: not-allowed;
        }

        @media (max-width: 768px) {
            .collection-meta {
                flex-direction: column;
                align-items: center;
            }

            .collection-meta div {
                flex: 1 1 100%;
                max-width: 400px;
            }

            .coins-list {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
        }

        .edit-button {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #ffc107;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .edit-button:hover {
            background-color: #e0a800;
        }
    </style>
</head>

<body>
    <?php include '../components/header.php'; ?>

    <div class="collection-details-container">
        <div class="collection-header">
            <h1><?= htmlspecialchars($collection['name']); ?></h1>
            <p>Owned by: <?= htmlspecialchars($collection['user_name']); ?></p>
            <p>Access Level: <?= ucfirst(htmlspecialchars($collection['access'])); ?></p>

            <?php
            if ($collection['user_name'] === $_SESSION['username']) {
                echo '<a href="edit_collection.php?collection_id=' . urlencode($collection['id']) . '" class="edit-button">Edit Collection</a>';
            }
            ?>
        </div>

        <?php if (!empty($tags)): ?>
            <div class="collection-tags">
                <?php foreach ($tags as $tag): ?>
                    <span class="tag"><?= htmlspecialchars($tag['name']); ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($collection['access'] === 'protected' && !empty($allowed_users)): ?>
            <div class="allowed-users">
                <h3>Allowed Users</h3>
                <ul>
                    <?php foreach ($allowed_users as $user): ?>
                        <li><?= htmlspecialchars($user); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php elseif ($collection['access'] === 'protected'): ?>
            <div class="allowed-users">
                <h3>Allowed Users</h3>
                <p>No users have been granted access to this collection.</p>
            </div>
        <?php endif; ?>

        <?php if (!empty($periods)): ?>
            <div class="collection-meta">
                <div>
                    <h3>Associated Periods</h3>
                    <?php foreach ($periods as $period): ?>
                        <p><?= htmlspecialchars($period['name']) . " (" . htmlspecialchars($period['country']) . ", " . htmlspecialchars($period['from']) . " - " . htmlspecialchars($period['to']) . ")"; ?></p>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (count($coins) > 0): ?>
            <h2>Coins in this Collection</h2>
            <div class="coins-list">
                <?php foreach ($coins as $coin): ?>
                    <div class="coin-card">
                        <?php
                        $front_image = $coin['front_path'];
                        ?>
                        <img src="<?= htmlspecialchars($front_image); ?>" alt="Front Image of <?= htmlspecialchars($coin['country']); ?> Coin">

                        <?php
                        $back_image =$coin['back_path'];
                        ?>
                        <img src="<?= htmlspecialchars($back_image); ?>" alt="Back Image of <?= htmlspecialchars($coin['country']); ?> Coin">

                        <div class="coin-details">
                            <p><strong>Country:</strong> <?= htmlspecialchars($coin['country']); ?></p>
                            <p><strong>Year:</strong> <?= htmlspecialchars($coin['year']); ?></p>
                            <p><strong>Cost:</strong> <?= htmlspecialchars($coin['currency']) . " " . htmlspecialchars(number_format($coin['cost'], 2)); ?></p>
                            <p><strong>Value:</strong> <?= htmlspecialchars($coin['currency']) . " " . htmlspecialchars(number_format($coin['value'], 2)); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No coins found in this collection.</p>
        <?php endif; ?>
    </div>

    <?php // include 'components/footer.php'; ?>

</body>

</html>