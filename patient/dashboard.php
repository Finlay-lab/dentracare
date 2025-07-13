<?php
/**
 * ==========================================
 * PATIENT DASHBOARD PAGE
 * ==========================================
 * 
 * This page serves as the main dashboard for logged-in patients.
 * It displays a summary of appointments, medical history, and provides
 * navigation to other patient features.
 */

// Start session and check if patient is logged in
session_start();
if (!isset($_SESSION['patient_id'])) {
    // Redirect to login if not authenticated
    header("Location: login.php");
    exit();
}

require_once(__DIR__ . '/../config/db.php'); // Include database connection

$patient_id = $_SESSION['patient_id'];
$patient_name = $_SESSION['patient_name'];

// Fetch patient appointments (example query)
$appointments = [];
$stmt = $conn->prepare("SELECT id, appointment_date, status FROM appointments WHERE patient_id = ? ORDER BY appointment_date DESC");
if ($stmt) {
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Patient Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/logo.css">
    <style>
        .dashboard-banner {
            width: 100%;
            max-width: 900px;
            height: 220px;
            object-fit: cover;
            display: block;
            margin: 0 auto 30px auto;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.10);
        }
        .container.patient-dashboard {
            max-width: 950px;
            margin: 40px auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.07);
            padding: 32px 28px;
        }
        .dashboard-nav {
            text-align: center;
            margin-bottom: 20px;
        }
        .dashboard-nav a {
            display: inline-block;
            margin: 0 10px;
            padding: 8px 18px;
            color: #1976d2;
            text-decoration: none;
            font-weight: 500;
            border-radius: 6px;
            transition: background 0.2s, color 0.2s;
        }
        .dashboard-nav a:hover, .dashboard-nav a.active {
            background: #e3f2fd;
            color: #0d47a1;
        }
        .profile-photo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="../index.php" class="dentracare-logo">
            <div class="logo-icon"></div>
            <div>
                <div class="logo-text">Dentracare</div>
                <div class="logo-subtitle">Dental Management Platform</div>
            </div>
        </a>
    </div>
    <div class="container patient-dashboard">
        <img src="assets/images/dental-hero.jpg" alt="Dental Clinic" class="dashboard-banner">
        <nav class="dashboard-nav">
            <a href="dashboard.php" class="active">Dashboard</a> |
            <a href="book_appointment.php">Book Appointment</a> |
            <a href="view_appointments.php">View Appointments</a> |
            <a href="medical_history.php">Medical History</a> |
            <a href="edit_profile.php">Edit Profile</a> |
            <a href="download_treatment_summary.php">Download Treatment Summary</a> |
            <a href="logout.php">Logout</a>
        </nav>
        <?php if (!empty($_SESSION['patient_photo'])): ?>
            <img src="uploads/<?php echo htmlspecialchars($_SESSION['patient_photo']); ?>" alt="Profile Photo" class="profile-photo">
        <?php endif; ?>
        <main>
            <h2>Welcome, <?php echo htmlspecialchars($patient_name); ?>!</h2>
            <h3>Your Appointments</h3>
            <?php if (empty($appointments)): ?>
                <p>No appointments found.</p>
            <?php else: ?>
                <table>
                    <tr><th>Date</th><th>Status</th></tr>
                    <?php foreach ($appointments as $appt): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($appt['appointment_date']); ?></td>
                            <td><?php echo htmlspecialchars($appt['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
            <p><a href="book_appointment.php">Book Appointment</a> | <a href="medical_history.php">View Medical History</a> | <a href="edit_profile.php">Edit Profile</a></p>
        </main>
    </div>
</body>
</html>