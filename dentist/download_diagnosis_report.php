<?php
session_start();
require_once(__DIR__ . '/../config/db.php');

if (!isset($_SESSION['dentist_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['patient_id'])) {
    die("No patient selected.");
}

$patient_id = intval($_GET['patient_id']);

// Fetch patient info
$stmt = $conn->prepare("SELECT name, email FROM patients WHERE id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$stmt->bind_result($name, $email);
if (!$stmt->fetch()) {
    $stmt->close();
    die("Patient not found.");
}
$stmt->close();

// Fetch medical history
$medical_history = "";
$stmt = $conn->prepare("SELECT details FROM medical_history WHERE patient_id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$stmt->bind_result($medical_history);
$stmt->fetch();
$stmt->close();

// Fetch appointments
$appointments = [];
$stmt = $conn->prepare("SELECT appointment_date, appointment_time, status FROM appointments WHERE patient_id = ? ORDER BY appointment_date DESC, appointment_time DESC");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}
$stmt->close();

// Fetch diagnosis records
$diagnoses = [];
$stmt = $conn->prepare("SELECT diagnosis, treatment, created_at FROM diagnosis WHERE patient_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $diagnoses[] = $row;
}
$stmt->close();

// Build report
$report = "Diagnosis Report for $name ($email)\n";
$report .= str_repeat('=', 40) . \"\\n\\n\";
$report .= "Medical History:\\n";
$report .= ($medical_history ? $medical_history : "No medical history provided.") . \"\\n\\n\";

$report .= "Appointments:\\n";
if ($appointments) {
    foreach ($appointments as $appt) {
        $report .= "- Date: " . $appt['appointment_date'] . ", Time: " . $appt['appointment_time'] . ", Status: " . $appt['status'] . \"\\n\";
    }
} else {
    $report .= "No appointments found.\\n";
}
$report .= \"\\nDiagnosis & Treatment Records:\\n\";
if ($diagnoses) {
    foreach ($diagnoses as $d) {
        $report .= "Date: " . $d['created_at'] . \"\\nDiagnosis: \" . $d['diagnosis'] . \"\\nTreatment: \" . $d['treatment'] . \"\\n---\\n\";
    }
} else {
    $report .= "No diagnosis records found.\\n";
}

// Output as downloadable text file
header('Content-Type: text/plain; charset=utf-8');
header('Content-Disposition: attachment; filename=\"diagnosis_report.txt\"');
echo $report;
exit();
?>