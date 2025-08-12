<?php
// Ensure no output is sent before JSON response
ob_clean();

include 'config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    $room_id = isset($_POST['room_id']) ? intval($_POST['room_id']) : null;
    $category = isset($_POST['category']) ? trim($_POST['category']) : 'room';
    
    // Validate required fields
    if (empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Message is required']);
        exit;
    }
    
    // Set default values
    $status = 'open';
    
    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO enquiries (user_id, message, category, status, room_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        exit;
    }
    
    // Bind parameters
    $stmt->bind_param("isssi", $user_id, $message, $category, $status, $room_id);
    
    // Execute query
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Enquiry submitted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error submitting enquiry: ' . $stmt->error]);
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
