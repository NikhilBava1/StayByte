<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to submit feedback.']);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit();
}

include 'config/db.php';

// Handle feedback submission via AJAX
if (isset($_POST['submit_feedback'])) {
    $user_id = $_SESSION['user_id'];
    $feedback_message = trim($_POST['feedback_message']);
    
    // Validate input
    if (empty($feedback_message)) {
        echo json_encode(['status' => 'error', 'message' => 'Please enter your feedback message.']);
        exit();
    } else {
        // Insert feedback into database
        $sql = "INSERT INTO feedback (user_id, feedback_message) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "is", $user_id, $feedback_message);
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['status' => 'success', 'message' => 'Thank you for your feedback!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error submitting feedback. Please try again.']);
        }
        
        mysqli_stmt_close($stmt);
        exit();
    }
}

// If we get here, it's an invalid request
echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
exit();
?>
