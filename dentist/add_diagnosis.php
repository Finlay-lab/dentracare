<?php
session_start();
require_once(__DIR__ . '/../config/db.php');

if (!isset($_SESSION['dentist_id'])) {
    header("Location: login.php");
    exit();
}

$errors = [];
$success = '';
$dentist_id = $_SESSION['dentist_id'];

// Fetch all patients
$patients = $conn->query("SELECT id, name FROM patients ORDER BY name ASC");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = $_POST['patient_id'] ?? '';
    $appointment_id = $_POST['appointment_id'] ?? '';
    $diagnosis = trim($_POST['diagnosis'] ?? '');

    if (empty($patient_id) || empty($appointment_id) || empty($diagnosis)) {
        $errors[] = "All fields are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO diagnoses (appointment_id, dentist_id, patient_id, diagnosis, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("iiis", $appointment_id, $dentist_id, $patient_id, $diagnosis);
        if ($stmt->execute()) {
            $success = "Diagnosis added successfully.";
        } else {
            $errors[] = "Failed to add diagnosis.";
        }
        $stmt->close();
    }
}

// Fetch appointments for selected patient (AJAX or after POST)
$patient_appointments = [];
if (!empty($_POST['patient_id']) || !empty($_GET['patient_id'])) {
    $selected_patient_id = $_POST['patient_id'] ?? $_GET['patient_id'];
    $appt_stmt = $conn->prepare("SELECT id, appointment_date, appointment_time FROM appointments WHERE patient_id = ? AND dentist_id = ? AND status != 'Cancelled' ORDER BY appointment_date DESC, appointment_time DESC");
    $appt_stmt->bind_param("ii", $selected_patient_id, $dentist_id);
    $appt_stmt->execute();
    $appt_result = $appt_stmt->get_result();
    while ($row = $appt_result->fetch_assoc()) {
        $patient_appointments[] = $row;
    }
    $appt_stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Diagnosis</title>
    <link rel="stylesheet" href="../patient/assets/css/style.css">
    <link rel="stylesheet" href="../patient/assets/css/logo.css">
    <style>
        .container { max-width: 500px; margin: 40px auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,0.07); padding: 32px 28px; }
        label { display: block; margin-top: 18px; font-weight: 500; }
        select, textarea { width: 100%; margin-top: 6px; border-radius: 6px; border: 1px solid #bdbdbd; padding: 8px; }
        textarea { min-height: 80px; }
        button[type="submit"] { margin-top: 24px; padding: 10px 28px; background: #00897b; color: #fff; border: none; border-radius: 6px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        button[type="submit"]:hover { background: #00695c; }
        .error { background: #ffebee; color: #c62828; padding: 10px 16px; border-radius: 6px; margin-bottom: 16px; text-align: center; }
        .success { background: #e8f5e9; color: #2e7d32; padding: 10px 16px; border-radius: 6px; margin-bottom: 16px; text-align: center; }
    </style>
    <script>
    function reloadWithPatient() {
        var patientId = document.getElementById('patient_id').value;
        window.location.href = 'add_diagnosis.php?patient_id=' + patientId;
    }
    </script>
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
        <h2>Add Diagnosis</h2>
        <?php
        if (!empty($errors)) {
            echo "<div class='error'>" . implode("<br>", array_map('htmlspecialchars', $errors)) . "</div>";
        }
        if ($success) {
            echo "<div class='success'>" . htmlspecialchars($success) . "</div>";
        }
        ?>
        <form method="POST" action="">
            <label>Patient:</label>
            <select name="patient_id" id="patient_id" onchange="reloadWithPatient()" required>
                <option value="">-- Select Patient --</option>
                <?php foreach ($patients as $p): ?>
                    <option value="<?php echo $p['id']; ?>" <?php if ((isset($selected_patient_id) && $selected_patient_id == $p['id']) || (!empty($_POST['patient_id']) && $_POST['patient_id'] == $p['id'])) echo 'selected'; ?>><?php echo htmlspecialchars($p['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <label>Appointment:</label>
            <select name="appointment_id" required>
                <option value="">-- Select Appointment --</option>
                <?php foreach ($patient_appointments as $appt): ?>
                    <option value="<?php echo $appt['id']; ?>">
                        <?php echo htmlspecialchars($appt['appointment_date'] . ' ' . $appt['appointment_time']); ?> 
                    </option>
                <?php endforeach; ?>
            </select>
            <label>Diagnosis:</label>
            <textarea name="diagnosis" required></textarea>
            <button type="submit">Add Diagnosis</button>
        </form>
        <p style="margin-top:18px;"><a href="dashboard.php">Back to Dashboard</a></p>
    </div>
</body>
</html> 