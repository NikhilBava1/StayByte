<?php
session_start();
include 'config/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Handle meal deletion
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql = "DELETE FROM meals WHERE meal_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $delete_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header('Location: meals.php?msg=deleted');
    exit();
}

// Fetch all meals
$sql = "SELECT * FROM meals ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
$meals = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'layout.php'; ?>
    <title>Meal Management - StayByte Admin</title>
    <style>
        .meal-image {
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
                                    <h1>Meal Management</h1>
                                    <p class="text-muted">Manage all meals in the system</p>
                                </div>
                                <div class="col-md-6 text-end">
                                    <a href="add_edit_meal.php" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i> Add New Meal
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Meal deleted successfully.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header bg-white">
                        <h5><i class="fas fa-utensils me-2"></i> Meal List</h5>
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
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($meals) > 0): ?>
                                        <?php foreach ($meals as $meal): ?>
                                            <tr>
                                                <td><?php echo $meal['meal_id']; ?></td>
                                                <td>
                                                    <?php if (!empty($meal['image_url'])): ?>
                                                        <img src="../<?php echo $meal['image_url']; ?>" alt="<?php echo htmlspecialchars($meal['meal_title']); ?>" class="meal-image">
                                                    <?php else: ?>
                                                        <div class="bg-light text-center" style="width: 80px; height: 60px; line-height: 60px; border-radius: 5px;">
                                                            <i class="fas fa-image text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($meal['meal_title']); ?></td>
                                                <td>$<?php echo number_format($meal['price'], 2); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo ($meal['meal_type'] == 'Veg') ? 'success' : 'danger'; ?>">
                                                        <?php echo $meal['meal_type']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($meal['rating'] > 0): ?>
                                                        <span class="badge bg-warning">
                                                            <?php echo $meal['rating']; ?> <i class="fas fa-star"></i>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-muted">N/A</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="status-<?php echo strtolower($meal['status']); ?>">
                                                        <?php echo $meal['status']; ?>
                                                    </span>
                                                </td>
                                                <td class="action-buttons">
                                                    <a href="add_edit_meal.php?id=<?php echo $meal['meal_id']; ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="?delete_id=<?php echo $meal['meal_id']; ?>" class="btn btn-sm btn-outline-danger" 
                                                       onclick="return confirm('Are you sure you want to delete this meal? This action cannot be undone.')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center">No meals found. <a href="add_edit_meal.php">Add your first meal</a>.</td>
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
