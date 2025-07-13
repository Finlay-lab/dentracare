<?php
/**
 * ==========================================
 * VIEW APPOINTMENTS PAGE
 * ==========================================
 * 
 * This page displays all appointments for the logged-in patient.
 * It shows appointment details and provides options to cancel appointments.
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

// Fetch appointments for this patient from database
$stmt = $conn->prepare("SELECT appointment_date, appointment_time, status FROM appointments WHERE patient_id = ? ORDER BY appointment_date DESC, appointment_time DESC");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Appointments</title>
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
        <h2>Your Appointments</h2>
        
        <!-- Appointments table -->
        <table>
            <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                <td><?php echo htmlspecialchars($row['appointment_time']); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
                <td>
                    <!-- Show cancel option only if appointment is not already cancelled -->
                    <?php if ($row['status'] !== 'Cancelled'): ?>
                        <a href="cancel_appointment.php?date=<?php echo urlencode($row['appointment_date']); ?>&time=<?php echo urlencode($row['appointment_time']); ?>" onclick="return confirm('Are you sure you want to cancel this appointment?');">Cancel</a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        
        <!-- Navigation link back to dashboard -->
        <p><a href="dashboard.php">Back to Dashboard</a></p>
    </div>
</body>
</html>