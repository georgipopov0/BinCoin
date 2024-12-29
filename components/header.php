<?php
// header.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>
<header>
    <div class="navbar">
        <div class="title">BinCoin</div>
        <div class="nav-buttons">
            <a href="dashboard.php">Home</a>
            <a href="collections.php">Collections</a>
            <a href="trade.php">Trade</a>
            <a href="user_profile.php?user=<?= htmlspecialchars($_SESSION['username']); ?>">Profile</a>
            <span class="username"><?= htmlspecialchars($_SESSION['username']); ?></span>
            <form action="logout.php" method="POST" style="display: inline;">
                <button type="submit" class="logout-button">Logout</button>
            </form>
        </div>
    </div>
</header>
