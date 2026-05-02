<?php
require_once "auth.php";
require_once "db.php";
requireRole(['admin']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $description = $_POST['description'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $available_beds = intval($_POST['available_beds']);
    $user_id = intval($_SESSION['user_id']);

    $stmt = $conn->prepare("INSERT INTO resources (name, type, description, address, phone, available_beds, updated_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssii", $name, $type, $description, $address, $phone, $available_beds, $user_id);
    $stmt->execute();
    $message = "Resource added successfully.";
}

$result = $conn->query("SELECT * FROM resources ORDER BY type, name");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - BridgeConnect</title>
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

<div class="container">
    <div class="dashboard-title">
        <h1>Admin Dashboard</h1>
        <p class="muted">Manage resources across the entire system.</p>
    </div>

    <?php if (!empty($message)) echo "<p class='success'>$message</p>"; ?>

    <div class="form-card">
        <h2>Add Resource</h2>
        <form method="POST">
            <label>Resource Name</label>
            <input name="name" required>

            <label>Type</label>
            <select name="type" required>
                <option value="shelter">Shelter</option>
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

            <label>Available Beds</label>
            <input type="number" name="available_beds" value="0" min="0">

            <button type="submit">Add Resource</button>
        </form>
    </div>

    <h2>All Resources</h2>
    <div class="resource-grid">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="card">
                <span class="pill"><?php echo htmlspecialchars($row['type']); ?></span>
                <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                <p><?php echo htmlspecialchars($row['description']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($row['address']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($row['phone']); ?></p>
                <p><strong>Available Beds:</strong> <?php echo $row['available_beds']; ?></p>
                <a class="danger" href="delete_resource.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this resource?');">Delete</a>
            </div>
        <?php endwhile; ?>
    </div>
</div>
</body>
</html>
