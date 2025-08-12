<?php

    include '../config/db.php';
// fetch all rooms from the database 
    $sql = "SELECT * FROM room";
    $result = mysqli_query($conn, $sql);

    $rooms = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // close the database connection
    mysqli_close($conn);

    // output the rooms in JSON format
    header('Content-Type: application/json');
    echo json_encode($rooms);
?>  