<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>BridgeConnect</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="hero">
    <div class="nav">
        <h2>BridgeConnect</h2>
        <div>
            <a href="login.php">Login</a>
            <a class="btn nav-btn" href="signup.php">Create Account</a>
        </div>
    </div>

    <div class="hero-content">
        <h1>Connecting people to resources<br>when they need them most.</h1>
        <p>Find shelters, food drives, medical help, hygiene services, job support, and real-time shelter bed availability.</p>
        <a class="btn large" href="login.php">Get Started</a>
    </div>
</div>

<div class="section">
    <h2>What BridgeConnect Offers</h2>
    <div class="grid">
        <div class="card">
            <div class="icon">🏠</div>
            <h3>Find Shelter</h3>
            <p>View available shelter beds and get directions quickly.</p>
        </div>
        <div class="card">
            <div class="icon">🍽️</div>
            <h3>Food Resources</h3>
            <p>Find food pantries, meal programs, and food drives.</p>
        </div>
        <div class="card">
            <div class="icon">🔐</div>
            <h3>Role-Based Access</h3>
            <p>Providers update beds, volunteers add resources, and admins manage the system.</p>
        </div>
    </div>
</div>
</body>
</html>
