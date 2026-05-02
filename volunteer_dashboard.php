<?php
require_once "auth.php";
require_once "db.php";
requireRole(['volunteer']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $description = $_POST['description'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $user_id = intval($_SESSION['user_id']);

    $stmt = $conn->prepare("INSERT INTO resources (name, type, description, address, phone, updated_by) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $name, $type, $description, $address, $phone, $user_id);
    $stmt->execute();
    $message = "Resource added successfully.";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Volunteer - BridgeConnect</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="topbar">
    <h2>BridgeConnect</h2>
    <div>
        <span><?php echo htmlspecialchars($_SESSION['name']); ?></span>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container small">
    <div class="dashboard-title">
        <h1>Volunteer Dashboard</h1>
        <p class="muted">Add community resources, food drives, hygiene support, and job support.</p>
    </div>

    <?php if (!empty($message)) echo "<p class='success'>$message</p>"; ?>

    <div class="form-card">
        <form method="POST">
            <label>Resource Name</label>
            <input name="name" required>

            <label>Type</label>
            <select name="type" required>
                <option value="food">Food</option>
                <option value="medical">Medical</option>
                <option value="hygiene">Hygiene</option>
                <option value="job_support">Job Support</option>
                <option value="other">Other</option>
            </select>

            <label>Description</label>
            <textarea name="description"></textarea>

            <label>Address</label>
            <input name="address">

            <label>Phone</label>
            <input name="phone">

            <button type="submit">Add Resource</button>
        </form>
    </div>
</div>
</body>
</html>
