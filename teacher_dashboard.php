<?php
session_start();

// Check if user is logged in and is a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: index.php');
    exit();
}

include 'config/db.php';

// Get teacher details
$teacher_id = $_SESSION['user_id'];
$sql = "SELECT * FROM teachers WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $teacher_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$teacher = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - StayByte</title>
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
            <h1>Teacher Dashboard</h1>
            <p>Welcome back, <?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?>!</p>
            <p><strong>Teacher ID:</strong> <?php echo htmlspecialchars($teacher['teacher_id']); ?></p>
            <p><strong>Department:</strong> <?php echo htmlspecialchars($teacher['department']); ?></p>
        </div>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h3>📚 My Classes</h3>
                <p>Manage your assigned classes and course materials.</p>
                <p><em>Feature coming soon...</em></p>
            </div>

            <div class="dashboard-card">
                <h3>📝 Grade Management</h3>
                <p>Enter and manage student grades and assessments.</p>
                <p><em>Feature coming soon...</em></p>
            </div>

            <div class="dashboard-card">
                <h3>📅 Class Schedule</h3>
                <p>View your teaching schedule and class timings.</p>
                <p><em>Feature coming soon...</em></p>
            </div>

            <div class="dashboard-card">
                <h3>📊 Student Performance</h3>
                <p>Track student progress and generate reports.</p>
                <p><em>Feature coming soon...</em></p>
            </div>

            <div class="dashboard-card">
                <h3>📋 Assignments</h3>
                <p>Create and manage assignments for your students.</p>
                <p><em>Feature coming soon...</em></p>
            </div>

            <div class="dashboard-card">
                <h3>👥 Profile Settings</h3>
                <p>Update your personal information and teaching preferences.</p>
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