<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Login required']);
    exit();
}

$user_id = $_SESSION['user_id'];
$meal_id = intval($_POST['meal_id']);
$rating  = intval($_POST['rating']);

if ($meal_id <= 0 || $rating < 1 || $rating > 5) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    exit();
}

// Check if rating exists
$check = $conn->prepare("SELECT id FROM rating WHERE meal_id = ? AND user_id = ?");
$check->bind_param("ii", $meal_id, $user_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    // Update
    $update = $conn->prepare("UPDATE rating SET rating = ? WHERE meal_id = ? AND user_id = ?");
    $update->bind_param("iii", $rating, $meal_id, $user_id);
    $update->execute();
} else {
    // Insert
    $insert = $conn->prepare("INSERT INTO rating (meal_id, user_id, rating) VALUES (?, ?, ?)");
    $insert->bind_param("iii", $meal_id, $user_id, $rating);
    $insert->execute();
}

echo json_encode(['status' => 'success', 'message' => 'Rating saved successfully']);
