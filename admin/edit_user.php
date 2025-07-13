<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

$role = $_GET['role'] ?? '';
$id = intval($_GET['id'] ?? 0);
$errors = [];
$success = "";

if (!in_array($role, ['patient', 'dentist', 'admin']) || $id <= 0) {
    die("Invalid user.");
}

$table = $role . "s"; // patients, dentists, admins

// Fetch user info
$stmt = $conn->prepare("SELECT name, email FROM $table WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($name, $email);
$stmt->fetch();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_name = trim($_POST["name"]);
    $new_email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if (empty($new_name) || empty($new_email)) {
        $errors[] = "Name and email are required.";
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } else {
        // Update name and email
        $stmt = $conn->prepare("UPDATE $table SET name = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $new_name, $new_email, $id);
        if ($stmt->execute()) {
            $success = "User updated successfully!";
            log_activity($_SESSION['admin_id'], "Edited $role", "ID: $id, Name: $new_name, Email: $new_email");
            $name = $new_name;
            $email = $new_email;
        } else {
            $errors[] = "Failed to update user.";
        }
        $stmt->close();

        // Update password if provided and matches confirmation
        if (!empty($password) || !empty($confirm_password)) {
            if ($password !== $confirm_password) {
                $errors[] = "Passwords do not match.";
            } elseif (strlen($password) < 6) {
                $errors[] = "Password must be at least 6 characters.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE $table SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $hashed_password, $id);
                if ($stmt->execute()) {
                    $success .= "<br>Password updated successfully!";
                    log_activity($_SESSION['admin_id'], "Reset password for $role", "ID: $id, Name: $new_name, Email: $new_email");
                } else {
                    $errors[] = "Failed to update password.";
                }
                $stmt->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
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
        <h2>Edit User (<?php echo ucfirst($role); ?>)</h2>
        <?php
        if (!empty($errors)) {
            echo "<div class='error'>" . implode("<br>", $errors) . "</div>";
        }
        if ($success) {
            echo "<div class='success'>$success</div>";
        }
        ?>
        <form method="POST" action="">
            <label>Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required><br>
            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required><br>
            <hr>
            <label>New Password (leave blank to keep current):</label>
            <input type="password" name="password"><br>
            <label>Confirm New Password:</label>
            <input type="password" name="confirm_password"><br>
            <button type="submit">Update User</button>
        </form>
        <p><a href="manage_users.php">Back to Manage Users</a></p>
    </div>
       <script src="assets/js/global.js"></script>
</body>
</html>