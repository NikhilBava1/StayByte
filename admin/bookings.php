<?php
session_start();
include 'config/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Handle booking status update
if (isset($_POST['update_status'])) {
    $booking_id = intval($_POST['booking_id']);
    $status = $_POST['status'];
    
    $sql = "UPDATE bookings SET status=? WHERE booking_id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $status, $booking_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $message = 'Booking status updated successfully.';
    } else {
        $error = 'Error updating booking status. Please try again.';
    }
    mysqli_stmt_close($stmt);
}


// Fetch all bookings with user and room details
$sql = "SELECT b.*, u.username, r.title as room_title FROM bookings b 
        JOIN users u ON b.user_id = u.user_id 
        JOIN rooms r ON b.room_id = r.room_id 
        ORDER BY b.booking_date DESC";
$result = mysqli_query($conn, $sql);
$bookings = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'layout.php'; ?>
    <title>Booking Management - StayByte Admin</title>
    <style>
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .badge-pending { background-color: #ffc107; color: #212529; }
        .badge-active { background-color: #28a745; color: white; }
        .badge-completed { background-color: #17a2b8; color: white; }
        .badge-cancelled { background-color: #dc3545; color: white; }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>
        
        <main class="main-content">
            <div class="container-fluid">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h1>Booking Management</h1>
                        <p class="text-muted">Manage all bookings in the system</p>
                    </div>
                    <div class="col-md-6 text-end">
                        <!-- <a href="#" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Booking
                        </a> -->
                    </div>
                </div>
                
                <?php if (isset($message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Room</th>
                                        <th>Check-in</th>
                                        <th>Check-out</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Booking Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($bookings) > 0): ?>
                                        <?php foreach ($bookings as $booking): ?>
                                            <tr>
                                                <td><?php echo $booking['booking_id']; ?></td>
                                                <td><?php echo htmlspecialchars($booking['username']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['room_title']); ?></td>
                                                <td><?php echo date('M j, Y', strtotime($booking['check_in_date'])); ?></td>
                                                <td><?php echo date('M j, Y', strtotime($booking['check_out_date'])); ?></td>
                                                <td>$<?php echo number_format($booking['total_amount'], 2); ?></td>
                                                <td>
                                                    <span class="status-badge badge-<?php echo $booking['status']; ?>">
                                                        <?php echo ucfirst($booking['status']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('M j, Y', strtotime($booking['booking_date'])); ?></td>
                                                <td>
                                                    <!-- Status Update Form -->
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                                        <select name="status" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                                            <option value="pending" <?php echo ($booking['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                                            <option value="active" <?php echo ($booking['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                                            <option value="completed" <?php echo ($booking['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                                            <option value="cancelled" <?php echo ($booking['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                                        </select>
                                                        <input type="hidden" name="update_status" value="1">
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="9" class="text-center">No bookings found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
