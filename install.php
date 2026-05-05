<?php
$host = "localhost";
$username = "root";
$password = "root";
$port = 3306;

$conn = new mysqli($host, $username, $password, "", $port);

if ($conn->connect_error) {
    die("MAMP MySQL Connection Failed: " . $conn->connect_error);
}

$sql = file_get_contents("setup.sql");

if ($conn->multi_query($sql)) {
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->more_results() && $conn->next_result());
} else {
    die("Install error: " . $conn->error);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>BridgeConnect Installed</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="page-bg">
    <div class="container small">
        <div class="form-card center">
            <h1>BridgeConnect Installed</h1>
            <p class="muted">Database, tables, demo users, and demo resources were created successfully.</p>
            <a class="btn" href="index.php">Go to Homepage</a>
            <a class="btn secondary" href="login.php">Go to Login</a>
        </div>
    </div>
</div>
</body>
</html>
