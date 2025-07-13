<?php
session_start();
require_once(__DIR__ . '/../config/db.php');
header('Content-Type: application/json');

if (!isset($_SESSION['patient_id'])) {
    echo json_encode([]);
    exit();
}
$patient_id = $_SESSION['patient_id'];
$events = [];
$stmt = $conn->prepare("SELECT a.id, a.appointment_date, a.appointment_time, a.status, d.name AS dentist_name FROM appointments a JOIN dentists d ON a.dentist_id = d.id WHERE a.patient_id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $time_formatted = date('h:i A', strtotime($row['appointment_time']));
    $events[] = [
        'id' => $row['id'],
        'title' => 'Dr. ' . $row['dentist_name'] . ' at ' . $time_formatted,
        'start' => $row['appointment_date'] . 'T' . $row['appointment_time'],
        'status' => $row['status'],
        'color' => $row['status'] === 'confirmed' ? '#4caf50' : '#fbc02d'
    ];
}
$stmt->close();
echo json_encode($events); 