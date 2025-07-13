<?php
session_start();
require_once(__DIR__ . '/../config/db.php');

if (!isset($_SESSION['patient_id'])) {
    echo "Not logged in.";
    exit;
}

$patient_id = $_SESSION['patient_id'];
$date = $_POST['date'] ?? '';
$time = $_POST['time'] ?? '';
$reason = $_POST['reason'] ?? '';
$dentist_id = $_POST['dentist'] ?? '';

if (!$date || !$time || !$reason || !$dentist_id) {
    echo "All fields are required.";
    exit;
}

// Prevent double-booking for the same dentist, date, and time
$check = $conn->prepare("SELECT id FROM appointments WHERE dentist_id = ? AND appointment_date = ? AND appointment_time = ?");
$check->bind_param("iss", $dentist_id, $date, $time);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    echo "This time slot is already booked for the selected dentist.";
    exit;
}
$check->close();

$stmt = $conn->prepare("INSERT INTO appointments (patient_id, dentist_id, appointment_date, appointment_time, reason, status) VALUES (?, ?, ?, ?, ?, 'pending')");
$stmt->bind_param("iisss", $patient_id, $dentist_id, $date, $time, $reason);
if ($stmt->execute()) {
    echo "Appointment booked!";
} else {
    echo "Failed to book appointment.";
}
$stmt->close(); 