<?php
session_start();
require_once(__DIR__ . '/../config/db.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch all admins for the filter dropdown
$admins = [];
$admins_result = $conn->query("SELECT id, name FROM admins ORDER BY name ASC");
if (!$admins_result) {
    die("Failed to fetch admins: " . $conn->error);
}
while ($row = $admins_result->fetch_assoc()) {
    $admins[] = $row;
}

// Get filter/search values
$search = $_GET['search'] ?? '';
$admin_id = $_GET['admin_id'] ?? '';
$action = $_GET['action'] ?? '';

// Build the query with filters
$where = [];
$params = [];
$types = '';

if ($search) {
    $where[] = "(activity_logs.action LIKE ? OR activity_logs.details LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'ss';
}
if ($admin_id) {
    $where[] = "activity_logs.admin_id = ?";
    $params[] = $admin_id;
    $types .= 'i';
}
if ($action) {
    $where[] = "activity_logs.action = ?";
    $params[] = $action;
    $types .= 's';
}

$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$sql = "SELECT activity_logs.*, admins.name AS admin_name FROM activity_logs LEFT JOIN admins ON activity_logs.admin_id = admins.id $where_sql ORDER BY activity_logs.created_at DESC";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
if (!$result) {
    die("Failed to fetch activity logs: " . $conn->error);
}

// Fetch unique actions for the action filter
$actions = [];
$actions_result = $conn->query("SELECT DISTINCT action FROM activity_logs ORDER BY action ASC");
if (!$actions_result) {
    die("Failed to fetch actions: " . $conn->error);
}
while ($row = $actions_result->fetch_assoc()) {
    $actions[] = $row['action'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Activity Logs</title>
    <link rel="stylesheet" href="../patient/assets/css/style.css">
    <link rel="stylesheet" href="../patient/assets/css/logo.css">
    <style>
        .filter-form input, .filter-form select {
            margin-right: 10px;
            margin-bottom: 10px;
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
    <div class="container">
        <div class="dashboard-nav">
            <a href="dashboard.php">Dashboard</a> |
            <a href="manage_users.php">Manage Users</a> |
            <a href="add_user.php">Add User</a> |
            <a href="activity_logs.php">Activity Logs</a> |
            <a href="logout.php">Logout</a>
        </div>
        <h2>Activity Logs</h2>

        <form method="get" class="filter-form">
            <input type="text" name="search" placeholder="Search action/details" value="<?php echo htmlspecialchars($search); ?>">
            <select name="admin_id">
                <option value="">All Admins</option>
                <?php foreach ($admins as $admin): ?>
                    <option value="<?php echo $admin['id']; ?>" <?php if ($admin_id == $admin['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($admin['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select name="action">
                <option value="">All Actions</option>
                <?php foreach ($actions as $act): ?>
                    <option value="<?php echo htmlspecialchars($act); ?>" <?php if ($action == $act) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($act); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Filter</button>
            <a href="activity_logs.php" style="margin-left:10px;">Reset</a>
        </form>

        <table>
            <tr>
                <th>Date/Time</th>
                <th>Admin</th>
                <th>Action</th>
                <th>Details</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                <td><?php echo htmlspecialchars($row['admin_name'] ?? 'System'); ?></td>
                <td><?php echo htmlspecialchars($row['action']); ?></td>
                <td><?php echo htmlspecialchars($row['details']); ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
       <script src="assets/js/global.js"></script>
</body>
</html>
