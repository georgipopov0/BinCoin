<?php

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Collections</title>
    <link rel="stylesheet" href="../css/theme.css">
    <link rel="stylesheet" href="../css/collections.css">


</head>

<body>

    <?php include '../components/header.php'; ?>

    <div class="collections-container">
        <div class="toggle-container">
            <form method="GET" action="collections.php">
                <input type="hidden" name="search" value="<?= htmlspecialchars($search); ?>">
                <input type="hidden" name="page" value="1"> <!-- Reset to first page on toggle -->
                <input type="checkbox" id="show_my_collections" name="show_my_collections" value="1"
                    <?= $show_my_collections ? 'checked' : ''; ?> onchange="this.form.submit()">
                <label for="show_my_collections">Only my collections</label>
            </form>
        </div>

        <?php if (!empty($search)): ?>
            <h2>Search Results for "<?= htmlspecialchars($search); ?>"</h2>
        <?php else: ?>
            <h2>Collections</h2>
        <?php endif; ?>

        <?php if ($show_my_collections): ?>
            <h3>Your Collections</h3>
        <?php endif; ?>

        <?php if (count($collections) > 0): ?>
            <div class="collections-list">
                <?php foreach ($collections as $collection): ?>
                    <div class="collection-card">
                        <h3><?= htmlspecialchars($collection['name']); ?></h3>
                        <p><strong>Owner:</strong> <?= htmlspecialchars($collection['user_name']); ?></p>
                        <p><strong>Access:</strong> <?= ucfirst(htmlspecialchars($collection['access'])); ?></p>
                        <a href="collection_details.php?collection_id=<?= urlencode($collection['id']); ?>"
                            class="view-button">View Details</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No collections found.</p>
        <?php endif; ?>

        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php
                if ($page > 1) {
                    echo '<a href="?page=' . ($page - 1) . '&search=' . urlencode($search) . '&show_my_collections=' . ($show_my_collections ? '1' : '0') . '">Previous</a>';
                } else {
                    echo '<span class="disabled">Previous</span>';
                }

                for ($i = 1; $i <= $total_pages; $i++) {
                    if ($i == $page) {
                        echo '<span class="current">' . $i . '</span>';
                    } else {
                        echo '<a href="?page=' . $i . '&search=' . urlencode($search) . '&show_my_collections=' . ($show_my_collections ? '1' : '0') . '">' . $i . '</a>';
                    }
                }

                if ($page < $total_pages) {
                    echo '<a href="?page=' . ($page + 1) . '&search=' . urlencode($search) . '&show_my_collections=' . ($show_my_collections ? '1' : '0') . '">Next</a>';
                } else {
                    echo '<span class="disabled">Next</span>';
                }
                ?>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>