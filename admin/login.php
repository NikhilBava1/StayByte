<?php
session_start();
include 'config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        // Check if user exists in admins table
        $sql = "SELECT * FROM admins WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($admin = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $admin['password'])) {
                // Login successful
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_name'] = $admin['first_name'] . ' ' . $admin['last_name'];
                $_SESSION['admin_role'] = $admin['role'];
                
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Invalid username or password.';
            }
        } else {
            $error = 'Invalid username or password.';
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - StayByte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            padding: 40px;
            width: 100%;
            max-width: 450px;
            backdrop-filter: blur(10px);
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h2 {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .login-header p {
            color: #7f8c8d;
        }
        .form-control {
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        .btn-login {
            background: linear-gradient(to right, #3498db, #2c3e50);
            border: none;
            padding: 12px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
        }
        .btn-login:hover {
            background: linear-gradient(to right, #2980b9, #1a2530);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .error-message {
            background: #e74c3c;
            color: white;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: <?php echo empty($error) ? 'none' : 'block'; ?>;
        }
        .input-group-text {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-right: none;
            border-radius: 8px 0 0 8px;
        }
        .form-control {
            border-left: none;
            border-radius: 0 8px 8px 0;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h2><i class="fas fa-user-shield"></i> Admin Login</h2>
            <p>Welcome to StayByte Admin Panel</p>
        </div>
        
        <div class="error-message" id="error-message">
            <?php echo $error; ?>
        </div>
        
        <form method="POST" action="">
            <div class="input-group mb-3">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
                <input type="text" class="form-control" name="username" placeholder="Username" required>
            </div>
            
            <div class="input-group mb-3">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
            
            <button type="submit" class="btn btn-login">Login</button>
        </form>
        
        <div class="text-center mt-4">
            <a href="../index.php" class="text-decoration-none">
                <i class="fas fa-arrow-left"></i> Back to Main Site
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
