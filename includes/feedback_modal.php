<?php
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    return; // Don't show modal if user is not logged in
}
?>

<!-- Feedback Popup Modal -->
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-desktop-right">
        <div class="modal-content" style="background-color: #ffffff; border: 1px solid #e0e0e0;">
            <div class="modal-header" style="background-color: #0a0a0a; color: #ffffff; border-bottom: 1px solid #313131;">
                <h5 class="modal-title text-white" id="feedbackModalLabel">Quick Feedback</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="background-color: #ffffff;">
                <form id="feedbackForm">
                    <div class="mb-3">
                        <label for="feedback_message" class="form-label" style="color: #313131;"><strong>Your Feedback *</strong></label>
                        <textarea class="form-control" id="feedback_message" name="feedback_message" rows="4" placeholder="Share your feedback with us..." required style="background-color: #ffffff; border: 1px solid #94959b; color: #0a0a0a;"></textarea>
                    </div>
                </form>
                <div id="feedbackMessage" class="mt-3"></div>
            </div>
            <div class="modal-footer" style="background-color: #f8f9fa; border-top: 1px solid #e0e0e0;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background-color: #94959b; border: 1px solid #94959b; color: #ffffff;">Cancel</button>
                <button type="button" class="btn btn-primary" id="submitFeedbackBtn" style="background-color: #0a0a0a; border: 1px solid #0a0a0a; color: #ffffff;">Submit Feedback</button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('submitFeedbackBtn').addEventListener('click', function() {
    var feedbackMessage = document.getElementById('feedback_message').value;
    
    if (!feedbackMessage.trim()) {
        document.getElementById('feedbackMessage').innerHTML = '<div class="alert alert-danger mb-0">Please enter your feedback.</div>';
        return;
    }
    
    var formData = new FormData();
    formData.append('submit_feedback', '1');
    formData.append('feedback_message', feedbackMessage);
    
    fetch('submit_feedback.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        var messageDiv = document.getElementById('feedbackMessage');
        if (data.status === 'success') {
            messageDiv.innerHTML = '<div class="alert alert-success mb-0">' + data.message + '</div>';
            document.getElementById('feedbackForm').reset();
            // Auto-close the modal after 2 seconds
            setTimeout(function() {
                var feedbackModal = bootstrap.Modal.getInstance(document.getElementById('feedbackModal'));
                if (feedbackModal) {
                    feedbackModal.hide();
                }
            }, 2000);
        } else {
            messageDiv.innerHTML = '<div class="alert alert-danger mb-0">' + data.message + '</div>';
        }
    })
    .catch(error => {
        document.getElementById('feedbackMessage').innerHTML = '<div class="alert alert-danger mb-0">Error submitting feedback. Please try again.</div>';
    });
});
</script>
