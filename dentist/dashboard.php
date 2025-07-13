<?php
/**
 * ==========================================
 * DENTIST DASHBOARD PAGE
 * ==========================================
 * 
 * This page serves as the main dashboard for logged-in dentists.
 * It provides navigation to various dentist features and displays
 * a welcome message with professional styling.
 */

// Start session and check if dentist is logged in
session_start();
if (!isset($_SESSION['dentist_id'])) {
    // Redirect to login if not authenticated
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <!-- Page title and meta information -->
    <title>Dentist Dashboard</title>
    
    <!-- External CSS files for styling -->
    <link rel="stylesheet" href="../patient/assets/css/style.css">
    <link rel="stylesheet" href="../patient/assets/css/logo.css">
    
    <!-- Custom styling for dentist dashboard -->
    <style>
        /* ==========================================
        DENTIST CONTAINER STYLING
        ========================================== */
        .dentist-container {
            max-width: 950px;
            margin: 40px auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.07);
            padding: 32px 28px;
        }
        
        /* ==========================================
        DENTIST BANNER IMAGE STYLING
        ========================================== */
        .dentist-banner {
            width: 100%;
            max-width: 900px;
            height: 220px;
            object-fit: cover;
            display: block;
            margin: 0 auto 30px auto;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.10);
        }
        
        /* ==========================================
        DENTIST NAVIGATION STYLING
        ========================================== */
        .dentist-nav {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .dentist-nav a {
            display: inline-block;
            margin: 0 10px;
            padding: 8px 18px;
            color: #00897b; /* Teal color for dentist theme */
            text-decoration: none;
            font-weight: 500;
            border-radius: 6px;
            transition: background 0.2s, color 0.2s;
        }
        
        .dentist-nav a:hover, .dentist-nav a.active {
            background: #e0f2f1; /* Light teal background on hover/active */
            color: #004d40; /* Darker teal text */
        }
        
        /* ==========================================
        DENTIST PROFILE PHOTO STYLING
        ========================================== */
        .dentist-profile-photo {
            width: 80px;
            height: 80px;
            border-radius: 50%; /* Circular profile photo */
            object-fit: cover;
            margin-bottom: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
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
    MAIN DENTIST DASHBOARD CONTAINER
    ========================================== -->
    <div class="dentist-container">
        <!-- Dental clinic banner image -->
        <img src="../patient/assets/images/dental-hero.jpg" alt="Dental Clinic" class="dentist-banner">
        
        <!-- Navigation menu for dentist features -->
        <nav class="dentist-nav">
            <a href="dashboard.php" class="active">Dashboard</a> |
            <a href="view_patients.php">View Patients</a> |
            <a href="add_diagnosis.php">Add Diagnosis</a> |
            <a href="logout.php">Logout</a>
        </nav>
        
        <!-- Display dentist profile photo if available -->
        <?php if (!empty($_SESSION['dentist_photo'])): ?>
            <img src="../uploads/<?php echo htmlspecialchars($_SESSION['dentist_photo']); ?>" alt="Profile Photo" class="dentist-profile-photo">
        <?php endif; ?>
        
        <!-- Main content area -->
        <main>
            <h2>Welcome, Dr. <?php echo htmlspecialchars($_SESSION['dentist_name']); ?>!</h2>
            <p>This is your dentist dashboard.</p>
        </main>
    </div>
</body>
</html>