<?php
session_start();
include '../config/db.php';

// Check if provider is logged in
if (!isset($_SESSION['provider_id'])) {
    header('Location: login.php');
    exit();
}

$provider_id = $_SESSION['provider_id'];

// Initialize variables
$meal_id = 0;
$meal_title = '';
$description = '';
$image_url = '';
$price = '';
$rating = '';
$meal_type = 'Veg';
$items_included = '';
$meal_address = '';
$status = 'Active';
$meal_images = array(); // For multiple images

$editing = false;

// Check if editing existing meal
if (isset($_GET['id'])) {
    $meal_id = intval($_GET['id']);
    $editing = true;
    
    // Fetch meal details (only if it belongs to this provider)
    $sql = "SELECT * FROM meals WHERE meal_id = ? AND meal_provider_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $meal_id, $provider_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($meal = mysqli_fetch_assoc($result)) {
        $meal_title = $meal['meal_title'];
        $description = $meal['description'];
        $image_url = $meal['image_url'];
        $price = $meal['price'];
        $rating = $meal['rating'];
        $meal_type = $meal['meal_type'];
        $items_included = $meal['items_included'];
        $meal_address = $meal['meal_address'];
        $status = $meal['status'];
        
        // Fetch additional meal images
        $images_sql = "SELECT image_url FROM meals_images WHERE meal_id = ? ORDER BY image_id ASC";
        $images_stmt = mysqli_prepare($conn, $images_sql);
        mysqli_stmt_bind_param($images_stmt, "i", $meal_id);
        mysqli_stmt_execute($images_stmt);
        $images_result = mysqli_stmt_get_result($images_stmt);
        while ($image = mysqli_fetch_assoc($images_result)) {
            $meal_images[] = $image['image_url'];
        }
        mysqli_stmt_close($images_stmt);
    } else {
        // Meal not found
        header('Location: meals.php?msg=not_found');
        exit();
    }
    mysqli_stmt_close($stmt);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $meal_title = trim($_POST['meal_title']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $rating = floatval($_POST['rating']);
    $meal_type = $_POST['meal_type'];
    $items_included = trim($_POST['items_included']);
    $meal_address = trim($_POST['meal_address']);
    $status = $_POST['status'];
    
    // Handle primary image upload
    if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] == 0) {
        $target_dir = '../uploads/meals/';
        
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $ext = pathinfo($_FILES['image_url']['name'], PATHINFO_EXTENSION);
        $filename = 'meal_' . time() . '_' . rand(1000,9999) . '.' . $ext;
        $target_file = $target_dir . $filename;
        
        if (move_uploaded_file($_FILES['image_url']['tmp_name'], $target_file)) {
            $image_url = 'uploads/meals/' . $filename;
        }
    }
    
    // Handle additional images upload
    $additional_images = array();
    if (isset($_FILES['additional_images'])) {
        $files = $_FILES['additional_images'];
        $file_count = count($files['name']);
        
        for ($i = 0; $i < $file_count; $i++) {
            if ($files['error'][$i] == 0) {
                $target_dir = '../uploads/meals/';
                
                // Create directory if it doesn't exist
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                
                $ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
                $filename = 'meal_' . time() . '_' . rand(1000,9999) . '_' . $i . '.' . $ext;
                $target_file = $target_dir . $filename;
                
                if (move_uploaded_file($files['tmp_name'][$i], $target_file)) {
                    $additional_images[] = 'uploads/meals/' . $filename;
                }
            }
        }
    }
    
    if ($editing) {
        // Update existing meal
        if (!empty($image_url)) {
            $sql = "UPDATE meals SET meal_title=?, description=?, image_url=?, price=?, rating=?, meal_type=?, items_included=?, meal_address=?, status=? WHERE meal_id=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssssdssssi", $meal_title, $description, $image_url, $price, $rating, $meal_type, $items_included, $meal_address, $status, $meal_id);
        } else {
            $sql = "UPDATE meals SET meal_title=?, description=?, price=?, rating=?, meal_type=?, items_included=?, meal_address=?, status=? WHERE meal_id=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssdssssi", $meal_title, $description, $price, $rating, $meal_type, $items_included, $meal_address, $status, $meal_id);
        }
        
        if (mysqli_stmt_execute($stmt)) {
            // Handle additional images
            // First, delete existing additional images for this meal
            $delete_sql = "DELETE FROM meals_images WHERE meal_id=?";
            $delete_stmt = mysqli_prepare($conn, $delete_sql);
            mysqli_stmt_bind_param($delete_stmt, "i", $meal_id);
            mysqli_stmt_execute($delete_stmt);
            mysqli_stmt_close($delete_stmt);
            
            // Then insert new additional images
            if (!empty($additional_images)) {
                $insert_sql = "INSERT INTO meals_images (meal_id, image_url) VALUES (?, ?)";
                $insert_stmt = mysqli_prepare($conn, $insert_sql);
                foreach ($additional_images as $image_url) {
                    mysqli_stmt_bind_param($insert_stmt, "is", $meal_id, $image_url);
                    mysqli_stmt_execute($insert_stmt);
                }
                mysqli_stmt_close($insert_stmt);
            }
            
            header('Location: meals.php?msg=updated');
            exit();
        } else {
            $error = 'Error updating meal. Please try again.';
        }
        mysqli_stmt_close($stmt);
    } else {
        // Add new meal (associated with provider)
        $sql = "INSERT INTO meals (meal_title, description, image_url, price, rating, meal_type, items_included, meal_address, status, meal_provider_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssdssssi", $meal_title, $description, $image_url, $price, $rating, $meal_type, $items_included, $meal_address, $status, $provider_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $new_meal_id = mysqli_insert_id($conn);
            
            // Insert additional images
            if (!empty($additional_images)) {
                $insert_sql = "INSERT INTO meals_images (meal_id, image_url) VALUES (?, ?)";
                $insert_stmt = mysqli_prepare($conn, $insert_sql);
                foreach ($additional_images as $image_url) {
                    mysqli_stmt_bind_param($insert_stmt, "is", $new_meal_id, $image_url);
                    mysqli_stmt_execute($insert_stmt);
                }
                mysqli_stmt_close($insert_stmt);
            }
            
            header('Location: meals.php?msg=added');
            exit();
        } else {
            $error = 'Error adding meal. Please try again.';
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'layout.php'; ?>
    <title><?php echo $editing ? 'Edit Meal' : 'Add Meal'; ?> - StayByte Provider</title>
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>
        
        <main class="main-content">
            <div class="container-fluid">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h1><?php echo $editing ? 'Edit Meal' : 'Add New Meal'; ?></h1>
                        <p class="text-muted"><?php echo $editing ? 'Update meal details' : 'Create a new meal'; ?></p>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="meals.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Meals
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
                                        <label for="meal_title" class="form-label">Meal Title *</label>
                                        <input type="text" class="form-control" id="meal_title" name="meal_title" value="<?php echo htmlspecialchars($meal_title); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Price ($)</label>
                                        <input type="number" class="form-control" id="price" name="price" step="0.01" value="<?php echo $price; ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="meal_type" class="form-label">Meal Type</label>
                                        <select class="form-control" id="meal_type" name="meal_type">
                                            <option value="Veg" <?php echo ($meal_type == 'Veg') ? 'selected' : ''; ?>>Vegetarian</option>
                                            <option value="Non-Veg" <?php echo ($meal_type == 'Non-Veg') ? 'selected' : ''; ?>>Non-Vegetarian</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="rating" class="form-label">Rating (0-5)</label>
                                        <input type="number" class="form-control" id="rating" name="rating" step="0.01" min="0" max="5" value="<?php echo $rating; ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="Active" <?php echo ($status == 'Active') ? 'selected' : ''; ?>>Active</option>
                                            <option value="Inactive" <?php echo ($status == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="meal_address" class="form-label">Meal Address/Location</label>
                                        <input type="text" class="form-control" id="meal_address" name="meal_address" value="<?php echo htmlspecialchars($meal_address); ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="image_url" class="form-label">Primary Meal Image</label>
                                <input type="file" class="form-control" id="image_url" name="image_url" accept="image/*">
                                <?php if (!empty($image_url) && $editing): ?>
                                    <div class="mt-2">
                                        <img src="../<?php echo $image_url; ?>" alt="Current image" style="max-width: 200px; max-height: 150px;">
                                        <input type="hidden" name="current_image" value="<?php echo $image_url; ?>">
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="additional_images" class="form-label">Additional Meal Images</label>
                                <input type="file" class="form-control" id="additional_images" name="additional_images[]" accept="image/*" multiple>
                                <?php if (!empty($meal_images) && $editing): ?>
                                    <div class="mt-2">
                                        <p class="text-muted">Current additional images:</p>
                                        <div class="d-flex flex-wrap gap-2">
                                            <?php foreach ($meal_images as $image): ?>
                                                <div class="position-relative" style="width: 100px; height: 75px;">
                                                    <img src="../<?php echo $image; ?>" alt="Additional image" style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px;">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="items_included" class="form-label">Items Included</label>
                                <textarea class="form-control" id="items_included" name="items_included" rows="2"><?php echo htmlspecialchars($items_included); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($description); ?></textarea>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> <?php echo $editing ? 'Update Meal' : 'Add Meal'; ?>
                                </button>
                                <a href="meals.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
