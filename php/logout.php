<?php
require "constants.php";
session_start();

session_unset();
session_destroy();
$_SESSION['username'] = "";
header('Location: login.php');
exit;
?>
