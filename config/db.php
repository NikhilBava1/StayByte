<?php
$host = "dpg-d2hfcrndiees73efetp0-a";  
$port = "5432";                        
$dbname = "staybyte1_83s5";              
$user = "staybyte1_83s5_user";                 
$password = "nPpzEff0iEOM0eloVjoc8ksdoK7dJPPx";          

// Connection
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Connection failed: " . pg_last_error());
} else {
    echo "Database connected successfully!";
}
?>
