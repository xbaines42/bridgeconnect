<?php
$host = "127.0.0.1";
$username = "root";
$password = "root";
$database = "bridgeconnect";
$port = 8889;

$conn = new mysqli($host, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("<div style='font-family:Arial;padding:30px;'>
        <h2>Database Connection Failed</h2>
        <p>" . $conn->connect_error . "</p>
        <p>Run the installer first:</p>
        <a href='install.php'>Install Database</a>
    </div>");
}

$conn->set_charset("utf8mb4");
?>
