<?php
session_start();
include 'config/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Initialize variables
$room_id = 0;
$title = '';
$description = '';
$room_image = '';
$price = '';
$rating = '';
$bed_size = '';
$guest_capacity = '';
$room_space = '';
$status = 'Active';
$room_images = array(); // For multiple images
$room_amenities = array(); // For room amenities

$editing = false;
// Check if editing existing room
if (isset($_GET['id'])) {
    $room_id = intval($_GET['id']);
    $editing = true;
    
    // Fetch room details
    $sql = "SELECT * FROM rooms WHERE room_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $room_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($room = mysqli_fetch_assoc($result)) {
        $title = $room['title'];
        $description = $room['description'];
        $room_image = $room['room_image'];
        $price = $room['price'];
        $rating = $room['rating'];
        $bed_size = $room['bed_size'];
        $guest_capacity = $room['guest_capacity'];      
        $room_space = $room['room_space'];  
        $status = $room['status'];
        
        // Fetch additional room images
        $images_sql = "SELECT image_url FROM rooms_images WHERE room_id = ? ORDER BY image_id ASC";
        $images_stmt = mysqli_prepare($conn, $images_sql);
        mysqli_stmt_bind_param($images_stmt, "i", $room_id);
        mysqli_stmt_execute($images_stmt);
        $images_result = mysqli_stmt_get_result($images_stmt);
        while ($image = mysqli_fetch_assoc($images_result)) {
            $room_images[] = $image['image_url'];       
        }
        mysqli_stmt_close($images_stmt);
        
        // Fetch room amenities
        $amenities_sql = "SELECT facility_name FROM room_amenities WHERE room_id = ? ORDER BY amenities_id ASC";
        $amenities_stmt = mysqli_prepare($conn, $amenities_sql);
        mysqli_stmt_bind_param($amenities_stmt, "i", $room_id); 
        mysqli_stmt_execute($amenities_stmt);
        $amenities_result = mysqli_stmt_get_result($amenities_stmt);
        while ($amenity = mysqli_fetch_assoc($amenities_result)) {
            $room_amenities[] = $amenity['facility_name'];
        }   
        mysqli_stmt_close($amenities_stmt);
    } else {
        // Room not found
        header('Location: rooms.php?msg=not_found');
        exit();
    }
    mysqli_stmt_close($stmt);   
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $rating = floatval($_POST['rating']);
    $bed_size = trim($_POST['bed_size']);
    $guest_capacity = intval($_POST['guest_capacity']);
    $room_space = intval($_POST['room_space']);
    $status = $_POST['status'];     
    $amenities = isset($_POST['amenities']) ? explode(',', $_POST['amenities']) : array();
    
    // Handle primary image upload
    if (isset($_FILES['room_image']) && $_FILES['room_image']['error'] == 0) {
        $target_dir = '../uploads/rooms/';
        
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $ext = pathinfo($_FILES['room_image']['name'], PATHINFO_EXTENSION);
        $filename = 'room_' . time() . '_' . rand(1000,9999) . '.' . $ext;
        $target_file = $target_dir . $filename;
        
        if (move_uploaded_file($_FILES['room_image']['tmp_name'], $target_file)) {
            $room_image = 'uploads/rooms/' . $filename;
        }
    }
    
    // Handle additional images upload
    $additional_images = array();
    if (isset($_FILES['additional_images'])) {
        $files = $_FILES['additional_images'];
        $file_count = count($files['name']);
        
        for ($i = 0; $i < $file_count; $i++) {
            if ($files['error'][$i] == 0) {
                $target_dir = '../uploads/rooms/';
                
                // Create directory if it doesn't exist
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                
                $ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
                $filename = 'room_' . time() . '_' . rand(1000,9999) . '_' . $i . '.' . $ext;
                $target_file = $target_dir . $filename;
                
                if (move_uploaded_file($files['tmp_name'][$i], $target_file)) {
                    $additional_images[] = 'uploads/rooms/' . $filename;
                }
            }
        }
    }
    
    // Handle deleted images
    $deleted_images = isset($_POST['deleted_images']) ? explode(',', $_POST['deleted_images']) : array();
    $existing_images = isset($_POST['existing_images']) ? $_POST['existing_images'] : array();
    
    if ($editing) {
        // Update existing room
        if (!empty($room_image) && !empty($room_space)) {
            $sql = "UPDATE rooms SET title=?, description=?, room_image=?, price=?, rating=?, bed_size=?, guest_capacity=?, room_space=?, status=? WHERE room_id=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssdsisisi", $title, $description, $room_image, $price, $rating, $bed_size, $guest_capacity, $room_space, $status, $room_id);
        } else {
            $sql = "UPDATE rooms SET title=?, description=?, price=?, rating=?, bed_size=?, guest_capacity=?, room_space=?, status=? WHERE room_id=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssdssisis", $title, $description, $price, $rating, $bed_size, $guest_capacity, $room_space, $status, $room_id);
        }

        if (mysqli_stmt_execute($stmt)) {
            // Handle additional images 
            // First, handle deleted images
            if (!empty($deleted_images) && !empty($existing_images)) {
                foreach ($deleted_images as $index) {
                    $index = intval($index);
                    if (isset($existing_images[$index])) {
                        // Delete the image file from the server
                        $image_path = '../' . $existing_images[$index];
                        if (file_exists($image_path)) {
                            unlink($image_path);
                        }
                    }
                }
            }
            
            // Then delete existing additional images for this room (except the ones that were deleted)
            if (!empty($existing_images)) {
                // Filter out deleted images    
                $images_to_keep = array();
                foreach ($existing_images as $index => $image_url) {
                    if (!in_array($index, $deleted_images)) {
                        $images_to_keep[] = $image_url;
                    }
                }
                
                // Delete all existing images for this room
                $delete_sql = "DELETE FROM rooms_images WHERE room_id=?";
                $delete_stmt = mysqli_prepare($conn, $delete_sql);
                mysqli_stmt_bind_param($delete_stmt, "i", $room_id);
                mysqli_stmt_execute($delete_stmt);
                mysqli_stmt_close($delete_stmt);
                
                // Insert the images that should be kept
                if (!empty($images_to_keep)) {
                    $insert_sql = "INSERT INTO rooms_images (room_id, image_url) VALUES (?, ?)";
                    $insert_stmt = mysqli_prepare($conn, $insert_sql);
                    foreach ($images_to_keep as $image_url) {
                        mysqli_stmt_bind_param($insert_stmt, "is", $room_id, $image_url);
                        mysqli_stmt_execute($insert_stmt);
                    }
                    mysqli_stmt_close($insert_stmt);
                }
            } else {
                // If no existing images, just delete all
                $delete_sql = "DELETE FROM rooms_images WHERE room_id=?";
                $delete_stmt = mysqli_prepare($conn, $delete_sql);
                mysqli_stmt_bind_param($delete_stmt, "i", $room_id);
                mysqli_stmt_execute($delete_stmt);
                mysqli_stmt_close($delete_stmt);
            }
            
            // Then insert new additional images
            if (!empty($additional_images)) {
                $insert_sql = "INSERT INTO rooms_images (room_id, image_url) VALUES (?, ?)";
                $insert_stmt = mysqli_prepare($conn, $insert_sql);
                foreach ($additional_images as $image_url) {
                    mysqli_stmt_bind_param($insert_stmt, "is", $room_id, $image_url);
                    mysqli_stmt_execute($insert_stmt);
                }
                mysqli_stmt_close($insert_stmt);
            }
            
            // Handle amenities
            // First, delete existing amenities for this room
            $delete_amenities_sql = "DELETE FROM room_amenities WHERE room_id=?";
            $delete_amenities_stmt = mysqli_prepare($conn, $delete_amenities_sql);
            mysqli_stmt_bind_param($delete_amenities_stmt, "i", $room_id);
            mysqli_stmt_execute($delete_amenities_stmt);
            mysqli_stmt_close($delete_amenities_stmt);
            
            // Then insert new amenities
            if (!empty($amenities)) {
                $insert_amenities_sql = "INSERT INTO room_amenities (facility_name, room_id) VALUES (?, ?)";
                $insert_amenities_stmt = mysqli_prepare($conn, $insert_amenities_sql);
                foreach ($amenities as $amenity) {
                    $amenity = trim($amenity);
                    if (!empty($amenity)) {
                        mysqli_stmt_bind_param($insert_amenities_stmt, "si", $amenity, $room_id);
                        mysqli_stmt_execute($insert_amenities_stmt);
                    }
                }
                mysqli_stmt_close($insert_amenities_stmt);
            }
            
            header('Location: rooms.php?msg=updated');
            exit();
        } else {
            $error = 'Error updating room. Please try again.';
        }
        mysqli_stmt_close($stmt);       
    } else {
        // Add new room     
        $sql = "INSERT INTO rooms (title, description, room_image, price, rating, bed_size, guest_capacity, room_space, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssdsssi", $title, $description, $room_image, $price, $rating, $bed_size, $guest_capacity, $room_space, $status);
        
        if (mysqli_stmt_execute($stmt)) {
            $new_room_id = mysqli_insert_id($conn);
            
            // Insert additional images
            if (!empty($additional_images)) {
                $insert_sql = "INSERT INTO rooms_images (room_id, image_url) VALUES (?, ?)";
                $insert_stmt = mysqli_prepare($conn, $insert_sql);
                foreach ($additional_images as $image_url) {
                    mysqli_stmt_bind_param($insert_stmt, "is", $new_room_id, $image_url);
                    mysqli_stmt_execute($insert_stmt);
                }
                mysqli_stmt_close($insert_stmt);
            }
            
            // Insert amenities
            if (!empty($amenities)) {
                $insert_amenities_sql = "INSERT INTO room_amenities (facility_name, room_id) VALUES (?, ?)";
                $insert_amenities_stmt = mysqli_prepare($conn, $insert_amenities_sql);
                foreach ($amenities as $amenity) {
                    $amenity = trim($amenity);
                    if (!empty($amenity)) {
                        mysqli_stmt_bind_param($insert_amenities_stmt, "si", $amenity, $new_room_id);
                        mysqli_stmt_execute($insert_amenities_stmt);
                    }
                }
                mysqli_stmt_close($insert_amenities_stmt);
            }
            
            header('Location: rooms.php?msg=added');
            exit();
        } else {
            $error = 'Error adding room. Please try again.';
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'layout.php'; ?>
    <title><?php echo $editing ? 'Edit Room' : 'Add Room'; ?> - StayByte Admin</title>
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>
        
        <main class="main-content">
            <div class="container-fluid">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h1><?php echo $editing ? 'Edit Room' : 'Add New Room'; ?></h1>
                        <p class="text-muted"><?php echo $editing ? 'Update room details' : 'Create a new room'; ?></p>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="rooms.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Rooms
                        </a>
                    </div>
                </div>
                
                <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Room Title *</label>
                                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Price per Night ($)</label>
                                        <input type="number" class="form-control" id="price" name="price" step="0.01" value="<?php echo $price; ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="bed_size" class="form-label">Bed Size</label>
                                        <input type="text" class="form-control" id="bed_size" name="bed_size" value="<?php echo htmlspecialchars($bed_size); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="guest_capacity" class="form-label">Guest Capacity</label>
                                        <input type="number" class="form-control" id="guest_capacity" name="guest_capacity" value="<?php echo $guest_capacity; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="room_space" class="form-label">Room Space (sq ft)</label>
                                        <input type="number" class="form-control" id="room_space" name="room_space" value="<?php echo $room_space; ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="rating" class="form-label">Rating (0-5)</label>
                                        <input type="number" class="form-control" id="rating" name="rating" step="0.01" min="0" max="5" value="<?php echo $rating; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="Active" <?php echo ($status == 'Active') ? 'selected' : ''; ?>>Active</option>
                                            <option value="Inactive" <?php echo ($status == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="room_image" class="form-label">Primary Room Image</label>
                                <input type="file" class="form-control" id="room_image" name="room_image" accept="image/*">
                                <?php if (!empty($room_image) && $editing): ?>
                                    <div class="mt-2">
                                        <img src="../<?php echo $room_image; ?>" alt="Current image" style="max-width: 200px; max-height: 150px;">
                                        <input type="hidden" name="current_image" value="<?php echo $room_image; ?>">
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="additional_images" class="form-label">Additional Room Images</label>
                                <input type="file" class="form-control" id="additional_images" name="additional_images[]" accept="image/*" multiple>
                                <?php if (!empty($room_images) && $editing): ?>
                                    <div class="mt-2">
                                        <p class="text-muted">Current additional images:</p>
                                        <div class="d-flex flex-wrap gap-2" id="additional-images-container">
                                            <?php foreach ($room_images as $index => $image): ?>
                                                <div class="position-relative" style="width: 100px; height: 75px;" data-image-index="<?php echo $index; ?>">
                                                    <img src="../<?php echo $image; ?>" alt="Additional image" style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px;">
                                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0" style="padding: 2px 5px; font-size: 0.7rem;" onclick="deleteRoomImage(<?php echo $index; ?>, this)" title="Delete image">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                    <input type="hidden" name="existing_images[]" value="<?php echo htmlspecialchars($image); ?>">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($description); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="amenities" class="form-label">Amenities</label>
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" id="amenity-input" placeholder="Type an amenity and press Enter">
                                    <button class="btn btn-outline-secondary" type="button" id="add-amenity">Add</button>
                                </div>
                                <div class="form-text">Press Enter or click Add to add amenities as tags</div>
                                
                                <!-- Hidden input to store amenities as array -->
                                <input type="hidden" id="amenities-hidden" name="amenities" value="<?php echo htmlspecialchars(implode(',', $room_amenities)); ?>">
                                
                                <!-- Container to display amenities as tags -->
                                <div id="amenities-container" class="mt-2">
                                    <?php if (!empty($room_amenities)): ?>
                                        <?php foreach ($room_amenities as $amenity): ?>
                                            <span class="badge bg-primary me-1 mb-1" data-amenity="<?php echo htmlspecialchars($amenity); ?>">
                                                <?php echo htmlspecialchars($amenity); ?>
                                                <button type="button" class="btn-close btn-close-white ms-1" aria-label="Remove" onclick="removeAmenity(this)"></button>
                                            </span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> <?php echo $editing ? 'Update Room' : 'Add Room'; ?>
                                </button>
                                <a href="rooms.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Amenities functionality
        document.addEventListener('DOMContentLoaded', function() {
            const amenityInput = document.getElementById('amenity-input');
            const addAmenityBtn = document.getElementById('add-amenity');
            const amenitiesContainer = document.getElementById('amenities-container');
            const amenitiesHidden = document.getElementById('amenities-hidden');
            
            // Add amenity on button click
            addAmenityBtn.addEventListener('click', function() {
                addAmenity();
            });
            
            // Add amenity on Enter key
            amenityInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    addAmenity();
                }
            });
            
            // Function to add amenity
            function addAmenity() {
                const amenity = amenityInput.value.trim();
                if (amenity) {
                    // Check if amenity already exists
                    const existingTags = amenitiesContainer.querySelectorAll('[data-amenity]');
                    let exists = false;
                    existingTags.forEach(tag => {
                        if (tag.getAttribute('data-amenity').toLowerCase() === amenity.toLowerCase()) {
                            exists = true;
                        }
                    });
                    
                    if (!exists) {
                        // Create tag element
                        const tag = document.createElement('span');
                        tag.className = 'badge bg-primary me-1 mb-1';
                        tag.setAttribute('data-amenity', amenity);
                        tag.innerHTML = `
                            ${amenity}
                            <button type="button" class="btn-close btn-close-white ms-1" aria-label="Remove" onclick="removeAmenity(this)"></button>
                        `;
                        
                        amenitiesContainer.appendChild(tag);
                        updateHiddenInput();
                    }
                    
                    amenityInput.value = '';
                }
            }
            
            // Update hidden input with current amenities
            function updateHiddenInput() {
                const tags = amenitiesContainer.querySelectorAll('[data-amenity]');
                const amenities = [];
                tags.forEach(tag => {
                    amenities.push(tag.getAttribute('data-amenity'));
                });
                amenitiesHidden.value = amenities.join(',');
            }
        });
        
        // Function to remove amenity
        function removeAmenity(button) {
            const tag = button.parentElement;
            tag.remove();
            updateHiddenInput();
        }
        
        // Function to delete room image
        function deleteRoomImage(imageIndex, element) {
            if (confirm('Are you sure you want to delete this image?')) {
                // Remove the image from the DOM
                element.parentElement.remove();
                
                // In a real implementation, you would make an AJAX call to delete the image
                // For now, we'll just mark it for deletion on the server side
                // by adding it to a hidden input field
                
                // Create a hidden input to track deleted images if it doesn't exist
                let deletedImagesInput = document.getElementById('deleted-images');
                if (!deletedImagesInput) {
                    deletedImagesInput = document.createElement('input');
                    deletedImagesInput.type = 'hidden';
                    deletedImagesInput.id = 'deleted-images';
                    deletedImagesInput.name = 'deleted_images';
                    deletedImagesInput.value = '';
                    document.querySelector('form').appendChild(deletedImagesInput);
                }
                
                // Add the image index to the deleted images list
                const currentDeleted = deletedImagesInput.value ? deletedImagesInput.value.split(',') : [];
                if (!currentDeleted.includes(imageIndex.toString())) {
                    currentDeleted.push(imageIndex.toString());
                    deletedImagesInput.value = currentDeleted.join(',');
                }
            }
        }
    </script>
</body>
</html>
