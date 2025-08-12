<?php

function fetchFeedback($conn, $limit = 5) {
    $sql = "SELECT f.feedback_message, u.username, u.profile_pic FROM feedback f JOIN users u ON f.user_id = u.user_id ORDER BY f.id DESC LIMIT ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $limit);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $feedbacks = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $feedbacks[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    return $feedbacks;
}

?>
