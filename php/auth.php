<?php
// auth.php

// Start the session if it hasn't been started yet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in by verifying the existence of 'username' in the session
if (!isset($_SESSION['username'])) {
    // Optionally, store the current page to redirect after successful login
    $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];

    // Redirect to the login page
    header("Location: login.php");
    exit();
}
?>
