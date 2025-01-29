<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="../css/theme.css">
    <link rel="stylesheet" href="../css/user_profile.css">
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
            <div class="add-coin">
                <a href="/php/add_coin.php" class="add-coin-btn">Add Coin</a>
            </div>

            <div class="dropdown">
                <button class="export-btn">Export</button>
                <div class="dropdown-content">
                    <a href="user_profile.php?export=csv">To .CSV</a>
                    <a href="user_profile.php?export=xls">To .XLS</a>
                </div>
            </div>
        </div>
        
    </div>
</body>

</html>
