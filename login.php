<?php
session_start();

// If user is already logged in, redirect to appropriate dashboard
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role_name'] ?? $_SESSION['role'];
    
    switch ($role) {
        case 'admin':
            header('Location: admin_dashboard.php');
            break;
        case 'student':
            header('Location: student_dashboard.php');
            break;
        case 'staff':
            header('Location: staff_dashboard.php');
            break;
        case 'teacher':
            header('Location: teacher_dashboard.php');
            break;
        case 'provider':
            header('Location: provider_dashboard.php');
            break;
        default:
            header('Location: index.php');
    }
    exit();
}

$error_message = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'config/db.php';
    
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    
    if (empty($username) || empty($password) || empty($role)) {
        $error_message = 'Please fill in all fields.';
    } else {
        // Get role_id based on role name
        $role_sql = "SELECT role_id FROM user_roles WHERE role_name = ?";
        $role_stmt = mysqli_prepare($conn, $role_sql);
        mysqli_stmt_bind_param($role_stmt, "s", $role);
        mysqli_stmt_execute($role_stmt);
        $role_result = mysqli_stmt_get_result($role_stmt);
        $role_data = mysqli_fetch_assoc($role_result);
        
        if ($role_data) {
            $role_id = $role_data['role_id'];
            
            // Check user credentials   
            $sql = "SELECT id, username, email, password, role_id FROM users WHERE username = ? AND role_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "si", $username, $role_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if ($user = mysqli_fetch_assoc($result)) {
                // Verify password (assuming passwords are hashed)
                if (password_verify($password, $user['password']) || $password === $user['password']) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];      
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role_id'] = $user['role_id'];
                    $_SESSION['role_name'] = $role;
                    
                    // Set flag to show feedback modal after login
                    $_SESSION['show_feedback_modal'] = true;
                    $_SESSION['just_logged_in'] = true;
                    
                    // Redirect based on role
                    switch ($role) {
                        case 'admin':
                            header('Location: admin_dashboard.php');
                            break;
                        case 'student':
                            header('Location: student_dashboard.php');
                            break;
                        case 'staff':
                            header('Location: staff_dashboard.php');
                            break;
                        case 'teacher':
                            header('Location: teacher_dashboard.php');
                            break;
                        case 'provider':
                            header('Location: provider_dashboard.php');
                            break;
                        default:
                            header('Location: index.php');
                    }
                    exit();
                } else {
                    $error_message = 'Invalid password.';
                }
            } else {
                $error_message = 'User not found with this role.';
            }
        } else {
            $error_message = 'Invalid role selected.';
        }
        
        mysqli_close($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - StayByte</title>
    <link rel="stylesheet" href="css/style-core.css" type="text/css" media="all" />
    <link rel="stylesheet" href="css/hotale-style-customafa1.css" type="text/css" media="all" />
    <link rel="stylesheet" href="css/login-form.css" type="text/css" media="all" />
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Jost', sans-serif;
        }
        
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            position: relative;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
            font-weight: 600;
        }
        
        .login-header p {
            color: #666;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #007cba;
            box-shadow: 0 0 0 3px rgba(0, 124, 186, 0.1);
        }
        
        .login-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #007cba 0%, #005a8b 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 124, 186, 0.3);
        }
        
        .error-message {
            background: #ffebee;
            color: #c62828;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
            border-left: 4px solid #f44336;
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: #007cba;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }
        
        .back-link a:hover {
            color: #005a8b;
        }
        
        .register-link {
            text-align: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e1e5e9;
        }
        
        .register-link a {
            color: #007cba;
            text-decoration: none;
            font-weight: 500;
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Welcome Back</h1>
            <p>Sign in to your StayByte account</p>
        </div>
        
        <?php if (!empty($error_message)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username or Email</label>
                <input type="text" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="role">Login As</label>
                <select id="role" name="role" required>
                    <option value="">Select your role</option>
                    <option value="student" <?php echo (isset($_POST['role']) && $_POST['role'] == 'student') ? 'selected' : ''; ?>>Student</option>
                    <option value="admin" <?php echo (isset($_POST['role']) && $_POST['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                    <option value="staff" <?php echo (isset($_POST['role']) && $_POST['role'] == 'staff') ? 'selected' : ''; ?>>Staff</option>
                    <option value="teacher" <?php echo (isset($_POST['role']) && $_POST['role'] == 'teacher') ? 'selected' : ''; ?>>Teacher</option>
                    <option value="provider" <?php echo (isset($_POST['role']) && $_POST['role'] == 'provider') ? 'selected' : ''; ?>>Provider</option>
                </select>
            </div>
            
            <button type="submit" class="login-btn">Sign In</button>
        </form>
        
        <div class="register-link">
            <p>Don't have an account? <a href="register.php">Create one here</a></p>
        </div>
        
        <div class="back-link">
            <a href="index.php">← Back to Home</a>
        </div>
    </div>
</body>
</html> 