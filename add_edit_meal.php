<?php
session_start();
header('Content-Type: application/json');
include 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    echo json_encode(['success'=>false, 'error'=>'Unauthorized']);
    exit;
}
$provider_id = $_SESSION['user_id'];

// Collect and sanitize input
$meal_id = isset($_POST['meal_id']) ? intval($_POST['meal_id']) : 0;
$meal_title = trim($_POST['meal_title'] ?? '');
$price = floatval($_POST['price'] ?? 0);
$status = $_POST['status'] ?? 'Active';
$description = trim($_POST['description'] ?? '');
$meal_type = $_POST['meal_type'] ?? 'Veg';
$items_included = trim($_POST['items_included'] ?? '');
$meal_address = trim($_POST['meal_address'] ?? '');

// Handle image upload
$image_url = null;
if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] == 0) {
    $target_dir = 'uploads/';
    $ext = pathinfo($_FILES['image_url']['name'], PATHINFO_EXTENSION);
    $filename = 'meal_' . time() . '_' . rand(1000,9999) . '.' . $ext;
    $target_file = $target_dir . $filename;
    if (move_uploaded_file($_FILES['image_url']['tmp_name'], $target_file)) {
        $image_url = $target_file;
    }
}

if (!$meal_title || !$price) {
    echo json_encode(['success'=>false, 'error'=>'Meal title and price are required.']);
    exit;
}

if ($meal_id > 0) {
    // Edit existing meal
    $sql = "UPDATE meals SET meal_title=?, price=?, status=?, description=?, meal_type=?, items_included=?, meal_address=?" . ($image_url ? ", image_url=?" : "") . " WHERE meal_id=? AND meal_provider_id=?";
    $params = [$meal_title, $price, $status, $description, $meal_type, $items_included, $meal_address];
    if ($image_url) $params[] = $image_url;
    $params[] = $meal_id;
    $params[] = $provider_id;
    $types = 'sdssss' . ($image_url ? 's' : '') . 'ii';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $ok = $stmt->execute();
    if ($ok && $stmt->affected_rows > 0) {
        echo json_encode(['success'=>true]);
    } else {
        echo json_encode(['success'=>false, 'error'=>'Update failed or no changes.']);
    }
    $stmt->close();
} else {
    // Add new meal
    $sql = "INSERT INTO meals (meal_title, price, status, description, meal_type, items_included, meal_address, image_url, meal_provider_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sdssssssi', $meal_title, $price, $status, $description, $meal_type, $items_included, $meal_address, $image_url, $provider_id);
    $ok = $stmt->execute();
    if ($ok) {
        echo json_encode(['success'=>true]);
    } else {
        echo json_encode(['success'=>false, 'error'=>'Insert failed.']);
    }
    $stmt->close();
}
$conn->close(); 