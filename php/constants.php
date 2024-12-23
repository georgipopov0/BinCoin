<?php
const SERVERNAME = "localhost";
const USERNAME = "root";
const PASSWORD = "";
const DBNAME = "bincoin";
$CURRENTUSER = "Alice";
$NAVBAR = <<<EOD
<header>
    <div class="navbar">
        <div class="title">BinCoin</div>
        <div class="nav-buttons">
            <a href="list_coins.php">Home</a>
            <a href="trade.php">Trade</a>
            <a href="user_profile.php?user=<?php echo htmlspecialchars($CURRENTUSER); ?>">Profile</a>
        </div>
    </div>
</header>
EOD;
?>