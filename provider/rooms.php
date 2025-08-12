<?php
session_start();
include '../config/db.php';

// Check if user is logged in and is a provider
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header('Location: ../login.php');
    exit();
}

$provider_id = $_SESSION['user_id'];

// Handle room deletion (only for this provider's rooms)
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    // First check if this room belongs to the provider
    $check_sql = "SELECT room_id FROM rooms WHERE room_id = ? AND room_provider_id = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "ii", $delete_id, $provider_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    
    if (mysqli_num_rows($check_result) > 0) {
        // Room belongs to this provider, proceed with deletion
        $sql = "DELETE FROM rooms WHERE room_id = ? AND room_provider_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $delete_id, $provider_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header('Location: rooms.php?msg=deleted');
    } else {
        // Room doesn't belong to this provider
        header('Location: rooms.php?msg=unauthorized');
    }
    exit();
}

// Fetch only this provider's rooms
$sql = "SELECT * FROM rooms WHERE room_provider_id = ? ORDER BY created_at DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $provider_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$rooms = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'layout.php'; ?>
    <title>Room Management - StayByte Admin</title>
    <style>
        .room-image {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
        .status-active {
            background-color: #d4edda;
            color: #155724;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        .action-buttons a {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>
        
        <main class="main-content">
            <div class="container-fluid">
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="page-header">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h1>Room Management</h1>
                                    <p class="text-muted">Manage all rooms in the system</p>
                                </div>
                                <div class="col-md-6 text-end">
                                    <a href="add_edit_room.php" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i> Add New Room
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php 
                    if ($_GET['msg'] == 'deleted') {
                        echo 'Room deleted successfully.';
                    } elseif ($_GET['msg'] == 'added') {
                        echo 'Room added successfully.';
                    } elseif ($_GET['msg'] == 'updated') {
                        echo 'Room updated successfully.';
                    }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header bg-white">
                        <h5><i class="fas fa-bed me-2"></i> Room List</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Image</th>
                                        <th>Title</th>
                                        <th>Price</th>
                                        <th>Bed Size</th>
                                        <th>Capacity</th>
                                        <th>Rating</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($rooms) > 0): ?>
                                        <?php foreach ($rooms as $room): ?>
                                            <tr>
                                                <td><?php echo $room['room_id']; ?></td>
                                                <td>
                                                    <?php if (!empty($room['room_image'])): ?>
                                                        <img src="../<?php echo $room['room_image']; ?>" alt="<?php echo htmlspecialchars($room['title']); ?>" class="room-image">
                                                    <?php else: ?>
                                                        <div class="bg-light text-center" style="width: 80px; height: 60px; line-height: 60px; border-radius: 5px;">
                                                            <i class="fas fa-image text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($room['title']); ?></td>
                                                <td>$<?php echo number_format($room['price'], 2); ?></td>
                                                <td><?php echo htmlspecialchars($room['bed_size']); ?></td>
                                                <td><?php echo $room['guest_capacity']; ?></td>
                                                <td>
                                                    <?php if ($room['rating'] > 0): ?>
                                                        <span class="badge bg-warning">
                                                            <?php echo $room['rating']; ?> <i class="fas fa-star"></i>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-muted">N/A</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="status-<?php echo strtolower($room['status']); ?>">
                                                        <?php echo $room['status']; ?>
                                                    </span>
                                                </td>
                                                <td class="action-buttons">
                                                    <a href="add_edit_room.php?id=<?php echo $room['room_id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="?delete_id=<?php echo $room['room_id']; ?>" class="btn btn-sm btn-outline-danger" title="Delete" 
                                                       onclick="return confirm('Are you sure you want to delete this room? This action cannot be undone.')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="9" class="text-center">No rooms found. <a href="add_edit_room.php">Add your first room</a>.</td>
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
