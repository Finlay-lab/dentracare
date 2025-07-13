<?php
session_start();
require_once(__DIR__ . '/../config/db.php');

if (!isset($_SESSION['dentist_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "No patient selected.";
    exit();
}

$patient_id = intval($_GET['id']);
$dentist_id = $_SESSION['dentist_id'];
$errors = [];
$success = "";

// Handle new diagnosis submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $diagnosis = trim($_POST["diagnosis"]);
    $treatment = trim($_POST["treatment"]);
    if (empty($diagnosis)) {
        $errors[] = "Diagnosis is required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO diagnosis (patient_id, dentist_id, diagnosis, treatment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $patient_id, $dentist_id, $diagnosis, $treatment);
        if ($stmt->execute()) {
            $success = "Diagnosis and treatment added successfully!";
        } else {
            $errors[] = "Failed to add diagnosis.";
        }
        $stmt->close();
    }
}

// Fetch patient info
$stmt = $conn->prepare("SELECT name, email FROM patients WHERE id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$stmt->bind_result($name, $email);
$stmt->fetch();
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

// Fetch previous diagnoses
$diagnoses = [];
$stmt = $conn->prepare("SELECT diagnosis, treatment, created_at FROM diagnosis WHERE patient_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $diagnoses[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Patient Details</title>
    <link rel="stylesheet" href="../patient/assets/css/style.css">
    <style>
        .dentist-container {
            max-width: 950px;
            margin: 40px auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.07);
            padding: 32px 28px;
        }
        .dentist-table {
            width: 100%;
            border-collapse: collapse;
            margin: 24px 0;
            background: #fafafa;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .dentist-table th, .dentist-table td {
            padding: 14px 18px;
            border-bottom: 1px solid #e0e0e0;
            text-align: left;
        }
        .dentist-table th {
            background: #e0f2f1;
            color: #00695c;
            font-weight: 600;
        }
        .dentist-table tr:last-child td {
            border-bottom: none;
        }
        .dentist-btn {
            background: #00897b;
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
        .dentist-btn:hover {
            background: #00695c;
        }
        .error {
            background: #ffebee;
            color: #c62828;
            padding: 10px 16px;
            border-radius: 6px;
            margin-bottom: 16px;
        }
        .success {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 10px 16px;
            border-radius: 6px;
            margin-bottom: 16px;
        }
        textarea {
            width: 100%;
            border-radius: 6px;
            border: 1px solid #bdbdbd;
            padding: 8px 10px;
            font-size: 1rem;
            margin-bottom: 10px;
        }
        form label {
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="dentist-container">
        <h2>Patient Details</h2>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($name); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
        <h3>Medical History</h3>
        <p><?php echo nl2br(htmlspecialchars($medical_history)); ?></p>
        <h3>Appointments</h3>
        <table class="dentist-table">
            <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
            </tr>
            <?php foreach ($appointments as $appt): ?>
            <tr>
                <td><?php echo htmlspecialchars($appt['appointment_date']); ?></td>
                <td><?php echo htmlspecialchars($appt['appointment_time']); ?></td>
                <td><?php echo htmlspecialchars($appt['status']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>

        <!-- Download Diagnosis Report Button -->
        <p>
            <a href="download_diagnosis_report.php?patient_id=<?php echo $patient_id; ?>" target="_blank" class="dentist-btn">
                Download Diagnosis Report
            </a>
        </p>

        <h3>Diagnosis & Treatment Records</h3>
        <?php if ($diagnoses): ?>
            <table class="dentist-table">
                <tr>
                    <th>Date</th>
                    <th>Diagnosis</th>
                    <th>Treatment</th>
                </tr>
                <?php foreach ($diagnoses as $d): ?>
                <tr>
                    <td><?php echo htmlspecialchars($d['created_at']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($d['diagnosis'])); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($d['treatment'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No diagnosis records yet.</p>
        <?php endif; ?>

        <h3>Add Diagnosis & Treatment</h3>
        <?php
        if (!empty($errors)) {
            echo "<div class='error'>" . implode("<br>", array_map('htmlspecialchars', $errors)) . "</div>";
        }
        if ($success) {
            echo "<div class='success'>" . htmlspecialchars($success) . "</div>";
        }
        ?>
        <form method="POST" action="">
            <label>Diagnosis:</label><br>
            <textarea name="diagnosis" rows="3" required></textarea><br>
            <label>Treatment:</label><br>
            <textarea name="treatment" rows="2"></textarea><br>
            <button type="submit" class="dentist-btn">Add Record</button>
        </form>
        <p style="margin-top:18px;"><a href="view_patients.php" class="dentist-btn">Back to Patients</a></p>
    </div>
    <div class="logo-text">Dentracare</div>
    <div class="logo-subtitle">Dental Management Platform</div>
</body>
</html>