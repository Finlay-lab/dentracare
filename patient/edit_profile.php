<?php
/**
 * ==========================================
 * EDIT PROFILE PAGE
 * ==========================================
 * 
 * This page allows patients to update their profile information including
 * name, email, and password. It includes validation and duplicate checking.
 */

// Start session and check authentication
session_start();
require_once(__DIR__ . '/../config/db.php');

// Redirect to login if not authenticated
if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit();
}

$patient_id = $_SESSION['patient_id'];
$errors = [];
$success = "";

// ==========================================
// FETCH CURRENT USER INFORMATION
// ==========================================
// Get current user details to populate form
$stmt = $conn->prepare("SELECT name, email FROM patients WHERE id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$stmt->bind_result($current_name, $current_email);
$stmt->fetch();
$stmt->close();

// ==========================================
// HANDLE FORM SUBMISSION
// ==========================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize form data
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Basic validation
    if (empty($name) || empty($email)) {
        $errors[] = "Name and email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } else {
        // Check if email is being changed to one that already exists
        $stmt = $conn->prepare("SELECT id FROM patients WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $patient_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Email is already taken by another account.";
        } else {
            // ==========================================
            // UPDATE NAME AND EMAIL
            // ==========================================
            $stmt->close();
            $stmt = $conn->prepare("UPDATE patients SET name = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssi", $name, $email, $patient_id);
            if ($stmt->execute()) {
                // Update session with new name
                $_SESSION['patient_name'] = $name;
                $success = "Profile updated successfully!";
                $current_name = $name;
                $current_email = $email;
            } else {
                $errors[] = "Failed to update profile.";
            }
            $stmt->close();

            // ==========================================
            // UPDATE PASSWORD (IF PROVIDED)
            // ==========================================
            // Only update password if new password is provided
            if (!empty($password) || !empty($confirm_password)) {
                if ($password !== $confirm_password) {
                    $errors[] = "Passwords do not match.";
                } elseif (strlen($password) < 6) {
                    $errors[] = "Password must be at least 6 characters.";
                } else {
                    // Hash new password and update in database
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE patients SET password = ? WHERE id = ?");
                    $stmt->bind_param("si", $hashed_password, $patient_id);
                    if ($stmt->execute()) {
                        $success .= "<br>Password updated successfully!";
                    } else {
                        $errors[] = "Failed to update password.";
                    }
                    $stmt->close();
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Header with logo and navigation -->
    <div class="header">
        <a href="../index.php" class="dentracare-logo">
            <div class="logo-icon"></div>
            <div>
                <div class="logo-text">Dentracare</div>
                <div class="logo-subtitle">Dental Management Platform</div>
            </div>
        </a>
    </div>
    
    <!-- Main content container -->
    <div class="container">
        <h2>Edit Profile</h2>
        
        <!-- Display error and success messages -->
        <?php
        if (!empty($errors)) {
            echo "<div class='error'>" . implode("<br>", $errors) . "</div>";
        }
        if ($success) {
            echo "<div class='success'>$success</div>";
        }
        ?>
        
        <!-- Profile update form -->
        <form method="POST" action="">
            <label>Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($current_name); ?>" required><br>
            
            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($current_email); ?>" required><br>
            
            <hr>
            
            <label>New Password (leave blank to keep current):</label>
            <input type="password" name="password"><br>
            
            <label>Confirm New Password:</label>
            <input type="password" name="confirm_password"><br>
            
            <button type="submit">Update Profile</button>
        </form>
        
        <!-- Navigation link back to dashboard -->
        <p><a href="dashboard.php">Back to Dashboard</a></p>
    </div>
</body>
</html>