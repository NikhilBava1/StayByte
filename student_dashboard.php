<?php
// Handle profile picture upload BEFORE including header to prevent headers already sent error
$upload_msg = '';
if (isset($_POST['upload_picture'])) {
    // Start session and check if user is logged in
    session_start();
    if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
        header('Location: index.php');
        exit();
    }
    
    include 'config/db.php';
    
    // Get student details
    $student_id = $_SESSION['user_id'];
    
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $target_dir = "uploads/profile_pics/";
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        // Validate file extension
        if (in_array($file_extension, $allowed_extensions)) {
            // Generate unique filename
            $new_filename = $student_id . '_' . time() . '.' . $file_extension;
            $target_file = $target_dir . $new_filename;
            
            // Move uploaded file
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_file)) {
                // Update database with new profile picture path
                $sql = "UPDATE users SET profile_pic = ? WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('si', $target_file, $student_id);
                
                if ($stmt->execute()) {
                    $upload_msg = "Profile picture updated successfully!";
                    // Refresh student data
                    $sql = "SELECT * FROM users WHERE user_id = ?";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, "i", $student_id);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $student = mysqli_fetch_assoc($result);
                } else {
                    $upload_msg = "Error updating profile picture: " . $conn->error;
                }
                $stmt->close();
            } else {
                $upload_msg = "Error uploading file.";
            }
        } else {
            $upload_msg = "Invalid file format. Only JPG, JPEG, PNG, and GIF files are allowed.";
        }
    } else {
        $upload_msg = "Please select a file to upload.";
    }
    // Redirect to prevent form resubmission
    header('Location: student_dashboard.php');
    exit();
}

// Now include the header and rest of the page
include 'includes/header.php';

// Handle enquiry deletion
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header('Location: index.php');
    exit();
}
include 'config/db.php';

// Get student details
$student_id = $_SESSION['user_id'];

// Handle form submission for updating info
$update_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['upload_picture'])) {
    $fields = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'username',
        'birth_date'
        // add more fields as needed
    ];

    $updates = [];
    $params = [];
    $types = '';

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            // You can add validation/sanitization here
            $updates[] = "$field = ?";
            $params[] = $_POST[$field];
            $types .= 's'; // assuming all fields are strings
        }
    }

    if (!empty($updates)) {
        $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE user_id = ?";
        $params[] = $student_id;
        $types .= 'i';

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            $update_msg = "Profile updated successfully!";
            // Refresh student data
            $sql = "SELECT * FROM users WHERE user_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $student_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $student = mysqli_fetch_assoc($result);
        } else {
            $update_msg = "Error updating profile: " . $conn->error;
        }
        $stmt->close();
    } else {
        $update_msg = "No changes to update.";
    }
}

// Fetch latest student info if not already fetched
if (!isset($student)) {
    $sql = "SELECT * FROM users WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $student = mysqli_fetch_assoc($result);
}

// Get current page numbers for pagination
$inquiries_page = isset($_GET['inquiries_page']) ? (int)$_GET['inquiries_page'] : 1;
$feedback_page = isset($_GET['feedback_page']) ? (int)$_GET['feedback_page'] : 1;

// Number of records per page
$records_per_page = 4;

// Fetch recent inquiries with pagination
$inquiries_offset = ($inquiries_page - 1) * $records_per_page;
$inquiries_count_sql = "SELECT COUNT(*) as count FROM enquiries WHERE user_id = ?";
$inquiries_count_stmt = mysqli_prepare($conn, $inquiries_count_sql);
mysqli_stmt_bind_param($inquiries_count_stmt, "i", $student_id);
mysqli_stmt_execute($inquiries_count_stmt);
$inquiries_count_result = mysqli_stmt_get_result($inquiries_count_stmt);
$inquiries_count_row = mysqli_fetch_assoc($inquiries_count_result);
$total_inquiries = $inquiries_count_row['count'];
$total_inquiries_pages = ceil($total_inquiries / $records_per_page);

$inquiries_sql = "SELECT * FROM enquiries WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
$inquiries_stmt = mysqli_prepare($conn, $inquiries_sql);
mysqli_stmt_bind_param($inquiries_stmt, "iii", $student_id, $records_per_page, $inquiries_offset);
mysqli_stmt_execute($inquiries_stmt);
$inquiries_result = mysqli_stmt_get_result($inquiries_stmt);
$recent_inquiries = [];
while ($row = mysqli_fetch_assoc($inquiries_result)) {
    $recent_inquiries[] = $row;
}

// Fetch recent feedback with pagination
$feedback_offset = ($feedback_page - 1) * $records_per_page;
$feedback_count_sql = "SELECT COUNT(*) as count FROM feedback WHERE user_id = ?";
$feedback_count_stmt = mysqli_prepare($conn, $feedback_count_sql);
mysqli_stmt_bind_param($feedback_count_stmt, "i", $student_id);
mysqli_stmt_execute($feedback_count_stmt);
$feedback_count_result = mysqli_stmt_get_result($feedback_count_stmt);
$feedback_count_row = mysqli_fetch_assoc($feedback_count_result);
$total_feedback = $feedback_count_row['count'];
$total_feedback_pages = ceil($total_feedback / $records_per_page);

$feedback_sql = "SELECT * FROM feedback WHERE user_id = ? ";
$feedback_stmt = mysqli_prepare($conn, $feedback_sql);
mysqli_stmt_bind_param($feedback_stmt, "i", $student_id);
mysqli_stmt_execute($feedback_stmt);
$feedback_result = mysqli_stmt_get_result($feedback_stmt);
$recent_feedback = [];
while ($row = mysqli_fetch_assoc($feedback_result)) {
    $recent_feedback[] = $row;
}
?>


    <!-- Main Content -->
    <div class="container mt-4">
        <?php if (isset($delete_message)): ?>
            <div class="alert alert-<?php echo strpos($delete_message, 'Error') !== false ? 'danger' : 'success'; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($delete_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <div class="row">
            <!-- Profile Section -->
            <div class="col-lg-4 mb-4">
                <div class="card profile-card border-0 shadow-sm">
                    <div class="card-header profile-header text-center">
                        <div class="profile-avatar mx-auto position-relative">
                            <?php if (!empty($student['profile_pic']) && file_exists($student['profile_pic'])): ?>
                                <img src="<?php echo htmlspecialchars($student['profile_pic']); ?>" alt="Profile Picture" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover; border: none; box-shadow: none;">
                            <?php else: ?>
                                <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white" style="width: 100px; height: 100px; font-size: 2.5rem; margin: 0 auto; border: none; box-shadow: none;">
                                    <?php echo substr($student['first_name'], 0, 1) . substr($student['last_name'], 0, 1); ?>
                                </div>
                            <?php endif; ?>
                            <!-- Upload Button -->
                            <button type="button" class="btn btn-sm btn-primary rounded-circle position-absolute d-flex align-items-center justify-content-center" style="bottom: 0; right: 0; width: 30px; height: 30px; padding: 0;" onclick="toggleUploadForm()">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        
                        <!-- Profile Picture Upload Form (Hidden by default) -->
                        <div id="uploadForm" class="mt-3" style="display: none;">
                            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data" class="d-inline">
                                <div class="input-group">
                                    <input type="file" class="form-control form-control-sm" id="profile_pic" name="profile_pic" accept="image/*" required>
                                    <button type="submit" class="btn btn-primary btn-sm" name="upload_picture">Upload</button>
                                </div>
                                <small class="form-text text-white-50">JPG, JPEG, PNG, GIF only</small>
                                <?php if ($upload_msg): ?>
                                    <div class="alert alert-info mt-2 p-2 small"><?php echo $upload_msg; ?></div>
                                <?php endif; ?>
                            </form>
                        </div>
                        
                        <h3 class="mb-0 text-white"><?php echo htmlspecialchars($student['username']); ?></h3>
                        <p class="mb-0 text-white-50">Student ID: <?php echo str_pad($student['user_id'], 6, '0', STR_PAD_LEFT); ?></p>
                        <div class="mt-3">
                            <a href="logout.php" class="btn btn-danger btn-sm">
                                <i class="fas fa-sign-out-alt me-1"></i>Logout
                            </a>
                        </div>
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title">Profile Information</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="fw-medium">Name:</span>
                                <span><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="fw-medium">Email:</span>
                                <span><?php echo htmlspecialchars($student['email']); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="fw-medium">Phone:</span>
                                <span><?php echo htmlspecialchars($student['phone']); ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Main Content Area -->
            <div class="col-lg-8">
                <div class="card profile-card border-0 shadow-sm mb-4">
                    <div class="card-header profile-header">
                        <h3 class="mb-0 text-white">Edit Profile</h3>
                    </div>

            <div class="card-body p-4">
                <?php if ($update_msg): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?php echo $update_msg; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="needs-validation" novalidate>
                    <!-- Basic Information -->
                    <h4 class="section-title mb-4">Basic Information</h4>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($student['first_name']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($student['last_name']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($student['phone']); ?>">
                        </div>
                    </div>

                    <!-- Additional Information Toggle -->
                    <div class="accordion mb-4" id="additionalInfoAccordion">
                        <div class="accordion-item border-0">
                            <h2 class="accordion-header" id="additionalInfoHeading">
                                <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#additionalInfo" aria-expanded="false" aria-controls="additionalInfo">
                                    <i class="fas fa-user me-2"></i>Additional Information
                                </button>
                            </h2>
                            <div id="additionalInfo" class="accordion-collapse collapse" aria-labelledby="additionalInfoHeading" data-bs-parent="#additionalInfoAccordion">
                                <div class="accordion-body">
                                    <!-- Personal Information -->
                                    <h5 class="section-title mb-3">Personal Information</h5>
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-6">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($student['username'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="birth_date" class="form-label">Date of Birth</label>
                                            <input type="date" class="form-control" id="birth_date" name="birth_date" value="<?php echo htmlspecialchars($student['birth_date'] ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-save">
                            <i class="fas fa-save me-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Recent Inquiries Section -->
<div class="container-fluid mt-4">
    <div class="card profile-card border-0 shadow-sm mb-4">
        <div class="card-header profile-header">
            <h3 class="mb-0 text-white">Recent Inquiries</h3>
        </div>
        <div class="card-body p-4">
            <?php if (empty($recent_inquiries)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-2x text-muted mb-3"></i>
                    <p class="text-muted mb-0">No inquiries found.</p>
                    <p class="text-muted small">You haven't submitted any inquiries yet.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Message</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_inquiries as $inquiry): ?>
                            <tr>
                                <td><?php 
                                    $message = htmlspecialchars($inquiry['message']);
                                    echo strlen($message) > 50 ? substr($message, 0, 50) . '...' : $message;
                                ?></td>
                                <td><?php echo ucfirst(htmlspecialchars($inquiry['category'])); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo $inquiry['status'] == 'open' ? 'warning' : 
                                             ($inquiry['status'] == 'in-progress' ? 'primary' : 
                                             ($inquiry['status'] == 'resolved' ? 'success' : 'secondary')); 
                                    ?>">
                                        <?php echo ucfirst(htmlspecialchars($inquiry['status'])); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($inquiry['created_at'])); ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-primary view-enquiry-btn" 
                                        data-enquiry-id="<?php echo $inquiry['enquiry_id']; ?>"
                                        data-category="<?php echo htmlspecialchars($inquiry['category']); ?>"
                                        data-status="<?php echo htmlspecialchars($inquiry['status']); ?>"
                                        data-date="<?php echo date('M j, Y g:i A', strtotime($inquiry['created_at'])); ?>"
                                        data-message="<?php echo htmlspecialchars($inquiry['message']); ?>"
                                        data-response="<?php echo htmlspecialchars($inquiry['response'] ?? ''); ?>"
                                        data-response-date="<?php echo !empty($inquiry['response']) ? date('M j, Y g:i A', strtotime($inquiry['updated_at'])) : ''; ?>"
                                        data-bs-toggle="modal" data-bs-target="#enquiryModal<?php echo $inquiry['enquiry_id']; ?>">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <?php if ($inquiry['status'] == 'open'): ?>
                                    <button class="btn btn-sm btn-outline-danger delete-enquiry-btn" data-enquiry-id="<?php echo $inquiry['enquiry_id']; ?>">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <!-- Pagination for Recent Inquiries -->
                    <?php if ($total_inquiries_pages > 1): ?>
                    <nav aria-label="Inquiries pagination">
                        <ul class="pagination justify-content-center">
                            <?php if ($inquiries_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?inquiries_page=<?php echo $inquiries_page - 1; ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            <?php endif; ?>
                            <?php for ($i = 1; $i <= $total_inquiries_pages; $i++): ?>
                            <li class="page-item <?php echo $i == $inquiries_page ? 'active' : ''; ?>">
                                <a class="page-link" href="?inquiries_page=<?php echo $i; ?>"> <?php echo $i; ?> </a>
                            </li>
                            <?php endfor; ?>
                            <?php if ($inquiries_page < $total_inquiries_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?inquiries_page=<?php echo $inquiries_page + 1; ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    </div>
    
    <!-- Recent Feedback Section -->
    <div class="card profile-card border-0 shadow-sm">
        <div class="card-header profile-header">
            <h3 class="mb-0 text-white">Recent Feedback</h3>
        </div>
        <div class="card-body p-4">
            <?php if (empty($recent_feedback)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-comment-dots fa-2x text-muted mb-3"></i>
                    <p class="text-muted mb-0">No feedback found.</p>
                    <p class="text-muted small">You haven't submitted any feedback yet.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Message</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach ($recent_feedback as $feedback): ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td class="text-truncate" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?php echo ucfirst(htmlspecialchars($feedback['feedback_message'])); ?>
                                </td>
                            </tr>
                            <?php $i++; endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Enquiry Detail Modals -->
<?php foreach ($recent_inquiries as $enquiry): 
    // Fetch room or meal details if applicable
    $item_details = null;
    if ($enquiry['category'] == 'room' && !empty($enquiry['room_id'])) {
        $stmt = $conn->prepare("SELECT title FROM rooms WHERE room_id = ?");
        $stmt->bind_param("i", $enquiry['room_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $item_details = $result->fetch_assoc();
        $stmt->close();
    } elseif ($enquiry['category'] == 'meal' && !empty($enquiry['meal_id'])) {
        $stmt = $conn->prepare("SELECT meal_title FROM meals WHERE meal_id = ?");
        $stmt->bind_param("i", $enquiry['meal_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $item_details = $result->fetch_assoc();
        $stmt->close();
    }
    ?>
    <div class="modal fade" id="enquiryModal<?php echo $enquiry['enquiry_id']; ?>" tabindex="-1" aria-labelledby="enquiryModalLabel<?php echo $enquiry['enquiry_id']; ?>" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="enquiryModalLabel<?php echo $enquiry['enquiry_id']; ?>">
                        Enquiry #<?php echo $enquiry['enquiry_id']; ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Category:</strong> 
                                <span class="badge bg-<?php echo ($enquiry['category'] == 'room') ? 'primary' : (($enquiry['category'] == 'meal') ? 'success' : 'secondary'); ?>">
                                    <?php echo ucfirst(htmlspecialchars($enquiry['category'])); ?>
                                </span>
                            </p>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-<?php 
                                    echo $enquiry['status'] == 'open' ? 'warning' : 
                                         ($enquiry['status'] == 'in-progress' ? 'primary' : 
                                         ($enquiry['status'] == 'resolved' ? 'success' : 'secondary')); 
                                ?>">
                                    <?php echo ucfirst(htmlspecialchars($enquiry['status'])); ?>
                                </span>
                            </p>
                            <p><strong>Date:</strong> <?php echo date('M j, Y g:i A', strtotime($enquiry['created_at'])); ?></p>
                        </div>
                        
                        <?php if (($enquiry['category'] == 'room' && !empty($enquiry['room_id'])) || ($enquiry['category'] == 'meal' && !empty($enquiry['meal_id']))): ?>
                        <div class="col-md-6">
                            <p><strong><?php echo ucfirst(htmlspecialchars($enquiry['category'])); ?> ID:</strong> 
                                #<?php echo $enquiry['category'] == 'room' ? $enquiry['room_id'] : $enquiry['meal_id']; ?>
                            </p>
                            <?php if ($item_details): ?>
                                <p><strong><?php echo ucfirst(htmlspecialchars($enquiry['category'])); ?> Name:</strong> 
                                    <?php echo htmlspecialchars($item_details['title'] ?? $item_details['meal_title'] ?? 'N/A'); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><strong>Message:</strong></label>
                        <div class="p-3 bg-light border rounded">
                            <?php echo nl2br(htmlspecialchars($enquiry['message'])); ?>
                        </div>
                    </div>
                    
                    <?php if (!empty($enquiry['response'])): ?>
                    <div class="mb-3">
                        <label class="form-label"><strong>Provider Response:</strong></label>
                        <div class="response-text p-3 bg-success bg-opacity-10 border border-success rounded">
                            <?php echo nl2br(htmlspecialchars($enquiry['response'])); ?>
                        </div>
                        <small class="text-muted">Responded on <?php echo date('M j, Y g:i A', strtotime($enquiry['updated_at'])); ?></small>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
<!-- Feedback Detail Modals - showing only feedback message and ID -->
<?php foreach ($recent_feedback as $feedback): ?>
    <div class="modal fade" id="feedbackModal<?php echo $feedback['feedback_id']; ?>" tabindex="-1" aria-labelledby="feedbackModalLabel<?php echo $feedback['feedback_id']; ?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="feedbackModalLabel<?php echo $feedback['feedback_id']; ?>">
                        Feedback #<?php echo $feedback['feedback_id']; ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label"><strong>Message:</strong></label>
                        <div class="p-3 bg-light border rounded">
                            <?php echo nl2br(htmlspecialchars($feedback['message'])); ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

    

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle profile picture upload form
        function toggleUploadForm() {
            const uploadForm = document.getElementById('uploadForm');
            if (uploadForm.style.display === 'none') {
                uploadForm.style.display = 'block';
            } else {
                uploadForm.style.display = 'none';
            }
        }
        
        // Bootstrap validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()

        // Toggle additional info text
        document.querySelector('.info-toggle').addEventListener('click', function() {
            const icon = this.querySelector('i');
            if (icon.classList.contains('fa-plus-circle')) {
                icon.classList.remove('fa-plus-circle');
                icon.classList.add('fa-minus-circle');
                this.innerHTML = '<i class="fas fa-minus-circle me-2"></i>Hide Additional Information';
            } else {
                icon.classList.remove('fa-minus-circle');
                icon.classList.add('fa-plus-circle');
                this.innerHTML = '<i class="fas fa-plus-circle me-2"></i>Add More Information';
            }
        });
        
        // Handle enquiry deletion with AJAX
        document.querySelectorAll('.delete-enquiry-btn').forEach(button => {
            button.addEventListener('click', function() {
                const enquiryId = this.getAttribute('data-enquiry-id');
                const row = this.closest('tr');
                
                // Send AJAX request
                fetch('delete_enquiry.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'enquiry_id=' + encodeURIComponent(enquiryId)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the row from the table
                        row.remove();
                        // Show success message
                        alert(data.message);
                    } else {
                        // Show error message
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the enquiry.');
                });
            });
        });
    </script>
    
    <script>
        $(document).ready(function() {
            // Handle view enquiry button click
            $('.view-enquiry-btn').click(function() {
                var enquiryId = $(this).data('enquiry-id');
                var category = $(this).data('category');
                var status = $(this).data('status');
                var date = $(this).data('date');
                var message = $(this).data('message');
                var response = $(this).data('response');
                var responseDate = $(this).data('response-date');
                var itemDetails = $(this).data('item-details');
                
                // Update modal content
                $('#enquiryModalLabel' + enquiryId).text('Enquiry #' + enquiryId);
                $('#enquiryCategory' + enquiryId).text(category.charAt(0).toUpperCase() + category.slice(1));
                $('#enquiryStatus' + enquiryId).text(status.charAt(0).toUpperCase() + status.slice(1));
                $('#enquiryDate' + enquiryId).text(date);
                $('#enquiryMessage' + enquiryId).html(message.replace(/\n/g, '<br>'));
                
                if (response) {
                    $('#enquiryResponse' + enquiryId).html(response.replace(/\n/g, '<br>'));
                    $('#enquiryResponseDate' + enquiryId).text(responseDate);
                    $('#responseSection' + enquiryId).show();
                } else {
                    $('#responseSection' + enquiryId).hide();
                }
                
                if (itemDetails) {
                    $('#itemDetails' + enquiryId).text(itemDetails);
                    $('#itemDetailsSection' + enquiryId).show();
                } else {
                    $('#itemDetailsSection' + enquiryId).hide();
                }
            });
            
            // Handle enquiry deletion with AJAX
            $('.delete-enquiry-btn').click(function() {
                var enquiryId = $(this).data('enquiry-id');
                var row = $(this).closest('tr');
                
                // Show confirmation dialog
                if (!confirm('Are you sure you want to delete this enquiry?')) {
                    return;
                }
                
                // Send AJAX request
                $.ajax({
                    url: 'delete_enquiry.php',
                    method: 'POST',
                    data: { enquiry_id: enquiryId },
                    dataType: 'json',
                    success: function(data) {
                        if (data.success) {
                            // Remove the row from the table
                            row.remove();
                            // Show success message
                            alert(data.message);
                        } else {
                            // Show error message
                            alert(data.message);
                        }
                    },
                    error: function() {
                        // Show error message
                        alert('An error occurred while deleting the enquiry.');
                    }
                });
            });
                
                // Show the modal
                $('#feedbackDetailModal').modal('show');
                
                // Debug: Log when modal is shown
                console.log('Modal shown with data:', feedbackData);
            });
        });
    </script>
    
    <?php
    include 'includes/footer.php';
    ?>
</body>
</html>