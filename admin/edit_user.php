<?php
session_start();
include 'config/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Check if user ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: users.php');
    exit();
}

$user_id = (int)$_GET['id'];

// Fetch user details
$sql = "SELECT u.*, ur.role_name FROM users u 
        JOIN user_roles ur ON u.role_id = ur.role_id 
        WHERE u.user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// If user not found
if (!$user) {
    header('Location: users.php');
    exit();
}

// Fetch all roles for dropdown
$roles_sql = "SELECT * FROM user_roles";
$roles_result = mysqli_query($conn, $roles_sql);
$roles = mysqli_fetch_all($roles_result, MYSQLI_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role_id = $_POST['role_id'];
    
    // Update user details
    $update_sql = "UPDATE users SET username = ?, email = ?, role_id = ? WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($stmt, "ssii", $username, $email, $role_id, $user_id);
    
    if (mysqli_stmt_execute($stmt)) {
        // Redirect back to users list with success message
        header("Location: users.php?updated=1");
        exit();
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'layout.php'; ?>
    <title>Edit User - StayByte Admin</title>
</head>
<body>
    <div class="wrapper">
        <?php include 'sidebar.php'; ?>
        
        <main class="main-content position-relative border-radius-lg">
            <?php include 'navbar.php'; ?>
            
            <div class="container-fluid py-4">
                <div class="row">
                    <div class="col-12">
                        <div class="card mb-4">
                            <div class="card-header pb-0">
                                <h6>Edit User</h6>
                            </div>
                            <div class="card-body px-4 pt-0 pb-2">
                                <form method="POST">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="username" class="form-control-label">Username</label>
                                                <input class="form-control" type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email" class="form-control-label">Email</label>
                                                <input class="form-control" type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="role_id" class="form-control-label">Role</label>
                                                <select class="form-control" id="role_id" name="role_id" required>
                                                    <option value="">Select Role</option>
                                                    <?php foreach ($roles as $role): ?>
                                                        <option value="<?php echo $role['role_id']; ?>" <?php echo ($user['role_id'] == $role['role_id']) ? 'selected' : ''; ?>>
                                                            <?php echo ucfirst($role['role_name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mt-4">
                                        <a href="users.php" class="btn btn-secondary">Cancel</a>
                                        <button type="submit" class="btn btn-primary">Update User</button>
                                    </div>
                                </form>
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
