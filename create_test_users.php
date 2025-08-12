<?php
include 'config/db.php';

// Create test users for different roles
$test_users = [
    [
        'username' => 'student1',
        'email' => 'student1@test.com',
        'password' => 'password123',
        'role' => 'student'
    ],
    [
        'username' => 'admin1',
        'email' => 'admin1@test.com',
        'password' => 'password123',
        'role' => 'admin'
    ],
    [
        'username' => 'staff1',
        'email' => 'staff1@test.com',
        'password' => 'password123',
        'role' => 'staff'
    ],
    [
        'username' => 'teacher1',
        'email' => 'teacher1@test.com',
        'password' => 'password123',
        'role' => 'teacher'
    ],
    [
        'username' => 'provider1',
        'email' => 'provider1@test.com',
        'password' => 'password123',
        'role' => 'provider'
    ]
];

// First, ensure user_roles table has the required roles
$roles = ['admin', 'student', 'staff', 'teacher', 'provider'];
foreach ($roles as $role) {
    $check_sql = "SELECT role_id FROM user_roles WHERE role_name = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "s", $role);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    
    if (mysqli_num_rows($check_result) == 0) {
        $insert_role_sql = "INSERT INTO user_roles (role_name, role_description) VALUES (?, ?)";
        $insert_role_stmt = mysqli_prepare($conn, $insert_role_sql);
        $description = ucfirst($role) . ' user';
        mysqli_stmt_bind_param($insert_role_stmt, "ss", $role, $description);
        mysqli_stmt_execute($insert_role_stmt);
        echo "Created role: $role<br>";
    }
}

// Create test users
foreach ($test_users as $user) {
    // Check if user already exists
    $check_sql = "SELECT user_id FROM users WHERE username = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "s", $user['username']);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    
    if (mysqli_num_rows($check_result) == 0) {
        // Get role_id
        $role_sql = "SELECT role_id FROM user_roles WHERE role_name = ?";
        $role_stmt = mysqli_prepare($conn, $role_sql);
        mysqli_stmt_bind_param($role_stmt, "s", $user['role']);
        mysqli_stmt_execute($role_stmt);
        $role_result = mysqli_stmt_get_result($role_stmt);
        $role_data = mysqli_fetch_assoc($role_result);
        
        if ($role_data) {
            $role_id = $role_data['role_id'];
            
            // Hash password
            $hashed_password = password_hash($user['password'], PASSWORD_DEFAULT);
            
            // Insert user
            $insert_sql = "INSERT INTO users (username, email, password, role_id) VALUES (?, ?, ?, ?)";
            $insert_stmt = mysqli_prepare($conn, $insert_sql);
            mysqli_stmt_bind_param($insert_stmt, "sssi", $user['username'], $user['email'], $hashed_password, $role_id);
            
            if (mysqli_stmt_execute($insert_stmt)) {
                $user_id = mysqli_insert_id($conn);
                echo "Created user: {$user['username']} (Role: {$user['role']})<br>";
                
                // If it's a student, also add to students table
                if ($user['role'] == 'student') {
                    $student_sql = "INSERT INTO students (user_id, first_name, last_name, student_id) VALUES (?, ?, ?, ?)";
                    $student_stmt = mysqli_prepare($conn, $student_sql);
                    $first_name = 'Test';
                    $last_name = 'Student';
                    $student_id = 'STU' . str_pad($user_id, 4, '0', STR_PAD_LEFT);
                    mysqli_stmt_bind_param($student_stmt, "isss", $user_id, $first_name, $last_name, $student_id);
                    mysqli_stmt_execute($student_stmt);
                    echo "Added student details for: {$user['username']}<br>";
                }
            } else {
                echo "Error creating user {$user['username']}: " . mysqli_error($conn) . "<br>";
            }
        }
    } else {
        echo "User {$user['username']} already exists<br>";
    }
}

echo "<br><strong>Test Users Created:</strong><br>";
echo "Username: student1, Password: password123, Role: Student<br>";
echo "Username: admin1, Password: password123, Role: Admin<br>";
echo "Username: staff1, Password: password123, Role: Staff<br>";
echo "Username: teacher1, Password: password123, Role: Teacher<br>";
echo "Username: provider1, Password: password123, Role: Provider<br>";

mysqli_close($conn);
?> 