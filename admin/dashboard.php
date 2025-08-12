<?php
session_start();
include 'config/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Get admin details
$admin_id = $_SESSION['admin_id'];
$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $admin_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$admin = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Get statistics
// Total rooms
$rooms_sql = "SELECT COUNT(*) as total FROM rooms";
$rooms_result = mysqli_query($conn, $rooms_sql);
$total_rooms = mysqli_fetch_assoc($rooms_result)['total'];

// Active rooms
$active_rooms_sql = "SELECT COUNT(*) as active FROM rooms WHERE status = 'Active'";
$active_rooms_result = mysqli_query($conn, $active_rooms_sql);
$active_rooms = mysqli_fetch_assoc($active_rooms_result)['active'];

// Total meals
$meals_sql = "SELECT COUNT(*) as total FROM meals";
$meals_result = mysqli_query($conn, $meals_sql);
$total_meals = mysqli_fetch_assoc($meals_result)['total'];

// Active meals
$active_meals_sql = "SELECT COUNT(*) as active FROM meals WHERE status = 'Active'";
$active_meals_result = mysqli_query($conn, $active_meals_sql);
$active_meals = mysqli_fetch_assoc($active_meals_result)['active'];

// Pending enquiries
$enquiries_sql = "SELECT COUNT(*) as pending FROM enquiries WHERE status = 'open'";
$enquiries_result = mysqli_query($conn, $enquiries_sql);
$pending_enquiries = mysqli_fetch_assoc($enquiries_result)['pending'];

// Recent enquiries
$recent_enquiries_sql = "SELECT e.*, u.username FROM enquiries e 
                         JOIN users u ON e.user_id = u.user_id 
                         ORDER BY e.created_at DESC LIMIT 5";
$recent_enquiries_result = mysqli_query($conn, $recent_enquiries_sql);
$recent_enquiries = mysqli_fetch_all($recent_enquiries_result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - StayByte Admin</title>
    <?php include 'layout.php'; ?>
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>
        
        <main class="main-content">
            <div class="container-fluid">
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="page-header">
                            <h1>Dashboard</h1>
                            <p class="text-muted">Welcome back, <?php echo htmlspecialchars($admin['username']); ?>!</p>
                        </div>
                    </div>
                </div>
                
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-4">
                        <div class="card stat-card rooms h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-muted">Total Rooms</h6>
                                        <div class="stat-number text-success"><?php echo $total_rooms; ?></div>
                                        <div class="text-muted"><?php echo $active_rooms; ?> Active</div>
                                    </div>
                                    <div class="icon text-success stat-icon">
                                        <i class="fas fa-bed bounce"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="card stat-card meals h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-muted">Total Meals</h6>
                                        <div class="stat-number text-warning"><?php echo $total_meals; ?></div>
                                        <div class="text-muted"><?php echo $active_meals; ?> Active</div>
                                    </div>
                                    <div class="icon text-warning stat-icon">
                                        <i class="fas fa-utensils bounce"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="card stat-card enquiries h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-muted">Pending Enquiries</h6>
                                        <div class="stat-number text-danger"><?php echo $pending_enquiries; ?></div>
                                        <div class="text-muted">Need attention</div>
                                    </div>
                                    <div class="icon text-danger stat-icon">
                                        <i class="fas fa-question-circle bounce"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Enquiries -->
                <div class="row">
                    <div class="col-md-12 mb-4">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5><i class="fas fa-question-circle me-2"></i> Recent Enquiries</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover recent-table">
                                        <thead>
                                            <tr>
                                                <th>User</th>
                                                <th>Subject</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($recent_enquiries) > 0): ?>
                                                <?php foreach ($recent_enquiries as $enquiry): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($enquiry['username']); ?></td>
                                                        <td><?php echo htmlspecialchars(substr($enquiry['subject'], 0, 20)) . (strlen($enquiry['subject']) > 20 ? '...' : ''); ?></td>
                                                        <td>
                                                            <span class="status-badge badge-<?php echo $enquiry['status'] == 'open' ? 'open' : 'resolved'; ?>">
                                                                <?php echo ucfirst($enquiry['status']); ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo date('M j, Y', strtotime($enquiry['created_at'])); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="4" class="text-center">No enquiries found</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
