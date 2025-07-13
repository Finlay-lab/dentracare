<?php
/**
 * ==========================================
 * PATIENT DASHBOARD PAGE
 * ==========================================
 * 
 * This page serves as the main dashboard for logged-in patients.
 * It displays a summary of appointments, medical history, and provides
 * navigation to other patient features.
 */

// Start session and check if patient is logged in
session_start();
if (!isset($_SESSION['patient_id'])) {
    // Redirect to login if not authenticated
    header("Location: login.php");
    exit();
}

require_once(__DIR__ . '/../config/db.php'); // Include database connection

$patient_id = $_SESSION['patient_id'];
$patient_name = $_SESSION['patient_name'];

// Fetch patient appointments (example query)
$appointments = [];
$stmt = $conn->prepare("SELECT id, appointment_date, status FROM appointments WHERE patient_id = ? ORDER BY appointment_date DESC");
if ($stmt) {
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Patient Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/logo.css">
    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
    <style>
        .dashboard-banner {
            width: 100%;
            max-width: 900px;
            height: 220px;
            object-fit: cover;
            display: block;
            margin: 0 auto 30px auto;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.10);
        }
        .container.patient-dashboard {
            max-width: 950px;
            margin: 40px auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.07);
            padding: 32px 28px;
        }
        .dashboard-nav {
            text-align: center;
            margin-bottom: 20px;
        }
        .dashboard-nav a {
            display: inline-block;
            margin: 0 10px;
            padding: 8px 18px;
            color: #1976d2;
            text-decoration: none;
            font-weight: 500;
            border-radius: 6px;
            transition: background 0.2s, color 0.2s;
        }
        .dashboard-nav a:hover, .dashboard-nav a.active {
            background: #e3f2fd;
            color: #0d47a1;
        }
        .profile-photo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        #calendar {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            border-radius: 8px;
            padding: 20px;
        }
    </style>
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
    <div class="container patient-dashboard">
        <img src="assets/images/dental-hero.jpg" alt="Dental Clinic" class="dashboard-banner">
        <nav class="dashboard-nav">
            <a href="dashboard.php" class="active">Dashboard</a> |
            <a href="book_appointment.php">Book Appointment</a> |
            <a href="view_appointments.php">View Appointments</a> |
            <a href="medical_history.php">Medical History</a> |
            <a href="edit_profile.php">Edit Profile</a> |
            <a href="download_treatment_summary.php">Download Treatment Summary</a> |
            <a href="logout.php">Logout</a>
        </nav>
        <?php if (!empty($_SESSION['patient_photo'])): ?>
            <img src="uploads/<?php echo htmlspecialchars($_SESSION['patient_photo']); ?>" alt="Profile Photo" class="profile-photo">
        <?php endif; ?>
        <main>
            <h2>Welcome, <?php echo htmlspecialchars($patient_name); ?>!</h2>
            <h3>Your Appointments</h3>
            <div id="calendar"></div>
            <!-- Booking Modal -->
            <div id="booking-modal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.4); z-index:1000;">
              <div style="background:#fff; max-width:400px; margin:100px auto; padding:24px; border-radius:10px; position:relative;">
                <button id="close-modal" style="position:absolute; top:8px; right:12px;">&times;</button>
                <h3>Book Appointment</h3>
                <form id="booking-form">
                  <input type="hidden" id="selected-date" name="date">
                  <label for="dentist">Dentist:</label>
                  <select id="dentist" name="dentist" required>
                    <?php
                    $dentists = $conn->query("SELECT id, name FROM dentists ORDER BY name ASC");
                    while ($d = $dentists->fetch_assoc()) {
                      echo '<option value="' . $d['id'] . '">' . htmlspecialchars($d['name']) . '</option>';
                    }
                    ?>
                  </select><br><br>
                  <label for="time">Time Slot:</label>
                  <select id="time" name="time" required>
                    <option value="09:00:00">09:00 AM</option>
                    <option value="10:00:00">10:00 AM</option>
                    <option value="11:00:00">11:00 AM</option>
                    <option value="12:00:00">12:00 PM</option>
                    <option value="14:00:00">02:00 PM</option>
                    <option value="15:00:00">03:00 PM</option>
                    <option value="16:00:00">04:00 PM</option>
                  </select><br><br>
                  <label for="reason">Reason:</label>
                  <input type="text" id="reason" name="reason" required><br><br>
                  <button type="submit">Book</button>
                </form>
              </div>
            </div>
            <p><a href="book_appointment.php">Book Appointment</a> | <a href="medical_history.php">View Medical History</a> | <a href="edit_profile.php">Edit Profile</a></p>
        </main>
    </div>
    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
      var calendarEl = document.getElementById('calendar');
      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: 'fetch_appointments.php',
        selectable: true,
        select: function(info) {
          var dateStr = info.startStr;
          document.getElementById('selected-date').value = dateStr;
          document.getElementById('booking-modal').style.display = 'block';
        },
        eventClick: function(info) {
          alert('Appointment ID: ' + info.event.id + '\nStatus: ' + info.event.extendedProps.status);
        }
      });
      calendar.render();

      // Handle booking form submission
      document.getElementById('booking-form').onsubmit = function(e) {
        e.preventDefault();
        var date = document.getElementById('selected-date').value;
        var reason = document.getElementById('reason').value;
        var dentist = document.getElementById('dentist').value;
        var time = document.getElementById('time').value;
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'book_appointment_ajax.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
          if (xhr.status == 200) {
            alert(xhr.responseText);
            document.getElementById('booking-modal').style.display = 'none';
            calendar.refetchEvents();
          }
        };
        xhr.send('date=' + encodeURIComponent(date) + '&time=' + encodeURIComponent(time) + '&reason=' + encodeURIComponent(reason) + '&dentist=' + encodeURIComponent(dentist));
      };

      // Close modal
      document.getElementById('close-modal').onclick = function() {
        document.getElementById('booking-modal').style.display = 'none';
      };
    });
    </script>
</body>
</html>