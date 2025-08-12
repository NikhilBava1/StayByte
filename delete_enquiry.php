<?php
session_start();
include 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Check if request method is POST and enquiry_id is provided
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['enquiry_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$enquiry_id = intval($_POST['enquiry_id']);
$student_id = $_SESSION['user_id'];

// Verify that the enquiry belongs to this student before deleting
$check_sql = "SELECT * FROM enquiries WHERE enquiry_id = ? AND user_id = ?";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, "ii", $enquiry_id, $student_id);
mysqli_stmt_execute($check_stmt);
$check_result = mysqli_stmt_get_result($check_stmt);

if (mysqli_num_rows($check_result) > 0) {
    // Enquiry belongs to this student, proceed with deletion
    $sql = "DELETE FROM enquiries WHERE enquiry_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $enquiry_id);
    $delete_result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    if ($delete_result) {
        echo json_encode(['success' => true, 'message' => 'Enquiry deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting enquiry']);
    }
} else {
    // Enquiry doesn't belong to this student or doesn't exist
    echo json_encode(['success' => false, 'message' => 'Enquiry not found or you do not have permission to delete it']);
}

mysqli_stmt_close($check_stmt);
mysqli_close($conn);
?>
