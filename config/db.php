<?php
/**
 * ==========================================
 * DATABASE CONFIGURATION FILE
 * ==========================================
 * 
 * This file contains the database connection settings for the Dentracare system.
 * It establishes a connection to the MySQL database using mysqli.
 * 
 * IMPORTANT: Update these credentials according to your database setup
 */

// Database connection parameters
$host = "localhost";        // Database server (usually localhost for local development)
$user = "root";            // Database username (default XAMPP username)
$pass = "";                // Database password (empty for default XAMPP setup)
$db = "dentracare";        // Database name - Make sure this matches your database name

// Create new mysqli connection object
$conn = new mysqli($host, $user, $pass, $db);

// Check if connection was successful
if ($conn->connect_error) {
    // If connection fails, display error and stop execution
    die("Connection failed: " . $conn->connect_error);
}

// Connection successful - $conn object is now available for database operations
// throughout the application
?>