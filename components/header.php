<header>
    <div class="navbar">
        <div class="title">BinCoin</div>
        <div class="nav-buttons">
            <a href="dashboard.php">Home</a>
            <a href="collections.php">Collections</a>
            <a href="trade.php">Trade</a>
            <a href="user_profile.php?user=<?php echo htmlspecialchars($_SESSION['username']); ?>">Profile</a>
        </div>
    </div>
</header>