<!DOCTYPE html>
<html>
<head>
    <!-- ==========================================
    DENTRACARE LANDING PAGE - MAIN ENTRY POINT
    ========================================== -->
    
    <!-- Page Title and Meta Information -->
    <title>Welcome to Dentracare</title>
    
    <!-- External CSS Files - Styling for the dental care platform -->
    <link rel="stylesheet" href="patient/assets/css/style.css">
    <link rel="stylesheet" href="patient/assets/css/logo.css">
    
    <!-- Inline CSS Styles - Custom styling for the landing page -->
    <style>
        /* ==========================================
        GLOBAL BODY STYLING
        ========================================== */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* Background with overlay effect - dental hero image with dark overlay */
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
                        url('patient/assets/images/dental-hero.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            color: white;
        }

        /* ==========================================
        NAVIGATION BAR STYLING
        ========================================== */
        .navbar {
            background: rgba(255, 255, 255, 0.95); /* Semi-transparent white background */
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }

        /* Logo styling for Dentracare branding */
        .logo {
            display: flex;
            align-items: center;
            color: #2c3e50;
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
        }

        .logo img {
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }

        /* Navigation menu styling */
        .nav-menu {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            gap: 30px;
        }

        .nav-menu a {
            color: #2c3e50;
            text-decoration: none;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 5px;
            transition: all 0.3s ease; /* Smooth hover transitions */
        }

        .nav-menu a:hover {
            background: #3498db;
            color: white;
        }

        /* ==========================================
        HERO SECTION STYLING - Main landing content
        ========================================== */
        .hero-section {
            text-align: center;
            padding: 100px 20px;
            max-width: 800px;
            margin: 0 auto;
        }

        .hero-title {
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5); /* Text shadow for readability */
        }

        .hero-subtitle {
            font-size: 20px;
            margin-bottom: 40px;
            opacity: 0.9;
            line-height: 1.6;
        }

        /* ==========================================
        CALL-TO-ACTION BUTTONS STYLING
        ========================================== */
        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .cta-btn {
            padding: 15px 30px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-block;
        }

        /* Primary button - Blue theme for main actions */
        .cta-primary {
            background: #3498db;
            color: white;
        }

        .cta-primary:hover {
            background: #217dbb;
            transform: translateY(-2px); /* Subtle lift effect on hover */
        }

        /* Secondary button - White theme for secondary actions */
        .cta-secondary {
            background: rgba(255, 255, 255, 0.9);
            color: #2c3e50;
        }

        .cta-secondary:hover {
            background: white;
            transform: translateY(-2px);
        }

        /* ==========================================
        ABOUT SECTION STYLING - Information section
        ========================================== */
        .about-section {
            background: rgba(255, 255, 255, 0.95);
            color: #2c3e50;
            padding: 60px 20px;
            margin-top: 50px;
        }

        .about-container {
            max-width: 1000px;
            margin: 0 auto;
            text-align: center;
        }

        .about-title {
            font-size: 36px;
            margin-bottom: 30px;
            color: #2c3e50;
        }

        .about-content {
            font-size: 18px;
            line-height: 1.8;
            max-width: 800px;
            margin: 0 auto;
        }

        /* ==========================================
        RESPONSIVE DESIGN - Mobile optimization
        ========================================== */
        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                gap: 15px;
            }

            .nav-menu {
                gap: 15px;
            }

            .hero-title {
                font-size: 36px;
            }

            .hero-subtitle {
                font-size: 18px;
            }

            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }

            .cta-btn {
                width: 200px;
            }
        }
    </style>
</head>
<body>
    <!-- ==========================================
    NAVIGATION BAR - Main navigation with logo and menu
    ========================================== -->
    <nav class="navbar">
        <div class="nav-container">
            <!-- Dentracare Logo and Branding -->
            <a href="index.php" class="dentracare-logo">
                <div class="logo-icon"></div>
                <div>
                    <div class="logo-text">Dentracare</div>
                    <div class="logo-subtitle">Dental Management Platform</div>
                </div>
            </a>
            
            <!-- Navigation Menu - Links to different user portals -->
            <ul class="nav-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="#about">About Us</a></li>
                <li><a href="patient/login.php">Patient Login</a></li>
                <li><a href="dentist/login.php">Dentist Login</a></li>
                <li><a href="admin/login.php">Admin Login</a></li>
            </ul>
        </div>
    </nav>

    <!-- ==========================================
    HERO SECTION - Main landing content and call-to-action
    ========================================== -->
    <div class="hero-section">
        <h1 class="hero-title">Welcome to Dentracare</h1>
        <p class="hero-subtitle">
            Your trusted partner in dental care management. We provide comprehensive solutions 
            for patients, dentists, and healthcare administrators to streamline dental care 
            and improve patient outcomes.
        </p>
        
        <!-- Call-to-Action Buttons - Direct users to register or learn more -->
        <div class="cta-buttons">
            <a href="patient/register.php" class="cta-btn cta-primary">Register as Patient</a>
            <a href="#about" class="cta-btn cta-secondary">Learn More</a>
        </div>
    </div>

    <!-- ==========================================
    ABOUT SECTION - Platform information and features
    ========================================== -->
    <div id="about" class="about-section">
        <div class="about-container">
            <h2 class="about-title">About Us</h2>
            <div class="about-content">
                <p>
                    <strong>Dentracare</strong> is a modern dental management platform designed to streamline 
                    appointments, patient records, and treatment tracking for clinics and patients alike. 
                    Our mission is to make dental care simple, organized, and accessible for everyone.
                </p>
                <p>
                    We provide comprehensive solutions for:
                </p>
                <!-- Feature list highlighting different user types -->
                <ul style="text-align: left; max-width: 600px; margin: 20px auto;">
                    <li><strong>Patients:</strong> Easy appointment booking, medical history tracking, and treatment summaries</li>
                    <li><strong>Dentists:</strong> Patient management, diagnosis tools, and treatment planning</li>
                    <li><strong>Administrators:</strong> User management, activity monitoring, and system oversight</li>
                </ul>
                <p>
                    Join thousands of healthcare professionals and patients who trust Dentracare 
                    for their dental care management needs.
                </p>
            </div>
        </div>
    </div>
    <footer>
      <div style="text-align:center; margin-top:40px; padding:20px 0; background:#f5f5f5; border-top:1px solid #e0e0e0;">
        <strong>Contact Us:</strong> dentracare@example.com | +123 456 7890
      </div>
    </footer>
</body>
</html>