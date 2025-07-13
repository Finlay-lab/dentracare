<?php
/**
 * ==========================================
 * VIEW PATIENTS PAGE - DENTIST MODULE
 * ==========================================
 * 
 * This page allows dentists to view a list of all patients in the system.
 * It displays patient information in a table format with options to view
 * detailed patient information.
 */

// Start session and check authentication
session_start();
require_once(__DIR__ . '/../config/db.php');

// Redirect to login if not authenticated
if (!isset($_SESSION['dentist_id'])) {
    header("Location: login.php");
    exit();
}

// ==========================================
// FETCH ALL PATIENTS FROM DATABASE
// ==========================================
// Get all patients ordered by name for easy browsing
$result = $conn->query("SELECT id, name, email FROM patients ORDER BY name ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Patients</title>
    <link rel="stylesheet" href="../patient/assets/css/style.css">
    
    <!-- Custom styling for dentist patient view -->
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
        PATIENT TABLE STYLING
        ========================================== */
        .dentist-table {
            width: 100%;
            border-collapse: collapse;
            margin: 24px 0;
            background: #fafafa;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        
        .dentist-table th, .dentist-table td {
            padding: 14px 18px;
            border-bottom: 1px solid #e0e0e0;
            text-align: left;
        }
        
        .dentist-table th {
            background: #e0f2f1; /* Light teal header */
            color: #00695c; /* Dark teal text */
            font-weight: 600;
        }
        
        .dentist-table tr:last-child td {
            border-bottom: none;
        }
        
        /* ==========================================
        DENTIST BUTTON STYLING
        ========================================== */
        .dentist-btn {
            background: #00897b; /* Teal background */
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 8px 22px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        
        .dentist-btn:hover {
            background: #00695c; /* Darker teal on hover */
        }
    </style>
</head>
<body>
    <!-- ==========================================
    MAIN DENTIST CONTAINER
    ========================================== -->
    <div class="dentist-container">
        <h2>All Patients</h2>
        
        <!-- Dentracare branding -->
        <div class="logo-text">Dentracare</div>
        <div class="logo-subtitle">Dental Management Platform</div>
        
        <!-- ==========================================
        PATIENTS TABLE
        ========================================== -->
        <table class="dentist-table">
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
            
            <!-- Loop through all patients and display their information -->
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td>
                    <!-- Link to view detailed patient information -->
                    <a href="view_patient.php?id=<?php echo $row['id']; ?>" class="dentist-btn">View Details</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        
        <!-- Navigation link back to dashboard -->
        <p style="margin-top:18px;"><a href="dashboard.php" class="dentist-btn">Back to Dashboard</a></p>
    </div>
</body>
</html>