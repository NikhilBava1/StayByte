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
$sql = "SELECT * FROM admins WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $admin_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$admin = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    
    $sql = "UPDATE admins SET first_name=?, last_name=?, email=?, phone=?, address=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssssi", $first_name, $last_name, $email, $phone, $address, $admin_id);
    
    if (mysqli_stmt_execute($stmt)) {
        // Update session variables
        $_SESSION['admin_name'] = $first_name . ' ' . $last_name;
        
        // Refresh admin data
        $admin['first_name'] = $first_name;
        $admin['last_name'] = $last_name;
        $admin['email'] = $email;
        $admin['phone'] = $phone;
        $admin['address'] = $address;
        
        $message = 'Profile updated successfully.';
    } else {
        $error = 'Error updating profile. Please try again.';
    }
    mysqli_stmt_close($stmt);
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verify current password
    if (password_verify($current_password, $admin['password'])) {
        // Check if new passwords match
        if ($new_password === $confirm_password) {
            // Check password strength
            if (strlen($new_password) >= 6) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                $sql = "UPDATE admins SET password=? WHERE id=?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "si", $hashed_password, $admin_id);
                
                if (mysqli_stmt_execute($stmt)) {
                    $message = 'Password changed successfully.';
                } else {
                    $error = 'Error changing password. Please try again.';
                }
                mysqli_stmt_close($stmt);
            } else {
                $error = 'New password must be at least 6 characters long.';
            }
        } else {
            $error = 'New passwords do not match.';
        }
    } else {
        $error = 'Current password is incorrect.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'layout.php'; ?>
    <title>Admin Profile - StayByte</title>
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
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            border-radius: 10px 10px 0 0 !important;
            font-weight: 600;
        }
        .form-control, .form-select {
            border-radius: 6px;
            padding: 0.5rem 0.75rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }
        .btn {
            border-radius: 6px;
            font-weight: 500;
            transition: var(--transition);
        }
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        .btn-warning {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
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
                                    <h1>Admin Profile</h1>
                                    <p class="text-muted">Manage your profile information and security settings</p>
                                </div>
                                <div class="col-md-6 text-end">
                                    <div class="d-flex justify-content-end">
                                        <div class="avatar-container me-3">
                                            <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-size: 1.2rem;">
                                                <?php echo substr(htmlspecialchars($admin['first_name']), 0, 1) . substr(htmlspecialchars($admin['last_name']), 0, 1); ?>
                                            </div>
                                        </div>
                                        <div>
                                            <h5 class="mb-0"><?php echo htmlspecialchars($admin['first_name'] . ' ' . $admin['last_name']); ?></h5>
                                            <p class="text-muted mb-0"><?php echo ucfirst(htmlspecialchars($admin['role'])); ?> Admin</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                
                <div class="row">
                    <!-- Profile Information -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5><i class="fas fa-user me-2"></i> Profile Information</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="first_name" class="form-label">First Name</label>
                                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($admin['first_name']); ?>" required>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="last_name" class="form-label">Last Name</label>
                                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($admin['last_name']); ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($admin['phone']); ?>">
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="admin_id" class="form-label">Admin ID</label>
                                            <input type="text" class="form-control" id="admin_id" value="<?php echo htmlspecialchars($admin['admin_id']); ?>" readonly>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($admin['address']); ?></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="role" class="form-label">Role</label>
                                        <input type="text" class="form-control" id="role" value="<?php echo htmlspecialchars(ucfirst($admin['role'])); ?>" readonly>
                                    </div>
                                    
                                    <div class="d-grid mt-4">
                                        <button type="submit" name="update_profile" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i> Update Profile
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Change Password -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5><i class="fas fa-lock me-2"></i> Change Password</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Current Password</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                                        <div class="form-text text-muted">Password must be at least 6 characters long.</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    </div>
                                    
                                    <div class="d-grid mt-4">
                                        <button type="submit" name="change_password" class="btn btn-warning">
                                            <i class="fas fa-key me-2"></i> Change Password
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Account Information -->
                        <div class="card mt-4">
                            <div class="card-header bg-white">
                                <h5><i class="fas fa-info-circle me-2"></i> Account Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="text-center p-3 bg-light rounded">
                                            <i class="fas fa-calendar-plus fa-2x text-primary mb-2"></i>
                                            <p class="mb-1"><strong>Account Created</strong></p>
                                            <p class="mb-0 text-muted"><?php echo date('M j, Y', strtotime($admin['created_at'])); ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="text-center p-3 bg-light rounded">
                                            <i class="fas fa-history fa-2x text-info mb-2"></i>
                                            <p class="mb-1"><strong>Last Updated</strong></p>
                                            <p class="mb-0 text-muted"><?php echo date('M j, Y', strtotime($admin['updated_at'])); ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="text-center p-3 bg-light rounded">
                                            <i class="fas fa-sign-in-alt fa-2x text-success mb-2"></i>
                                            <p class="mb-1"><strong>Last Login</strong></p>
                                            <p class="mb-0 text-muted">Just now</p>
                                        </div>
                                    </div>
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
