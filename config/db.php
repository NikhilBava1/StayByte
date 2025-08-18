<?php
// Database configuration for admin panel
$host = "dpg-d2hfcrndiees73efetp0-a.oregon-postgres.render.com";
$port = "5432";
$dbname = "staybyte1_83s5";
$user = "staybyte1_83s5_user";
$password = "nPpzEff0iEOM0eloVjoc8ksdoK7dJPPx";

try {
    // DSN (Data Source Name)
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";
    
    // Create PDO instance
    $conn = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo "✅ Connected to PostgreSQL successfully using PDO!";
} catch (PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage();
}
?>
