<?php
session_start();
include '../config/db.php';

// Check if user is logged in and is a provider
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header('Location: ../login.php');
    exit();
}

// Get provider details
$provider_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $provider_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$provider = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Get provider statistics      
// Total rooms for this provider
$rooms_sql = "SELECT COUNT(*) as total FROM rooms WHERE room_provider_id = ?";
$rooms_stmt = mysqli_prepare($conn, $rooms_sql);
mysqli_stmt_bind_param($rooms_stmt, "i", $provider_id);
mysqli_stmt_execute($rooms_stmt);
$rooms_result = mysqli_stmt_get_result($rooms_stmt);
$total_rooms = mysqli_fetch_assoc($rooms_result)['total'];

// Active rooms for this provider
$active_rooms_sql = "SELECT COUNT(*) as active FROM rooms WHERE status = 'Active' AND room_provider_id = ?";
$active_rooms_stmt = mysqli_prepare($conn, $active_rooms_sql);
mysqli_stmt_bind_param($active_rooms_stmt, "i", $provider_id);
mysqli_stmt_execute($active_rooms_stmt);
$active_rooms_result = mysqli_stmt_get_result($active_rooms_stmt);
$active_rooms = mysqli_fetch_assoc($active_rooms_result)['active'];

// Total meals for this provider
$meals_sql = "SELECT COUNT(*) as total FROM meals WHERE meal_provider_id = ?";
$meals_stmt = mysqli_prepare($conn, $meals_sql);
mysqli_stmt_bind_param($meals_stmt, "i", $provider_id);
mysqli_stmt_execute($meals_stmt);
$meals_result = mysqli_stmt_get_result($meals_stmt);
$total_meals = mysqli_fetch_assoc($meals_result)['total'];

// Active meals for this provider
$active_meals_sql = "SELECT COUNT(*) as active FROM meals WHERE status = 'Active' AND meal_provider_id = ?";
$active_meals_stmt = mysqli_prepare($conn, $active_meals_sql);
mysqli_stmt_bind_param($active_meals_stmt, "i", $provider_id);
mysqli_stmt_execute($active_meals_stmt);
$active_meals_result = mysqli_stmt_get_result($active_meals_stmt);
$active_meals = mysqli_fetch_assoc($active_meals_result)['active'];

// Recent rooms for this provider
$recent_rooms_sql = "SELECT * FROM rooms WHERE room_provider_id = ? ORDER BY room_id DESC LIMIT 5";
$recent_rooms_stmt = mysqli_prepare($conn, $recent_rooms_sql);
mysqli_stmt_bind_param($recent_rooms_stmt, "i", $provider_id);
mysqli_stmt_execute($recent_rooms_stmt);
$recent_rooms_result = mysqli_stmt_get_result($recent_rooms_stmt);
$recent_rooms = mysqli_fetch_all($recent_rooms_result, MYSQLI_ASSOC);

// Recent meals for this provider
$recent_meals_sql = "SELECT * FROM meals WHERE meal_provider_id = ? ORDER BY meal_id DESC LIMIT 5";
$recent_meals_stmt = mysqli_prepare($conn, $recent_meals_sql);
mysqli_stmt_bind_param($recent_meals_stmt, "i", $provider_id);
mysqli_stmt_execute($recent_meals_stmt);
$recent_meals_result = mysqli_stmt_get_result($recent_meals_stmt);
$recent_meals = mysqli_fetch_all($recent_meals_result, MYSQLI_ASSOC);


// Recent enquiries for this provider (related to their rooms or meals)
$recent_enquiries_sql = "SELECT e.*, u.username FROM enquiries e 
                         LEFT JOIN rooms r ON e.room_id = r.room_id 
                         LEFT JOIN meals m ON e.meal_id = m.meal_id 
                         JOIN users u ON e.user_id = u.user_id 
                         WHERE (r.room_provider_id = ? OR m.meal_provider_id = ?) 
                         ORDER BY e.created_at DESC LIMIT 5";
$recent_enquiries_stmt = mysqli_prepare($conn, $recent_enquiries_sql);
mysqli_stmt_bind_param($recent_enquiries_stmt, "ii", $provider_id, $provider_id);
mysqli_stmt_execute($recent_enquiries_stmt);
$recent_enquiries_result = mysqli_stmt_get_result($recent_enquiries_stmt);
$recent_enquiries = mysqli_fetch_all($recent_enquiries_result, MYSQLI_ASSOC);

// Total enquiries for this provider (related to their rooms or meals)
$total_enquiries_sql = "SELECT COUNT(*) as total FROM enquiries e 
                        LEFT JOIN rooms r ON e.room_id = r.room_id 
                        LEFT JOIN meals m ON e.meal_id = m.meal_id 
                        WHERE (r.room_provider_id = ? OR m.meal_provider_id = ?)";
$total_enquiries_stmt = mysqli_prepare($conn, $total_enquiries_sql);
mysqli_stmt_bind_param($total_enquiries_stmt, "ii", $provider_id, $provider_id);
mysqli_stmt_execute($total_enquiries_stmt);
$total_enquiries_result = mysqli_stmt_get_result($total_enquiries_stmt);
$total_enquiries = mysqli_fetch_assoc($total_enquiries_result)['total'];

// Pending enquiries for this provider (related to their rooms or meals)
$pending_enquiries_sql = "SELECT COUNT(*) as total FROM enquiries e 
                          LEFT JOIN rooms r ON e.room_id = r.room_id 
                          LEFT JOIN meals m ON e.meal_id = m.meal_id 
                          WHERE (r.room_provider_id = ? OR m.meal_provider_id = ?) AND e.status = 'open'";
$pending_enquiries_stmt = mysqli_prepare($conn, $pending_enquiries_sql);
mysqli_stmt_bind_param($pending_enquiries_stmt, "ii", $provider_id, $provider_id);
mysqli_stmt_execute($pending_enquiries_stmt);
$pending_enquiries_result = mysqli_stmt_get_result($pending_enquiries_stmt);
$pending_enquiries = mysqli_fetch_assoc($pending_enquiries_result)['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provider Dashboard - StayByte</title>
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
                            <p class="text-muted">Welcome back, <?php echo htmlspecialchars($provider['username']); ?>!</p>
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
                                                <th>Message</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($recent_enquiries) > 0): ?>
                                                <?php foreach ($recent_enquiries as $enquiry): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($enquiry['username']); ?></td>
                                                        <td><?php echo htmlspecialchars(substr($enquiry['message'], 0, 30)) . (strlen($enquiry['message']) > 30 ? '...' : ''); ?></td>
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
