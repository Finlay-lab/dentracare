<?php
/**
 * ==========================================
 * ADMIN LOGOUT SCRIPT
 * ==========================================
 * 
 * This script handles logging out an administrator by destroying their session
 * and redirecting them to the login page.
 */

// Start the session to access session variables
session_start();

// Remove all session variables
session_unset();

// Destroy the session completely
session_destroy();

// Redirect the user to the login page
header("Location: login.php");
exit();
?>