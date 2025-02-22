<?php
session_start(); // Start the session before destroying it
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session
header("Location: index.php"); // Ensure proper redirection
exit();
?>
