<?php
// Database configuration for admin panel
$host = "dpg-d2hfcrndiees73efetp0-a.oregon-postgres.render.com";
$port = "5432"
$user = "staybyte1_83s5_user";
$password = "nPpzEff0iEOM0eloVjoc8ksdoK7dJPPx";
$dbname = "staybyte1_83s5";

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
     die("Connection failed: " . pg_last_error());
}
?>
