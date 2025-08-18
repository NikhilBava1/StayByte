<?php
// Database configuration for admin panel
$HOST = "dpg-d2hfcrndiees73efetp0-a.oregon-postgres.render.com";
$port = "5432";
$DATABASE_NAME = "dpg-d2hfcrndiees73efetp0-a.oregon-postgres.render.com"; = "staybyte1_83s5";
$USER = "staybyte1_83s5_user";
$USER = "nPpzEff0iEOM0eloVjoc8ksdoK7dJPPx";

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
     die("Connection failed: " . pg_last_error());
}
?>
