<?php
session_start();
include 'config/db.php';

// Check if user is logged in and is a provider
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header('Location: ../login.php');
    exit();
}
// Handle enquiry status update
if (isset($_POST['update_status'])) {
    $enquiry_id = intval($_POST['enquiry_id']);
    $status = $_POST['status'];
    
    // Update status and set responded_by
    $sql = "UPDATE enquiries SET status=?, responded_by=? WHERE enquiry_id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sii", $status, $_SESSION['user_id'], $enquiry_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $message = 'Enquiry status updated successfully.';
    } else {
        $error = 'Error updating enquiry status. Please try again.';
    }
    mysqli_stmt_close($stmt);
}

// Handle enquiry response
if (isset($_POST['respond_enquiry'])) {
    $enquiry_id = intval($_POST['enquiry_id']);
    $response = trim($_POST['response']);
    
    if (!empty($response)) {
        $sql = "UPDATE enquiries SET response=?, status='resolved', responded_by=? WHERE enquiry_id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sii", $response, $_SESSION['user_id'], $enquiry_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $message = 'Enquiry responded successfully.';
        } else {
            $error = 'Error responding to enquiry. Please try again.';
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = 'Please enter a response.';
    }
}

// Handle enquiry deletion
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql = "DELETE FROM enquiries WHERE enquiry_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $delete_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header('Location: enquiries.php?msg=deleted');
    exit();
}

// Fetch all enquiries with user details (only related to this provider's rooms or meals)
$provider_id = $_SESSION['user_id'];
$sql = "SELECT e.*, u.username, u.email,u.phone FROM enquiries e 
        LEFT JOIN rooms r ON e.room_id = r.room_id 
        LEFT JOIN meals m ON e.meal_id = m.meal_id 
        JOIN users u ON e.user_id = u.user_id 
        WHERE (r.room_provider_id = ? OR m.meal_provider_id = ?) 
        ORDER BY e.created_at DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $provider_id, $provider_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$enquiries = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'layout.php'; ?>
    <title>Enquiry Management - StayByte Admin</title>
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --danger-color: #f72585;
            --warning-color: #f8961e;
            --info-color: #4895ef;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --sidebar-width: 250px;
            --header-height: 70px;
            --transition: all 0.3s ease;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            transition: var(--transition);
        }
        .badge-open { background-color: #ffc107; color: #212529; }
        .badge-in-progress { background-color: #17a2b8; color: white; }
        .badge-resolved { background-color: #28a745; color: white; }
        .badge-closed { background-color: #6c757d; color: white; }
        .enquiry-message {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .response-text {
            background-color: #f8f9fa;
            border-left: 3px solid var(--primary-color);
            padding: 15px;
            margin-top: 10px;
            border-radius: 0 8px 8px 0;
        }
        .page-header {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e9ecef;
        }
        .page-header h1 {
            font-weight: 700;
            color: var(--dark-color);
        }
        .page-header p {
            font-size: 1.1rem;
            color: #6c757d;
        }
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border-radius: 10px;
            transition: var(--transition);
        }
        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .table th {
            font-weight: 600;
            color: var(--dark-color);
            border-top: none;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
        }
        .btn {
            border-radius: 6px;
            font-weight: 500;
            transition: var(--transition);
        }
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .modal-content {
            border-radius: 10px;
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .modal-header {
            border-bottom: 1px solid #e9ecef;
            background-color: #f8f9fa;
            border-radius: 10px 10px 0 0 !important;
        }
        .modal-footer {
            border-top: 1px solid #e9ecef;
            background-color: #f8f9fa;
            border-radius: 0 0 10px 10px !important;
        }
        .form-control, .form-select {
            border-radius: 6px;
            padding: 0.5rem 0.75rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>
        
        <main class="main-content">
            <div class="container-fluid">
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="page-header">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h1>Enquiry Management</h1>
                                    <p class="text-muted">Manage all enquiries in the system</p>
                                </div>
                                <div class="col-md-6 text-end">
                                    <!-- <a href="#" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i> Add New Enquiry
                                    </a> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Enquiry deleted successfully.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <?php if (isset($message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header bg-white">
                        <h5><i class="fas fa-inbox me-2"></i> Enquiry List</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Message</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($enquiries) > 0): ?>
                                        <?php foreach ($enquiries as $enquiry): ?>
                                            <tr>
                                                <td class="fw-bold">#<?php echo $enquiry['enquiry_id']; ?></td>
                                                <td><?php echo htmlspecialchars($enquiry['username']); ?></td>
                                                <td>
                                                    <div class="enquiry-message" title="<?php echo htmlspecialchars($enquiry['message']); ?>">
                                                        <?php echo htmlspecialchars($enquiry['message']); ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php echo ($enquiry['category'] == 'room') ? 'primary' : (($enquiry['category'] == 'meal') ? 'success' : 'secondary'); ?>">
                                                        <?php echo ucfirst($enquiry['category']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="status-badge badge-<?php echo str_replace('-', '', $enquiry['status']); ?>">
                                                        <?php echo ucfirst($enquiry['status']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('M j, Y', strtotime($enquiry['created_at'])); ?></td>
                                                <td class="text-end">
                                                    <!-- View Details Button -->
                                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#enquiryModal<?php echo $enquiry['enquiry_id']; ?>" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    
                                                    <!-- Delete Button -->
                                                    <a href="?delete_id=<?php echo $enquiry['enquiry_id']; ?>" class="btn btn-sm btn-outline-danger" 
                                                       onclick="return confirm('Are you sure you want to delete this enquiry? This action cannot be undone.')" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-5">
                                                <i class="fas fa-inbox fa-2x text-muted mb-3"></i>
                                                <p class="mb-0">No enquiries found.</p>
                                                <p class="text-muted">There are currently no enquiries in the system.</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Enquiry Detail Modals -->
    <?php foreach ($enquiries as $enquiry): ?>
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
                                <div class="col-4">
                                    <p><strong>User:</strong> <?php echo htmlspecialchars($enquiry['username']); ?></p>
                                </div>
                                <div class="col-4">
                                    <p><strong>User Email:</strong> <?php echo htmlspecialchars($enquiry['email']); ?></p>
                                </div>
                                <div class="col-4">
                                    <p><strong>User Contact Number:</strong> <?php echo htmlspecialchars($enquiry['phone']); ?></p>
                                </div>

                            </div>
                    <div class="row">
                        <div class="col-6">
                            <!--  -->
                            <p><strong>Category:</strong> 
                                <span class="badge bg-<?php echo ($enquiry['category'] == 'room') ? 'primary' : (($enquiry['category'] == 'meal') ? 'success' : 'secondary'); ?>">
                                    <?php echo ucfirst($enquiry['category']); ?>
                                </span>
                            </p>
                            <p><strong>Status:</strong> 
                                <span class="status-badge badge-<?php echo str_replace('-', '', $enquiry['status']); ?>">
                                    <?php echo ucfirst($enquiry['status']); ?>
                                </span>
                            </p>
                            <p><strong>Date:</strong> <?php echo date('M j, Y g:i A', strtotime($enquiry['created_at'])); ?></p>
                        </div>
                        <!-- if room enquiry then show room-enquiry div -->
                        <?php if ($enquiry['category'] == 'room' && !empty($enquiry['room_id'])): ?>
                        <div class="col-6 room-enquiry">
                            <!-- Display additional information for rooms -->
                            <?php if ($enquiry['category'] == 'room' && !empty($enquiry['room_id'])): ?>
                                <p><strong>Room ID:</strong> #<?php echo $enquiry['room_id']; ?></p>
                                <?php endif; ?>
                                <?php
                                $sql= "select * from rooms where room_id = $enquiry[room_id];";
                                $result = $conn->query($sql);
                                $row = $result->fetch_assoc();
                                ?>
                                    <p><strong><?php echo ucfirst($enquiry['category']); ?> Name:</strong> <?php echo htmlspecialchars($row['title']); ?></p>
                        </div>
                        <?php endif; ?>
                            <!-- if meal enquiry then show meal-enquiry div -->
                        <?php if ($enquiry['category'] == 'meal' && !empty($enquiry['meal_id'])): ?>
                        <div class="col-6 meal-enquiry">
                            <!-- Display additional information for meals -->
                            <?php if ($enquiry['category'] == 'meal' && !empty($enquiry['meal_id'])): ?>
                                <p><strong>Meal ID:</strong> #<?php echo $enquiry['meal_id']; ?></p>
                                <?php endif; ?>
                                <?php
                                $sql= "select * from meals where meal_id = $enquiry[meal_id];";
                                $result = $conn->query($sql);
                                $row = $result->fetch_assoc();
                                ?>
                                    <p><strong><?php echo ucfirst($enquiry['category']); ?> Name:</strong> <?php echo htmlspecialchars($row['meal_title']); ?></p>
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
                        <label class="form-label"><strong>Response:</strong></label>
                        <div class="response-text">
                            <?php echo nl2br(htmlspecialchars($enquiry['response'])); ?>
                        </div>
                        <small class="text-muted">Responded by Provider on <?php echo date('M j, Y g:i A', strtotime($enquiry['updated_at'])); ?></small>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($enquiry['status'] != 'resolved' && $enquiry['status'] != 'closed'): ?>
                    <form method="POST">
                        <input type="hidden" name="enquiry_id" value="<?php echo $enquiry['enquiry_id']; ?>">
                        <div class="mb-3">
                            <label for="response<?php echo $enquiry['enquiry_id']; ?>" class="form-label"><strong>Respond to Enquiry:</strong></label>
                            <textarea class="form-control" id="response<?php echo $enquiry['enquiry_id']; ?>" name="response" rows="3" 
                                      placeholder="Enter your response here..."></textarea>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div>
                                <button type="submit" name="respond_enquiry" class="btn btn-success">Send Response</button>
                            </div>
                            <div>
                                <label for="status" class="form-label">Status:</label>
                                <select name="status" class="form-select form-select-sm d-inline-block w-auto">
                                    <option value="open" <?php echo ($enquiry['status'] == 'open') ? 'selected' : ''; ?>>Open</option>
                                    <option value="in-progress" <?php echo ($enquiry['status'] == 'in-progress') ? 'selected' : ''; ?>>In Progress</option>
                                    <option value="resolved" <?php echo ($enquiry['status'] == 'resolved') ? 'selected' : ''; ?>>Resolved</option>
                                    <option value="closed" <?php echo ($enquiry['status'] == 'closed') ? 'selected' : ''; ?>>Closed</option>
                                </select>
                            </div>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
