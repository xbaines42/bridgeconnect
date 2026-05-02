<?php
session_start();
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=? AND password=?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows == 1) {
        $user = $result->fetch_assoc();

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] == 'person_in_need') {
            header("Location: user_dashboard.php");
        } elseif ($user['role'] == 'shelter_provider') {
            header("Location: provider_dashboard.php");
        } elseif ($user['role'] == 'volunteer') {
            header("Location: volunteer_dashboard.php");
        } elseif ($user['role'] == 'admin') {
            header("Location: admin_dashboard.php");
        }
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - BridgeConnect</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="page-bg">
    <div class="container small">
        <a href="index.php" class="back-link">← Back Home</a>
        <div class="form-card">
            <h1>Login</h1>
            <p class="muted">Sign in to access your role-based dashboard.</p>

            <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

            <form method="POST">
                <label>Email</label>
                <input type="email" name="email" required>

                <label>Password</label>
                <input type="password" name="password" required>

                <button type="submit">Login</button>
            </form>
        </div>

        <div class="demo-box">
            <h3>Demo Accounts</h3>
            <p><strong>Person in Need:</strong> user@test.com / 1234</p>
            <p><strong>Shelter Provider:</strong> provider@test.com / 1234</p>
            <p><strong>Volunteer:</strong> volunteer@test.com / 1234</p>
            <p><strong>Admin:</strong> admin@test.com / 1234</p>
        </div>
    </div>
</div>
</body>
</html>
