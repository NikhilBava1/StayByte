<?php
// submit_stay_rating.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');
include 'config/db.php';

// Ensure POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Login required']);
    exit;
}

$user_id = intval($_SESSION['user_id']);
$stay_id = intval($_POST['stay_id'] ?? 0);
$rating  = intval($_POST['rating'] ?? 0);

if ($stay_id <= 0 || $rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

// Check if stay exists
$stmtChk = $conn->prepare("SELECT stay_id FROM stays WHERE stay_id = ? LIMIT 1");
$stmtChk->bind_param("i", $stay_id);
$stmtChk->execute();
$resChk = $stmtChk->get_result();
if (!$resChk || $resChk->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Stay not found']);
    exit;
}
$stmtChk->close();

// Check if user already rated this stay
$stmt = $conn->prepare("SELECT id FROM rating WHERE stay_id = ? AND user_id = ? LIMIT 1");
$stmt->bind_param("ii", $stay_id, $user_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Update rating
    $stmt->close();
    $stmtUpdate = $conn->prepare("UPDATE rating SET rating = ?, created_at = NOW() WHERE stay_id = ? AND user_id = ?");
    $stmtUpdate->bind_param("iii", $rating, $stay_id, $user_id);
    if ($stmtUpdate->execute()) {
        $stmtUpdate->close();

        // Recalculate average
        $stmtAvg = $conn->prepare("SELECT COUNT(*) as cnt, AVG(rating) as avg_rating FROM rating WHERE stay_id = ?");
        $stmtAvg->bind_param("i", $stay_id);
        $stmtAvg->execute();
        $resAvg = $stmtAvg->get_result();
        $avg = 0; $cnt = 0;
        if ($resAvg && $row = $resAvg->fetch_assoc()) {
            $cnt = intval($row['cnt']);
            $avg = round(floatval($row['avg_rating']), 2);
        }
        $stmtAvg->close();

        echo json_encode(['success' => true, 'message' => 'Rating updated', 'avg' => $avg, 'count' => $cnt]);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error updating rating']);
        exit;
    }
} else {
    $stmt->close();
    // Insert new rating
    $stmtInsert = $conn->prepare("INSERT INTO rating (stay_id, user_id, rating) VALUES (?, ?, ?)");
    $stmtInsert->bind_param("iii", $stay_id, $user_id, $rating);
    if ($stmtInsert->execute()) {
        $stmtInsert->close();

        // Recalculate average
        $stmtAvg = $conn->prepare("SELECT COUNT(*) as cnt, AVG(rating) as avg_rating FROM rating WHERE stay_id = ?");
        $stmtAvg->bind_param("i", $stay_id);
        $stmtAvg->execute();
        $resAvg = $stmtAvg->get_result();
        $avg = 0; $cnt = 0;
        if ($resAvg && $row = $resAvg->fetch_assoc()) {
            $cnt = intval($row['cnt']);
            $avg = round(floatval($row['avg_rating']), 2);
        }
        $stmtAvg->close();

        echo json_encode(['success' => true, 'message' => 'Rating submitted', 'avg' => $avg, 'count' => $cnt]);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error inserting rating']);
        exit;
    }
}
?>
