<?php
$host = "127.0.0.1";
$username = "root";
$password = "root";
$database = "bridgeconnect";
$port = 8889;

$conn = new mysqli($host, $username, $password, "", $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Force create and select database first
$conn->query("CREATE DATABASE IF NOT EXISTS `$database`");
$conn->select_db($database);

// Load setup.sql
$sql = file_get_contents("setup.sql");

// Remove phpMyAdmin comments
$sql = preg_replace('/--.*(\r\n|\r|\n)/', '', $sql);

// Remove CREATE DATABASE and USE lines because install.php handles that now
$sql = preg_replace('/CREATE DATABASE.*?;/is', '', $sql);
$sql = preg_replace('/USE\s+`?bridgeconnect`?\s*;/i', '', $sql);

// Split and run queries
$queries = array_filter(array_map('trim', explode(';', $sql)));

foreach ($queries as $query) {
    if ($query === '') {
        continue;
    }

    if (!$conn->query($query)) {
        die("<h2>Install Error</h2><p>" . $conn->error . "</p><pre>" . htmlspecialchars($query) . "</pre>");
    }
}

echo "<h1>BridgeConnect Installed Successfully</h1>";
echo "<p>Database and tables were created.</p>";
echo "<a href='index.php'>Go to App</a>";
?>