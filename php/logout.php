<?php
require "constants.php";
session_start();

session_unset();
session_destroy();
$CURRENTUSER = "None";
header('Location: login.php');
exit;
?>
