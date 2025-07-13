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

$role = $_GET['role'] ?? '';
$id = intval($_GET['id'] ?? 0);

if (!in_array($role, ['patient', 'dentist', 'admin']) || $id <= 0) {
    die("Invalid user.");
}

$table = $role . "s"; // patients, dentists, admins

// Optionally fetch user info for logging
$stmt = $conn->prepare("SELECT name, email FROM $table WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($name, $email);
$stmt->fetch();
$stmt->close();

// Delete the user
$stmt = $conn->prepare("DELETE FROM $table WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

// Log the deletion
log_activity($_SESSION['admin_id'], "Deleted $role", "ID: $id, Name: $name, Email: $email");

header("Location: manage_users.php");
exit();
?>