<?php
/**
 * ==========================================
 * ADMIN LOGIN PAGE
 * ==========================================
 * 
 * This file handles administrator authentication and login functionality.
 * It validates admin credentials against the database and creates
 * a session for authenticated administrators.
 * 
 * Features:
 * - Email and password validation
 * - Secure password verification using password_verify()
 * - Session management for logged-in administrators
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
        $stmt = $conn->prepare("SELECT id, name, password FROM admins WHERE email = ?");
        if (!$stmt) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        
        // Bind email parameter to prepared statement
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        // Check if admin exists in database
        if ($stmt->num_rows == 1) {
            // Bind result variables to fetch admin data
            $stmt->bind_result($id, $name, $hashed_password);
            $stmt->fetch();
            
            // Verify password using PHP's built-in password_verify function
            if (password_verify($password, $hashed_password)) {
                // ==========================================
                // LOGIN SUCCESSFUL - Create session
                // ==========================================
                
                // Store admin information in session variables
                $_SESSION['admin_id'] = $id;
                $_SESSION['admin_name'] = $name;
                
                // Redirect to admin dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                // Password verification failed
                $errors[] = "Incorrect password.";
            }
        } else {
            // No admin found with provided email
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
    <title>Admin Login</title>
    
    <!-- External CSS files for styling -->
    <link rel="stylesheet" href="../patient/assets/css/style.css">
    <link rel="stylesheet" href="../patient/assets/css/logo.css">
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
    MAIN LOGIN FORM CONTAINER
    ========================================== -->
    <div class="container">
        <h2>Admin Login</h2>
        
        <!-- Display error messages if any -->
        <?php
        if (!empty($errors)) {
            echo "<div class='error'>" . implode("<br>", $errors) . "</div>";
        }
        ?>
        
        <!-- Login form with POST method -->
        <form method="POST" action="">
            <label>Email:</label>
            <input type="email" name="email" required><br>
            
            <label>Password:</label>
            <input type="password" name="password" required><br>
            
            <button type="submit">Login</button>
        </form>
    </div>
    
    <!-- JavaScript file for additional functionality -->
    <script src="assets/js/global.js"></script>
</body>
</html>