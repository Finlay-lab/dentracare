<?php
/**
 * ==========================================
 * DOWNLOAD TREATMENT SUMMARY
 * ==========================================
 * 
 * This script generates and downloads a text file containing
 * the patient's treatment summary including medical history
 * and appointment records.
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

// ==========================================
// FETCH PATIENT INFORMATION
// ==========================================
// Get patient details for the summary
$stmt = $conn->prepare("SELECT name, email FROM patients WHERE id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$stmt->bind_result($name, $email);
$stmt->fetch();
$stmt->close();

// ==========================================
// FETCH APPOINTMENT HISTORY
// ==========================================
// Get all appointments for this patient
$stmt = $conn->prepare("SELECT appointment_date, appointment_time, status FROM appointments WHERE patient_id = ? ORDER BY appointment_date DESC, appointment_time DESC");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

// ==========================================
// FETCH MEDICAL HISTORY
// ==========================================
// Get patient's medical history
$medical_history = "";
$stmt2 = $conn->prepare("SELECT details FROM medical_history WHERE patient_id = ?");
$stmt2->bind_param("i", $patient_id);
$stmt2->execute();
$stmt2->bind_result($medical_history);
$stmt2->fetch();
$stmt2->close();

// ==========================================
// BUILD TREATMENT SUMMARY
// ==========================================
// Create formatted summary text
$summary = "Treatment Summary for $name ($email)\n";
$summary .= "----------------------------------------\n";
$summary .= "Medical History:\n" . ($medical_history ? $medical_history : "No medical history provided.") . "\n\n";
$summary .= "Appointments:\n";

// Add appointment details to summary
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $summary .= "- Date: " . $row['appointment_date'] . ", Time: " . $row['appointment_time'] . ", Status: " . $row['status'] . "\n";
    }
} else {
    $summary .= "No appointments found.\n";
}

// ==========================================
// OUTPUT AS DOWNLOADABLE FILE
// ==========================================
// Set headers for file download
header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename=\"treatment_summary.txt\"');

// Output the summary content
echo $summary;
exit();
?>