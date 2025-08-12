<?php
session_start();
include 'config/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch all users with role details
$sql = "SELECT u.*, ur.role_name FROM users u 
        JOIN user_roles ur ON u.role_id = ur.role_id 
        ORDER BY u.created_at DESC";
$result = mysqli_query($conn, $sql);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);
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
                                        <th>Last Updated</th>
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
                                                <td><?php echo date('M j, Y', strtotime($user['updated_at'])); ?></td>
                                                <td class="text-end">
                                                    <!-- View Details Button -->
                                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#userModal<?php echo $user['user_id']; ?>" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    
                                                    <!-- Edit Button -->
                                                    <!-- <a href="#" class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a> -->
                                                    
                                                    <!-- Delete Button -->
                                                    <!-- <a href="#" class="btn btn-sm btn-outline-danger" 
                                                       onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </a> -->
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
                            <p><strong>Last Updated:</strong> <?php echo date('M j, Y g:i A', strtotime($user['updated_at'])); ?></p>
                            
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
