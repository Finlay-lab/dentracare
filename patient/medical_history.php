<?php
/**
 * ==========================================
 * MEDICAL HISTORY PAGE
 * ==========================================
 * 
 * This page allows patients to view and update their medical history.
 * It handles both creating new records and updating existing ones.
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
// HANDLE FORM SUBMISSION
// ==========================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $details = trim($_POST["details"]);
    
    // Validate input
    if (empty($details)) {
        $errors[] = "Medical history details cannot be empty.";
    } else {
        // Check if a record already exists for this patient
        $stmt = $conn->prepare("SELECT id FROM medical_history WHERE patient_id = ?");
        $stmt->bind_param("i", $patient_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // ==========================================
            // UPDATE EXISTING RECORD
            // ==========================================
            $stmt->close();
            $stmt = $conn->prepare("UPDATE medical_history SET details = ? WHERE patient_id = ?");
            $stmt->bind_param("si", $details, $patient_id);
            if ($stmt->execute()) {
                $success = "Medical history updated successfully!";
            } else {
                $errors[] = "Failed to update medical history.";
            }
        } else {
            // ==========================================
            // INSERT NEW RECORD
            // ==========================================
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO medical_history (patient_id, details) VALUES (?, ?)");
            $stmt->bind_param("is", $patient_id, $details);
            if ($stmt->execute()) {
                $success = "Medical history saved successfully!";
            } else {
                $errors[] = "Failed to save medical history.";
            }
        }
        $stmt->close();
    }
}

// ==========================================
// FETCH EXISTING MEDICAL HISTORY
// ==========================================
// Get current medical history to display in form
$existing_details = "";
$stmt = $conn->prepare("SELECT details FROM medical_history WHERE patient_id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$stmt->bind_result($existing_details);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Medical History</title>
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
        <h2>Your Medical History</h2>
        
        <!-- Display error and success messages -->
        <?php
        if (!empty($errors)) {
            echo "<div class='error'>" . implode("<br>", $errors) . "</div>";
        }
        if ($success) {
            echo "<div class='success'>$success</div>";
        }
        ?>
        
        <!-- Medical history form -->
        <form method="POST" action="">
            <label for="details">Medical History Details:</label><br>
            <textarea name="details" id="details" rows="8" cols="50" required><?php echo htmlspecialchars($existing_details); ?></textarea><br>
            <button type="submit">Save</button>
        </form>
        
        <!-- Navigation link back to dashboard -->
        <p><a href="dashboard.php">Back to Dashboard</a></p>
    </div>
</body>
</html>