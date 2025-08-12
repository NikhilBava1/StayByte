<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_name'] != 'admin') {
    header('Location: index.php');
    exit();
}

include 'config/db.php';

// Get admin details
$admin_id = $_SESSION['user_id'];
$sql = "SELECT * FROM admins WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $admin_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$admin = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - StayByte</title>
    <link rel="stylesheet" href="css/style-core.css" type="text/css" media="all" />
    <link rel="stylesheet" href="css/hotale-style-customafa1.css" type="text/css" media="all" />
    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .dashboard-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #dc3545;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }
        .dashboard-card {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            border-left: 4px solid #dc3545;
        }
        .dashboard-card h3 {
            color: #dc3545;
            margin-bottom: 15px;
        }
        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .back-btn:hover {
            background: #c82333;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <a href="index.php" class="back-btn">← Back to Home</a>
        
        <div class="dashboard-header">
            <h1>Admin Dashboard</h1>
            <p>Welcome back, <?php echo htmlspecialchars($admin['first_name'] . ' ' . $admin['last_name']); ?>!</p>
            <p><strong>Admin ID:</strong> <?php echo htmlspecialchars($admin['admin_id']); ?></p>
            <p><strong>Role:</strong> <?php echo htmlspecialchars(ucfirst($admin['role'])); ?></p>
        </div>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h3>👥 User Management</h3>
                <p>Manage students, teachers, and other users in the system.</p>
                <p><em>Feature coming soon...</em></p>
            </div>

            <div class="dashboard-card">
                <h3>📚 Course Management</h3>
                <p>Create and manage courses, subjects, and academic programs.</p>
                <p><em>Feature coming soon...</em></p>
            </div>

            <div class="dashboard-card">
                <h3>📊 System Reports</h3>
                <p>Generate reports on academic performance and system usage.</p>
                <p><em>Feature coming soon...</em></p>
            </div>

            <div class="dashboard-card">
                <h3>⚙️ System Settings</h3>
                <p>Configure system parameters and administrative settings.</p>
                <p><em>Feature coming soon...</em></p>
            </div>

            <div class="dashboard-card">
                <h3>📅 Academic Calendar</h3>
                <p>Manage academic calendar, holidays, and important dates.</p>
                <p><em>Feature coming soon...</em></p>
            </div>

            <div class="dashboard-card">
                <h3>🔒 Security & Permissions</h3>
                <p>Manage user permissions and system security settings.</p>
                <p><em>Feature coming soon...</em></p>
            </div>
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <a href="logout.php" style="padding: 10px 20px; background: #dc3545; color: white; text-decoration: none; border-radius: 5px;">Logout</a>
        </div>
    </div>

    <?php mysqli_close($conn); ?>
</body>
</html> 