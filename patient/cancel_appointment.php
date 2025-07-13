<?php
/**
 * ==========================================
 * CANCEL APPOINTMENT SCRIPT
 * ==========================================
 * 
 * This script handles appointment cancellation for patients.
 * It updates the appointment status and sends email notifications
 * to both the patient and the dentist.
 */

// Start session and check authentication
session_start();
require_once(__DIR__ . '/../config/db.php');
require_once(__DIR__ . '/../config/mail.php'); // Include email functionality

// Redirect to login if not authenticated
if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit();
}

$patient_id = $_SESSION['patient_id'];
$date = $_GET['date'] ?? '';
$time = $_GET['time'] ?? '';

// Validate appointment parameters
if (empty($date) || empty($time)) {
    header("Location: view_appointments.php");
    exit();
}

// ==========================================
// UPDATE APPOINTMENT STATUS
// ==========================================
// Update appointment status to 'Cancelled' in database
$stmt = $conn->prepare("UPDATE appointments SET status = 'Cancelled' WHERE patient_id = ? AND appointment_date = ? AND appointment_time = ?");
$stmt->bind_param("iss", $patient_id, $date, $time);
$stmt->execute();
$stmt->close();

// ==========================================
// FETCH PATIENT INFORMATION
// ==========================================
// Get patient details for email notification
$pstmt = $conn->prepare("SELECT name, email FROM patients WHERE id = ?");
$pstmt->bind_param("i", $patient_id);
$pstmt->execute();
$pstmt->bind_result($patient_name, $patient_email);
$pstmt->fetch();
$pstmt->close();

// ==========================================
// FETCH DENTIST INFORMATION
// ==========================================
// Get dentist details for email notification
$dstmt = $conn->prepare("SELECT d.name, d.email FROM appointments a JOIN dentists d ON a.dentist_id = d.id WHERE a.patient_id = ? AND a.appointment_date = ? AND a.appointment_time = ?");
$dstmt->bind_param("iss", $patient_id, $date, $time);
$dstmt->execute();
$dstmt->bind_result($dentist_name, $dentist_email);
$dstmt->fetch();
$dstmt->close();

// ==========================================
// SEND EMAIL NOTIFICATIONS
// ==========================================

// Send confirmation email to patient
if (!empty($patient_email)) {
    $subject = "Appointment Cancellation - Dentracare";
    $message = "Dear $patient_name,\n\nYour appointment for $date at $time has been cancelled as per your request.\n\nIf this was a mistake, please book a new appointment.\n\nThank you for using Dentracare!";
    sendMail($patient_email, $subject, $message, $patient_name);
}

// Send notification email to dentist
if (!empty($dentist_email)) {
    $subject = "Appointment Cancelled - Dentracare";
    $message = "Dear Dr. $dentist_name,\n\nThe appointment scheduled by $patient_name for $date at $time has been cancelled by the patient.\n\nPlease log in to your dashboard for more details.";
    sendMail($dentist_email, $subject, $message, $dentist_name);
}

// Redirect back to appointments view
header("Location: view_appointments.php");
exit(); 