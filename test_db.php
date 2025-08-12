<?php
include 'config/db.php';

echo "<h2>Database Test</h2>";

// Test user_roles table
echo "<h3>User Roles:</h3>";
$sql = "SELECT * FROM user_roles";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "Role ID: " . $row['role_id'] . ", Role Name: " . $row['role_name'] . "<br>";
    }
} else {
    echo "Error: " . mysqli_error($conn) . "<br>";
}

echo "<h3>Users:</h3>";
$sql = "SELECT u.user_id, u.username, u.email, u.role_id, r.role_name 
        FROM users u 
        JOIN user_roles r ON u.role_id = r.role_id";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "User ID: " . $row['user_id'] . ", Username: " . $row['username'] . ", Role: " . $row['role_name'] . "<br>";
    }
} else {
    echo "Error: " . mysqli_error($conn) . "<br>";
}

mysqli_close($conn);
?> 