<?php
/**
 * Fetches the latest active news articles from the database using PDO.
 *
 * @param PDO $conn The PDO database connection object.
 * @param int $limit The maximum number of news articles to retrieve.
 * @return array An array of news articles, each as an associative array.
 */
function fetchNews(PDO $conn, $limit = 10) {
    // SQL query using a prepared statement placeholder (?).
    // Note: No need to quote standard identifiers when using PDO with PostgreSQL.
    $sql = 'SELECT news_id, title, image_url, publish_date FROM news WHERE status = \'Active\' ORDER BY publish_date DESC LIMIT ?';

    try {
        // Prepare the statement using the PDO connection object.
        $stmt = $conn->prepare($sql);

        // Bind the limit parameter.
        $stmt->bindParam(1, $limit, PDO::PARAM_INT);

        // Execute the statement.
        $stmt->execute();
        
        // Fetch all resulting rows into an associative array.
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        // In a real application, you should log this error properly.
        error_log("Database error in fetchNews: " . $e->getMessage());
        return [];
    }
}

/**
 * Fetches the latest feedback entries from the database using PDO.
 *
 * @param PDO $conn The PDO database connection object.
 * @param int $limit The maximum number of feedback entries to retrieve.
 * @return array An array of feedback entries.
 */
function fetchFeedback(PDO $conn, $limit = 5) {
    // SQL query using a prepared statement placeholder (?).
    $sql = 'SELECT f.feedback_message, u.username, u.profile_pic 
            FROM feedback f 
            JOIN users u ON f.user_id = u.user_id 
            ORDER BY f.id DESC 
            LIMIT ?';
    
    try {
        // Prepare the statement.
        $stmt = $conn->prepare($sql);

        // Bind the limit parameter.
        $stmt->bindParam(1, $limit, PDO::PARAM_INT);

        // Execute the statement.
        $stmt->execute();
        
        // Fetch all results.
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        // Log the error.
        error_log("Database error in fetchFeedback: " . $e->getMessage());
        return [];
    }
}


/**
 * Formats a date string for display.
 * This function is database-agnostic and does not need any changes.
 *
 * @param string $date The date string (e.g., '2025-08-19').
 * @return string The formatted date (e.g., 'Aug 19, 2025').
 */
function formatDate($date) {
    $timestamp = strtotime($date);
    return date('M d, Y', $timestamp);
}
?>
