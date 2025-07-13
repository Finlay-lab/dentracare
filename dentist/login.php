<?php
/**
 * ==========================================
 * DENTIST LOGIN PAGE
 * ==========================================
 * 
 * This file handles dentist authentication and login functionality.
 * It validates dentist credentials against the database and creates
 * a session for authenticated dentists.
 * 
 * Features:
 * - Email and password validation
 * - Secure password verification using password_verify()
 * - Session management for logged-in dentists
 * - Error handling and user feedback
 */

// Start session to manage user login state
session_start();

// Include database connection
require_once(__DIR__ . '/../config/db.php');

// Initialize errors array to store validation messages
$errors = [];

// ==========================================
// LOGIN FORM PROCESSING
// ==========================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize user input to prevent SQL injection
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Validate that both fields are provided
    if (empty($email) || empty($password)) {
        $errors[] = "Both fields are required.";
    } else {
        // Prepare SQL statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, name, password FROM dentists WHERE email = ?");
        if (!$stmt) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        
        // Bind email parameter to prepared statement
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        // Check if dentist exists in database
        if ($stmt->num_rows == 1) {
            // Bind result variables to fetch dentist data
            $stmt->bind_result($id, $name, $hashed_password);
            $stmt->fetch();
            
            // Verify password using PHP's built-in password_verify function
            if (password_verify($password, $hashed_password)) {
                // ==========================================
                // LOGIN SUCCESSFUL - Create session
                // ==========================================
                
                // Store dentist information in session variables
                $_SESSION['dentist_id'] = $id;
                $_SESSION['dentist_name'] = $name;
                
                // Redirect to dentist dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                // Password verification failed
                $errors[] = "Incorrect password.";
            }
        } else {
            // No dentist found with provided email
            $errors[] = "No account found with that email.";
        }
        
        // Close prepared statement
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <!-- Page title and meta information -->
    <title>Dentist Login</title>
    
    <!-- External CSS files for styling -->
    <link rel="stylesheet" href="../patient/assets/css/style.css">
    <link rel="stylesheet" href="../patient/assets/css/logo.css">
    
    <!-- Custom styling for dentist login page -->
    <style>
        /* ==========================================
        DENTIST LOGIN CONTAINER STYLING
        ========================================== */
        .dentist-login-container {
            max-width: 400px;
            margin: 60px auto;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.07);
            padding: 36px 32px;
        }
        
        .dentist-login-container h2 {
            color: #00897b; /* Teal color for dentist theme */
            text-align: center;
            margin-bottom: 28px;
        }
        
        .dentist-login-container label {
            display: block;
            margin-top: 18px;
            font-weight: 500;
        }
        
        .dentist-login-container input[type="email"],
        .dentist-login-container input[type="password"] {
            width: 100%;
            padding: 8px 10px;
            margin-top: 6px;
            border: 1px solid #bdbdbd;
            border-radius: 6px;
            font-size: 1rem;
        }
        
        .dentist-login-container button[type="submit"] {
            margin-top: 28px;
            width: 100%;
            padding: 10px 0;
            background: #00897b; /* Teal background */
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .dentist-login-container button[type="submit"]:hover {
            background: #00695c; /* Darker teal on hover */
        }
        
        /* Error message styling */
        .error {
            background: #ffebee;
            color: #c62828;
            padding: 10px 16px;
            border-radius: 6px;
            margin-bottom: 16px;
            text-align: center;
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
    DENTIST LOGIN FORM CONTAINER
    ========================================== -->
    <div class="dentist-login-container">
        <h2>Dentist Login</h2>
        
        <!-- Display error messages if any -->
        <?php
        if (!empty($errors)) {
            echo "<div class='error'>" . implode("<br>", array_map('htmlspecialchars', $errors)) . "</div>";
        }
        ?>
        
        <!-- Login form with POST method -->
        <form method="POST" action="">
            <label>Email:</label>
            <input type="email" name="email" required>
            
            <label>Password:</label>
            <input type="password" name="password" required>
            
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>