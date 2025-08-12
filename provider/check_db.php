<?php
include '../config/db.php';

// Check rooms table structure
$result = mysqli_query($conn, "DESCRIBE rooms");
if ($result) {
    echo "Rooms table structure:\n";
    while ($row = mysqli_fetch_assoc($result)) {
        print_r($row);
    }
} else {
    echo "Error: " . mysqli_error($conn);
}

// Check meals table structure
$result = mysqli_query($conn, "DESCRIBE meals");
if ($result) {
    echo "\n\nMeals table structure:\n";
    while ($row = mysqli_fetch_assoc($result)) {
        print_r($row);
    }
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
