<?php
/**
 * ==========================================
 * BOOK APPOINTMENT PAGE
 * ==========================================
 * 
 * This page allows patients to book new dental appointments.
 * It handles form submission, validation, and inserts the appointment into the database.
 */

session_start();
require_once(__DIR__ . '/../config/db.php');

if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit();
}

$errors = [];
$success = "";

// Fetch all dentists for the dropdown
$dentists = $conn->query("SELECT id, name FROM dentists ORDER BY name ASC");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST["date"];
    $time = $_POST["time"];
    $dentist_id = $_POST["dentist_id"] ?? '';
    $reason = trim($_POST['reason']);

    // Basic validation
    if (empty($date) || empty($time) || empty($dentist_id) || empty($reason)) {
        $errors[] = "Date, time, dentist, and reason are required.";
    } else {
        $now = date('Y-m-d H:i');
        $selected = $date . ' ' . $time;
        if (strtotime($selected) < strtotime($now)) {
            $errors[] = "You cannot book an appointment in the past.";
        } else {
            $patient_id = $_SESSION['patient_id'];
            // Prevent double booking for the same patient
            $check = $conn->prepare("SELECT id FROM appointments WHERE appointment_date = ? AND appointment_time = ? AND patient_id = ?");
            $check->bind_param("ssi", $date, $time, $patient_id);
            $check->execute();
            $check->store_result();
            if ($check->num_rows > 0) {
                $errors[] = "You already have an appointment at this date and time.";
            }
            $check->close();

            if (empty($errors)) {
                $stmt = $conn->prepare("INSERT INTO appointments (patient_id, dentist_id, appointment_date, appointment_time, reason, status) VALUES (?, ?, ?, ?, ?, 'Pending')");
                if (!$stmt) {
                    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
                }
                $stmt->bind_param("iisss", $patient_id, $dentist_id, $date, $time, $reason);
                if ($stmt->execute()) {
                    $success = "Appointment booked successfully!";
                    // Send email notification to patient
                    require_once(__DIR__ . '/../config/mail.php');
                    $patient_email = '';
                    $patient_name = '';
                    // Fetch patient email and name
                    $pstmt = $conn->prepare("SELECT name, email FROM patients WHERE id = ?");
                    $pstmt->bind_param("i", $patient_id);
                    $pstmt->execute();
                    $pstmt->bind_result($patient_name, $patient_email);
                    $pstmt->fetch();
                    $pstmt->close();
                    if (!empty($patient_email)) {
                        $subject = "Appointment Confirmation - Dentracare";
                        $message = "Dear $patient_name,\n\nYour appointment for $date at $time has been booked successfully.\n\nThank you for choosing Dentracare!";
                        sendMail($patient_email, $subject, $message, $patient_name);
                    }
                    // Send email notification to dentist
                    $dentist_email = '';
                    $dentist_name = '';
                    $dstmt = $conn->prepare("SELECT name, email FROM dentists WHERE id = ?");
                    $dstmt->bind_param("i", $dentist_id);
                    $dstmt->execute();
                    $dstmt->bind_result($dentist_name, $dentist_email);
                    $dstmt->fetch();
                    $dstmt->close();
                    if (!empty($dentist_email)) {
                        $subject = "New Appointment Booked - Dentracare";
                        $message = "Dear Dr. $dentist_name,\n\nA new appointment has been booked by $patient_name for $date at $time.\n\nPlease log in to your dashboard for more details.";
                        sendMail($dentist_email, $subject, $message, $dentist_name);
                    }
                } else {
                    $errors[] = "Failed to book appointment. Please try again.";
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
    <title>Book Appointment</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .container {
            max-width: 450px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.07);
            padding: 32px 28px;
        }
        form label {
            display: block;
            margin-top: 18px;
            font-weight: 500;
        }
        form input[type="date"],
        form input[type="time"] {
            width: 100%;
            padding: 8px 10px;
            margin-top: 6px;
            border: 1px solid #bdbdbd;
            border-radius: 6px;
            font-size: 1rem;
        }
        button[type="submit"] {
            margin-top: 24px;
            padding: 10px 28px;
            background: #1976d2;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        button[type="submit"]:hover {
            background: #1565c0;
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
    </style>
</head>
<body>
    <div class="container">
        <h2>Book Appointment</h2>
        <?php
        if (!empty($errors)) {
            echo "<div class='error'>" . implode("<br>", array_map('htmlspecialchars', $errors)) . "</div>";
        }
        if ($success) {
            echo "<div class='success'>" . htmlspecialchars($success) . "</div>";
        }
        ?>
        <form method="POST" action="">
            <label>Date:</label>
            <input type="date" name="date" required>
            <label>Time:</label>
            <input type="time" name="time" required>
            <label>Dentist:</label>
            <select name="dentist_id" required>
                <option value="">-- Select Dentist --</option>
                <?php while ($dentist = $dentists->fetch_assoc()): ?>
                    <option value="<?php echo $dentist['id']; ?>"><?php echo htmlspecialchars($dentist['name']); ?></option>
                <?php endwhile; ?>
            </select>
            <label>Reason for Visit:</label>
            <textarea name="reason" required></textarea>
            <button type="submit">Book Appointment</button>
        </form>
        <p style="margin-top:18px;"><a href="dashboard.php">Back to Dashboard</a></p>
    </div>
</body>
</html>