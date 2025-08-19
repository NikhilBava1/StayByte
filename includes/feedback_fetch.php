<?php
/**
 * Fetches the latest active news articles from the PostgreSQL database.
 *
 * @param resource $conn The PostgreSQL database connection resource.
 * @param int $limit The maximum number of news articles to retrieve.
 * @return array An array of news articles, each as an associative array.
 */
function fetchNews($conn, $limit = 10) {
    // SQL query using PostgreSQL syntax with a positional placeholder ($1).
    $sql = 'SELECT news_id, title, image_url, publish_date FROM news WHERE status = \'Active\' ORDER BY publish_date DESC LIMIT $1';

    // A unique name for the prepared statement
    $statementName = "fetch_latest_news";

    // Prepare the statement for execution.
    $stmt = pg_prepare($conn, $statementName, $sql);

    // Check if the statement was prepared successfully
    if (!$stmt) {
        // In a real application, you should log this error properly.
        error_log("Failed to prepare statement: " . pg_last_error($conn));
        return [];
    }

    // Execute the prepared statement with the limit parameter.
    $result = pg_execute($conn, $statementName, [$limit]);

    // Check for execution errors
    if (!$result) {
        error_log("Failed to execute statement: " . pg_last_error($conn));
        return [];
    }
    
    // Fetch all resulting rows into an associative array.
    $news = pg_fetch_all($result, PGSQL_ASSOC);
    
    // Free up the result memory.
    pg_free_result($result);
    
    // If pg_fetch_all returns false (e.g., no rows found), return an empty array.
    return $news ?: [];
}

/**
 * Fetches the latest feedback entries from the PostgreSQL database.
 *
 * @param resource $conn The PostgreSQL database connection resource.
 * @param int $limit The maximum number of feedback entries to retrieve.
 * @return array An array of feedback entries.
 */
function fetchFeedback($conn, $limit = 5) {
    // SQL query using PostgreSQL syntax (double quotes for identifiers, $1 for placeholder).
    $sql = 'SELECT f.feedback_message, u.username, u.profile_pic 
            FROM feedback f 
            JOIN users u ON f.user_id = u.user_id 
            ORDER BY f.id DESC 
            LIMIT $1';
    
    $statementName = "fetch_latest_feedback";

    // Prepare the statement.
    $stmt = pg_prepare($conn, $statementName, $sql);
    if (!$stmt) {
        error_log("Failed to prepare statement: " . pg_last_error($conn));
        return [];
    }

    // Execute the statement.
    $result = pg_execute($conn, $statementName, [$limit]);
    if (!$result) {
        error_log("Failed to execute statement: " . pg_last_error($conn));
        return [];
    }
    
    // Fetch all results.
    $feedbacks = pg_fetch_all($result, PGSQL_ASSOC);
    
    // Free result memory.
    pg_free_result($result);
    
    return $feedbacks ?: [];
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
