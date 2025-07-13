<?php
/**
 * ==========================================
 * MANAGE USERS PAGE - ADMIN MODULE
 * ==========================================
 * 
 * This page allows administrators to view and manage all users
 * in the system including patients, dentists, and other admins.
 */

// Start session and check authentication
session_start();
require_once(__DIR__ . '/../config/db.php');

// Redirect to login if not authenticated as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle user deletion
if (isset($_GET['delete']) && isset($_GET['type'])) {
    $user_id = $_GET['delete'];
    $user_type = $_GET['type'];
    
    if ($user_type === 'patient') {
        $stmt = $conn->prepare("DELETE FROM patients WHERE id = ?");
    } elseif ($user_type === 'dentist') {
        $stmt = $conn->prepare("DELETE FROM dentists WHERE id = ?");
    } elseif ($user_type === 'admin') {
        $stmt = $conn->prepare("DELETE FROM admins WHERE id = ?");
    }
    
    if (isset($stmt)) {
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $success = "User deleted successfully!";
        } else {
            $error = "Failed to delete user.";
        }
        $stmt->close();
    }
}

// Fetch all users
$patients = $conn->query("SELECT id, name, email, created_at FROM patients ORDER BY name ASC");
if (!$patients) { die("Patients query failed: " . $conn->error); }
$dentists = $conn->query("SELECT id, name, email, specialization, created_at FROM dentists ORDER BY name ASC");
if (!$dentists) { die("Dentists query failed: " . $conn->error); }
$admins = $conn->query("SELECT id, name, email, role, created_at FROM admins ORDER BY name ASC");
if (!$admins) { die("Admins query failed: " . $conn->error); }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users - Admin</title>
    <link rel="stylesheet" href="../patient/assets/css/style.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 40px auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.07);
            padding: 32px 28px;
        }
        .user-section {
            margin-bottom: 40px;
        }
        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: #fafafa;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .user-table th, .user-table td {
            padding: 14px 18px;
            border-bottom: 1px solid #e0e0e0;
            text-align: left;
        }
        .user-table th {
            background: #e3f2fd;
            color: #1565c0;
            font-weight: 600;
        }
        .admin-btn {
            background: #1565c0;
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
        .admin-btn:hover {
            background: #0d47a1;
        }
        .delete-btn {
            background: #d32f2f;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 6px 16px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        .delete-btn:hover {
            background: #b71c1c;
        }
        .success {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 10px 16px;
            border-radius: 6px;
            margin-bottom: 16px;
        }
        .error {
            background: #ffebee;
            color: #c62828;
            padding: 10px 16px;
            border-radius: 6px;
            margin-bottom: 16px;
        }
    </style>
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
    
    <!-- Main admin container -->
    <div class="admin-container">
        <h2>Manage Users</h2>
        
        <!-- Display success/error messages -->
        <?php if (isset($success)): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <!-- Navigation -->
        <div style="margin-bottom: 20px;">
            <a href="dashboard.php" class="admin-btn">Dashboard</a>
            <a href="add_user.php" class="admin-btn">Add User</a>
            <a href="activity_logs.php" class="admin-btn">Activity Logs</a>
            <a href="logout.php" class="admin-btn">Logout</a>
        </div>
        
        <!-- Patients Section -->
        <div class="user-section">
            <h3>Patients</h3>
            <table class="user-table">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Registration Date</th>
                    <th>Actions</th>
                </tr>
                <?php while ($patient = $patients->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($patient['name']); ?></td>
                    <td><?php echo htmlspecialchars($patient['email']); ?></td>
                    <td><?php echo date('M d, Y', strtotime($patient['created_at'])); ?></td>
                    <td>
                        <a href="edit_user.php?type=patient&id=<?php echo $patient['id']; ?>" class="admin-btn">Edit</a>
                        <a href="?delete=<?php echo $patient['id']; ?>&type=patient" class="delete-btn" onclick="return confirm('Are you sure you want to delete this patient?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
        
        <!-- Dentists Section -->
        <div class="user-section">
            <h3>Dentists</h3>
            <table class="user-table">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Specialization</th>
                    <th>Registration Date</th>
                    <th>Actions</th>
                </tr>
                <?php while ($dentist = $dentists->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($dentist['name']); ?></td>
                    <td><?php echo htmlspecialchars($dentist['email']); ?></td>
                    <td><?php echo htmlspecialchars($dentist['specialization']); ?></td>
                    <td><?php echo date('M d, Y', strtotime($dentist['created_at'])); ?></td>
                    <td>
                        <a href="edit_user.php?type=dentist&id=<?php echo $dentist['id']; ?>" class="admin-btn">Edit</a>
                        <a href="?delete=<?php echo $dentist['id']; ?>&type=dentist" class="delete-btn" onclick="return confirm('Are you sure you want to delete this dentist?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
        
        <!-- Admins Section -->
        <div class="user-section">
            <h3>Administrators</h3>
            <table class="user-table">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Registration Date</th>
                    <th>Actions</th>
                </tr>
                <?php while ($admin = $admins->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($admin['name']); ?></td>
                    <td><?php echo htmlspecialchars($admin['email']); ?></td>
                    <td><?php echo htmlspecialchars($admin['role']); ?></td>
                    <td><?php echo date('M d, Y', strtotime($admin['created_at'])); ?></td>
                    <td>
                        <a href="edit_user.php?type=admin&id=<?php echo $admin['id']; ?>" class="admin-btn">Edit</a>
                        <?php if ($admin['id'] != $_SESSION['admin_id']): ?>
                            <a href="?delete=<?php echo $admin['id']; ?>&type=admin" class="delete-btn" onclick="return confirm('Are you sure you want to delete this admin?')">Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>