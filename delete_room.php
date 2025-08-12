<?php
session_start();
header('Content-Type: application/json');
include 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    echo json_encode(['success'=>false, 'error'=>'Unauthorized']);
    exit;
}
$provider_id = $_SESSION['user_id'];
$room_id = isset($_POST['room_id']) ? intval($_POST['room_id']) : 0;
if ($room_id <= 0) {
    echo json_encode(['success'=>false, 'error'=>'Invalid room ID.']);
    exit;
}
$sql = "DELETE FROM rooms WHERE room_id=? AND room_provider_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $room_id, $provider_id);
$stmt->execute();
if ($stmt->affected_rows > 0) {
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false, 'error'=>'Delete failed or not allowed.']);
}
$stmt->close();
$conn->close(); 