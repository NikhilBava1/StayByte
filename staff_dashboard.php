<?php
session_start();

// Check if user is logged in and is a staff member (role_id = 3)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 3) {
    header('Location: index.php');
    exit();
}

include 'config/db.php';

// Get staff details
$staff_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $staff_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$staff = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - StayByte</title>
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
            border-bottom: 2px solid #28a745;
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
            border-left: 4px solid #28a745;
        }
        .dashboard-card h3 {
            color: #28a745;
            margin-bottom: 15px;
        }
        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .back-btn:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <a href="index.php" class="back-btn">← Back to Home</a>
        
        <div class="dashboard-header">
            <h1>Staff Dashboard</h1>
            <p>Welcome back, <?php echo htmlspecialchars($staff['username']); ?>!</p>
            <p><strong>Staff ID:</strong> <?php echo htmlspecialchars($staff['user_id']); ?></p>
            <p><strong>Role:</strong> Hotel Staff</p>
        </div>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h3>🏨 Room Management</h3>
                <p>Manage room availability, cleaning status, and maintenance.</p>
                <p><em>Feature coming soon...</em></p>
            </div>

            <div class="dashboard-card">
                <h3>📋 Guest Services</h3>
                <p>Handle guest check-ins, check-outs, and special requests.</p>
                <p><em>Feature coming soon...</em></p>
            </div>

            <div class="dashboard-card">
                <h3>📅 Booking Management</h3>
                <p>View and manage room bookings and reservations.</p>
                <p><em>Feature coming soon...</em></p>
            </div>

            <div class="dashboard-card">
                <h3>🧹 Housekeeping</h3>
                <p>Track room cleaning status and maintenance requests.</p>
                <p><em>Feature coming soon...</em></p>
            </div>

            <div class="dashboard-card">
                <h3>🍽️ Food & Beverage</h3>
                <p>Manage meal orders and dining services.</p>
                <p><em>Feature coming soon...</em></p>
            </div>

            <div class="dashboard-card">
                <h3>📊 Daily Reports</h3>
                <p>Generate daily occupancy and service reports.</p>
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