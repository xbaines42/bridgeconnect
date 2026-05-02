<?php
session_start();
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $password, $role);

    if ($stmt->execute()) {
        header("Location: login.php");
        exit();
    } else {
        $error = "Could not create account. Email may already be used.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Account - BridgeConnect</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="page-bg">
    <div class="container small">
        <a href="index.php" class="back-link">← Back Home</a>
        <div class="form-card">
            <h1>Create Account</h1>
            <p class="muted">Choose your role to access the correct dashboard.</p>

            <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

            <form method="POST">
                <label>Name</label>
                <input name="name" required>

                <label>Email</label>
                <input type="email" name="email" required>

                <label>Password</label>
                <input type="password" name="password" required>

                <label>Role</label>
                <select name="role" required>
                    <option value="person_in_need">Person in Need</option>
                    <option value="volunteer">Volunteer</option>
                    <option value="shelter_provider">Shelter Provider</option>
                    <option value="admin">Admin</option>
                </select>

                <button type="submit">Create Account</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
