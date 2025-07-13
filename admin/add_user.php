<?php
session_start();
require_once(__DIR__ . '/../config/db.php');
function log_activity($admin_id, $action, $details = null) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO activity_logs (admin_id, action, details) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $admin_id, $action, $details);
    $stmt->execute();
    $stmt->close();
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST["role"];
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        if ($role == "patient") {
            $stmt = $conn->prepare("SELECT id FROM patients WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $errors[] = "Email already registered as a patient.";
            } else {
                $stmt = $conn->prepare("INSERT INTO patients (name, email, password) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $name, $email, $hashed_password);
                if ($stmt->execute()) {
                    $success = "Patient added successfully!";
                    log_activity($_SESSION['admin_id'], "Added patient", "Name: $name, Email: $email");
                } else {
                    $errors[] = "Failed to add patient.";
                }
            }
            $stmt->close();
        } elseif ($role == "dentist") {
            $stmt = $conn->prepare("SELECT id FROM dentists WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $errors[] = "Email already registered as a dentist.";
            } else {
                $stmt = $conn->prepare("INSERT INTO dentists (name, email, password) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $name, $email, $hashed_password);
                if ($stmt->execute()) {
                    $success = "Dentist added successfully!";
                    log_activity($_SESSION['admin_id'], "Added dentist", "Name: $name, Email: $email");
                } else {
                    $errors[] = "Failed to add dentist.";
                }
            }
            $stmt->close();
        } elseif ($role == "admin") {
            $stmt = $conn->prepare("SELECT id FROM admins WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $errors[] = "Email already registered as an admin.";
            } else {
                $stmt = $conn->prepare("INSERT INTO admins (name, email, password) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $name, $email, $hashed_password);
                if ($stmt->execute()) {
                    $success = "Admin added successfully!";
                    log_activity($_SESSION['admin_id'], "Added admin", "Name: $name, Email: $email");
                } else {
                    $errors[] = "Failed to add admin.";
                }
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add User</title>
    <link rel="stylesheet" href="../patient/assets/css/style.css">
    <link rel="stylesheet" href="../patient/assets/css/logo.css">
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
    <div class="container">
        <div class="dashboard-nav">
            <a href="dashboard.php">Dashboard</a> |
            <a href="manage_users.php">Manage Users</a> |
            <a href="add_user.php">Add User</a> |
            <a href="logout.php">Logout</a>
        </div>
        <h2>Add User</h2>
        <?php
        if (!empty($errors)) {
            echo "<div class='error'>" . implode("<br>", $errors) . "</div>";
        }
        if ($success) {
            echo "<div class='success'>$success</div>";
        }
        ?>
        <form method="POST" action="">
            <label>Role:</label>
            <select name="role" required>
                <option value="patient">Patient</option>
                <option value="dentist">Dentist</option>
                <option value="admin">Admin</option>
            </select><br>
            <label>Name:</label>
            <input type="text" name="name" required><br>
            <label>Email:</label>
            <input type="email" name="email" required><br>
            <label>Password:</label>
            <input type="password" name="password" required><br>
            <label>Confirm Password:</label>
            <input type="password" name="confirm_password" required><br>
            <button type="submit">Add User</button>
        </form>
    </div>
       <script src="assets/js/global.js"></script>
</body>
</html>