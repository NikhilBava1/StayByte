<?php
session_start();
include 'config/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Pagination variables
$limit = 10; // Number of entries per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get total number of users
$count_sql = "SELECT COUNT(*) as total FROM users u 
              JOIN user_roles ur ON u.role_id = ur.role_id";
$count_result = mysqli_query($conn, $count_sql);
$count_row = mysqli_fetch_assoc($count_result);
$total_users = $count_row['total'];
$total_pages = ceil($total_users / $limit);

// Fetch users with pagination
$sql = "SELECT u.*, ur.role_name FROM users u 
        JOIN user_roles ur ON u.role_id = ur.role_id 
        ORDER BY u.created_at DESC 
        LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $sql);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $user_id = (int)$_GET['id'];
    
    // Prevent deletion of the current admin user
    if ($user_id != $_SESSION['admin_id']) {
        $delete_sql = "DELETE FROM users WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $delete_sql);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            // Redirect to same page after deletion
            header("Location: users.php?page=$page&deleted=1");
            exit();
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'layout.php'; ?>
    <title>User Management - StayByte Admin</title>
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --danger-color: #f72585;
            --warning-color: #f8961e;
            --info-color: #4895ef;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --sidebar-width: 250px;
            --header-height: 70px;
            --transition: all 0.3s ease;
        }
        
        .role-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            transition: var(--transition);
        }
        .badge-admin { background-color: #dc3545; color: white; }
        .badge-student { background-color: #007bff; color: white; }
        .badge-staff { background-color: #28a745; color: white; }
        .badge-provider { background-color: #ffc107; color: #212529; }
        .page-header {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e9ecef;
        }
        .page-header h1 {
            font-weight: 700;
            color: var(--dark-color);
        }
        .page-header p {
            font-size: 1.1rem;
            color: #6c757d;
        }
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border-radius: 10px;
            transition: var(--transition);
        }
        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .table th {
            font-weight: 600;
            color: var(--dark-color);
            border-top: none;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
        }
        .btn {
            border-radius: 6px;
            font-weight: 500;
            transition: var(--transition);
        }
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .modal-content {
            border-radius: 10px;
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .modal-header {
            border-bottom: 1px solid #e9ecef;
            background-color: #f8f9fa;
            border-radius: 10px 10px 0 0 !important;
        }
        .modal-footer {
            border-top: 1px solid #e9ecef;
            background-color: #f8f9fa;
            border-radius: 0 0 10px 10px !important;
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
                                    <h1>User Management</h1>
                                    <p class="text-muted">Manage all users in the system</p>
                                </div>
                                <div class="col-md-6 text-end">
                                    <!-- <a href="#" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i> Add New User
                                    </a> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header bg-white">
                        <h5><i class="fas fa-users me-2"></i> User List</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Created At</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($users) > 0): ?>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td class="fw-bold">#<?php echo $user['user_id']; ?></td>
                                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                <td>
                                                    <span class="role-badge badge-<?php echo $user['role_name']; ?>">
                                                        <?php echo ucfirst($user['role_name']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                                <td class="text-end">
                                                    <!-- View Details Button -->
                                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#userModal<?php echo $user['user_id']; ?>" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    
                                                    <!-- Edit Button -->
                                                    <a href="edit_user.php?id=<?php echo $user['user_id']; ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    
                                                    <!-- Delete Button -->
                                                    <a href="users.php?action=delete&id=<?php echo $user['user_id']; ?>&page=<?php echo $page; ?>" class="btn btn-sm btn-outline-danger" 
                                                       onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <i class="fas fa-users fa-2x text-muted mb-3"></i>
                                                <p class="mb-0">No users found.</p>
                                                <p class="text-muted">There are currently no users in the system.</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination Controls -->
                        <?php if ($total_users > $limit): ?>
                        <nav aria-label="User list pagination" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <!-- Previous Button -->
                                <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="<?php echo ($page <= 1) ? '#' : '?page=' . ($page - 1); ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                
                                <!-- Page Numbers -->
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <!-- Next Button -->
                                <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="<?php echo ($page >= $total_pages) ? '#' : '?page=' . ($page + 1); ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                            <div class="text-center text-muted">
                                Showing <?php echo ($offset + 1); ?> to <?php echo min($offset + $limit, $total_users); ?> of <?php echo $total_users; ?> users
                            </div>
                        </nav>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- User Detail Modals -->
    <?php foreach ($users as $user): ?>
    <div class="modal fade" id="userModal<?php echo $user['user_id']; ?>" tabindex="-1" aria-labelledby="userModalLabel<?php echo $user['user_id']; ?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel<?php echo $user['user_id']; ?>">
                        User Details - <?php echo htmlspecialchars($user['username']); ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p><strong>User ID:</strong> <?php echo $user['user_id']; ?></p>
                            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                            <p><strong>Role:</strong> 
                                <span class="role-badge badge-<?php echo $user['role_name']; ?>">
                                    <?php echo ucfirst($user['role_name']); ?>
                                </span>
                            </p>
                            <p><strong>Created At:</strong> <?php echo date('M j, Y g:i A', strtotime($user['created_at'])); ?></p>
                            
                            <!-- Additional role-specific details -->
                            <?php 
                            // Fetch role-specific details
                            $role_details = null;
                            switch ($user['role_name']) {
                                case 'student':
                                    $sql = "SELECT * FROM students WHERE user_id = ?";
                                    break;
                                case 'admin':
                                    $sql = "SELECT * FROM admins WHERE id = ?";
                                    break;
                                default:
                                    $sql = null;
                                    break;
                            }
                            
                            if ($sql) {
                                $stmt = mysqli_prepare($conn, $sql);
                                mysqli_stmt_bind_param($stmt, "i", $user['user_id']);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                                $role_details = mysqli_fetch_assoc($result);
                                mysqli_stmt_close($stmt);
                            }
                            
                            if ($role_details):
                            ?>
                            <hr>
                            <h6>Role-Specific Details:</h6>
                            <?php foreach ($role_details as $key => $value): ?>
                                <?php if (!in_array($key, ['user_id', 'id'])): ?>
                                <p><strong><?php echo ucfirst(str_replace('_', ' ', $key)); ?>:</strong> 
                                    <?php echo htmlspecialchars($value); ?>
                                </p>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
