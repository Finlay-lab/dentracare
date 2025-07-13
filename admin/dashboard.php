<?php
/**
 * ==========================================
 * ADMIN DASHBOARD PAGE
 * ==========================================
 * 
 * This page serves as the main dashboard for logged-in administrators.
 * It provides navigation to various admin features and displays
 * a welcome message with administrative controls.
 */

// Start session and check if admin is logged in
session_start();
if (!isset($_SESSION['admin_id'])) {
    // Redirect to login if not authenticated
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <!-- Page title and meta information -->
    <title>Admin Dashboard</title>
    
    <!-- External CSS files for styling -->
    <link rel="stylesheet" href="../patient/assets/css/style.css">
    <link rel="stylesheet" href="../patient/assets/css/logo.css">
    
    <!-- Custom styling for admin dashboard -->
    <style>
        /* ==========================================
        ADMIN DASHBOARD NAVIGATION STYLING
        ========================================== */
        .dashboard-nav {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- ==========================================
    HEADER SECTION - Dentracare branding
    ========================================== -->
    <div class="header">
        <a href="../index.php" class="dentracare-logo">
            <div class="logo-icon"></div>
            <div>
                <div class="logo-text">Dentracare</div>
                <div class="logo-subtitle">Dental Management Platform</div>
            </div>
        </a>
    </div>
    
    <!-- ==========================================
    MAIN ADMIN DASHBOARD CONTAINER
    ========================================== -->
    <div class="container">
        <!-- ==========================================
        ADMIN NAVIGATION MENU
        ========================================== -->
        <div class="dashboard-nav">
            <a href="dashboard.php">Dashboard</a> |
            <a href="manage_users.php">Manage Users</a> |
            <a href="add_user.php">Add User</a> |
            <a href="activity_logs.php">Activity Logs</a> |
            <a href="logout.php">Logout</a>
        </div>
        
        <!-- ==========================================
        WELCOME MESSAGE AND MAIN CONTENT
        ========================================== -->
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</h2>
        <p>This is your admin dashboard.</p>
    </div>
    
    <!-- JavaScript file for additional functionality -->
    <script src="assets/js/global.js"></script>
</body>
</html>