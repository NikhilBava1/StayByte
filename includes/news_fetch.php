<?php
// Function to fetch news from database
function fetchNews($conn, $limit = 10) {
    $sql = "SELECT news_id, title, image_url, publish_date FROM news WHERE status = 'Active' ORDER BY publish_date DESC LIMIT ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $limit);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $news = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $news[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    return $news;
}

// Function to format date for display
function formatDate($date) {
    $timestamp = strtotime($date);
    return date('M d, Y', $timestamp);
}
?>
