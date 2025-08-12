<?php
header('Content-Type: application/json');
include '../config/db.php';

try {
    // Fetch all roles from user_roles table
    $sql = "SELECT role_id, role_name FROM user_roles ORDER BY role_name";
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        throw new Exception("Database error: " . mysqli_error($conn));
    }
    
    $roles = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $roles[] = [
            'role_id' => $row['role_id'],
            'role_name' => ucfirst($row['role_name'])
        ];
    }
    
    echo json_encode($roles);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

mysqli_close($conn);
?>