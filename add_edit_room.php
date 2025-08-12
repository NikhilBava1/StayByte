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
$room_id = isset($_POST['room_id']) ? intval($_POST['room_id']) : 0;
$title = trim($_POST['title'] ?? '');
$price = floatval($_POST['price'] ?? 0);
$status = $_POST['status'] ?? 'Active';
$description = trim($_POST['description'] ?? '');

// Handle image upload
$room_image = null;
if (isset($_FILES['room_image']) && $_FILES['room_image']['error'] == 0) {
    $target_dir = 'uploads/';
    $ext = pathinfo($_FILES['room_image']['name'], PATHINFO_EXTENSION);
    $filename = 'room_' . time() . '_' . rand(1000,9999) . '.' . $ext;
    $target_file = $target_dir . $filename;
    if (move_uploaded_file($_FILES['room_image']['tmp_name'], $target_file)) {
        $room_image = $target_file;
    }
}

if (!$title || !$price) {
    echo json_encode(['success'=>false, 'error'=>'Title and price are required.']);
    exit;
}

if ($room_id > 0) {
    // Edit existing room
    $sql = "UPDATE rooms SET title=?, price=?, status=?, description=?" . ($room_image ? ", room_image=?" : "") . " WHERE room_id=? AND room_provider_id=?";
    $params = [$title, $price, $status, $description];
    if ($room_image) $params[] = $room_image;
    $params[] = $room_id;
    $params[] = $provider_id;
    $types = 'sdss' . ($room_image ? 's' : '') . 'ii';
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
    // Add new room
    $sql = "INSERT INTO rooms (title, price, status, description, room_image, room_provider_id) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sdsssi', $title, $price, $status, $description, $room_image, $provider_id);
    $ok = $stmt->execute();
    if ($ok) {
        echo json_encode(['success'=>true]);
    } else {
        echo json_encode(['success'=>false, 'error'=>'Insert failed.']);
    }
    $stmt->close();
}
$conn->close(); 