<?php
// Start the session
session_start();

// Clear the session
session_unset();
session_destroy();

// Redirect back to the username form
header('Location: index.php');
exit;
?>
