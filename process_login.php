<?php
session_start();
include 'config/db.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if ($data && isset($data['username']) && isset($data['password']) && isset($data['role'])) {
        $username = trim($data['username']);
        $password = $data['password'];
        $role_id = $data['role'];
        
        // Validate input
        if (empty($username) || empty($password) || empty($role_id)) {
            echo json_encode(['success' => false, 'error' => 'Please fill in all fields']);
            exit();
        }
        
        // Check user credentials with role_id
        $sql = "SELECT u.user_id, u.username, u.email, u.password, u.role_id, r.role_name 
                FROM users u 
                JOIN user_roles r ON u.role_id = r.role_id 
                WHERE u.username = ? AND u.password = ? AND u.role_id = ?";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssi", $username, $password, $role_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($user = mysqli_fetch_assoc($result)) {
            // Login successful - set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role_id'] = $user['role_id'];
            $_SESSION['role_name'] = $user['role_name'];
            
            // Redirect based on role
            $redirect_url = '';
            switch ($user['role_name']) {
                case 'admin':
                    $redirect_url = 'admin_dashboard.php';
                    break;
                case 'student':
                    $redirect_url = 'student_dashboard.php';
                    break;
                case 'staff':
                    $redirect_url = 'staff_dashboard.php';
                    break;
                case 'teacher':
                    $redirect_url = 'teacher_dashboard.php';
                    break;
                case 'provider':
                    $redirect_url = 'provider_dashboard.php';
                    break;
                default:
                    $redirect_url = 'index.php';
            }
            
            echo json_encode([
                'success' => true, 
                'message' => 'Login successful',
                'redirect' => $redirect_url,
                'user' => [
                    'username' => $user['username'],
                    'role' => $user['role_name']
                ]
            ]);
        } else {
            // Login failed
            echo json_encode(['success' => false, 'error' => 'Invalid username, password or role']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid data received']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}

mysqli_close($conn);
?>
        



