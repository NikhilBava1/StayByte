<?php
// Database configuration for admin panel
$db_host = "dpg-d2hfcrndiees73efetp0-a.oregon-postgres.render.com";
$db_user = "staybyte1_83s5_user";
$db_pass = "nPpzEff0iEOM0eloVjoc8ksdoK7dJPPx";
$db_name = "staybyte1_83s5";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
