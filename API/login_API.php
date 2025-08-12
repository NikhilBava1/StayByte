<?php
session_start();
include '../config/db.php'; // ✅ your DB connection file

// Get data
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Sanitize inputs
$username = trim($username);
$password = trim($password);

// Check if user exists
$sql = "SELECT user_id, username, email, password, role_id FROM users WHERE username = ? OR email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // ✅ Assuming password is hashed with password_hash()
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role_id'] = $user['role_id'];

        // Set flag to show feedback modal after login
        $_SESSION['show_feedback_modal'] = true;
        $_SESSION['just_logged_in'] = true;
        
        // ✅ Redirect based on role_id
        switch ($user['role_id']) {
            case 1: // admin
                header("Location: ../admin_dashboard.php");
                break;
            case 2: // student
                header("Location: ../student_dashboard.php");
                break;
            case 3: // provider
                header("Location: ../provider/index.php");
                break;
            default:
                header("Location: ../unknown_role.php");
        }
        exit();
    } else {
        echo "<script>alert('Invalid password.'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('User not found.'); window.history.back();</script>";
}
?>
