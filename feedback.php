<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

include 'config/db.php';
include 'includes/header.php';

// Initialize variables
$success_message = '';
$error_message = '';

// Handle feedback submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_feedback'])) {
    $user_id = $_SESSION['user_id'];
    $feedback_message = trim($_POST['feedback_message']);
    
    // Validate input
    if (empty($feedback_message)) {
        $error_message = 'Please enter your feedback message.';
    } else {
        // Insert feedback into database
        $sql = "INSERT INTO feedback (user_id, feedback_message) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "is", $user_id, $feedback_message);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = 'Thank you for your feedback!';
            // Clear the form
            $feedback_message = '';
        } else {
            $error_message = 'Error submitting feedback. Please try again.';
        }
        
        mysqli_stmt_close($stmt);
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4><i class="fas fa-comment-dots me-2"></i>Feedback</h4>
                </div>
                <div class="card-body">
                    <p>We'd love to hear your thoughts! Please share your experience with us.</p>
                    
                    <?php if ($success_message): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($success_message); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="feedback.php">
                        <div class="mb-3">
                            <label for="feedback_message" class="form-label"><strong>Your Feedback *</strong></label>
                            <textarea class="form-control" id="feedback_message" name="feedback_message" rows="6" placeholder="Share your feedback with us..." required><?php echo isset($feedback_message) ? htmlspecialchars($feedback_message) : ''; ?></textarea>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="index.php" class="btn btn-secondary me-md-2">Back to Home</a>
                            <button type="submit" name="submit_feedback" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i>Submit Feedback
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>