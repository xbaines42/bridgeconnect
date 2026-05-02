<?php
require_once "auth.php";
require_once "db.php";
requireRole(['shelter_provider']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $resource_id = intval($_POST['resource_id']);
    $available_beds = intval($_POST['available_beds']);
    $user_id = intval($_SESSION['user_id']);

    $stmt = $conn->prepare("UPDATE resources SET available_beds=?, updated_by=? WHERE id=? AND type='shelter'");
    $stmt->bind_param("iii", $available_beds, $user_id, $resource_id);
    $stmt->execute();
    $message = "Bed count updated successfully.";
}

$result = $conn->query("SELECT * FROM resources WHERE type='shelter' ORDER BY name ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Shelter Provider - BridgeConnect</title>
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
        <h1>Shelter Provider Dashboard</h1>
        <p class="muted">Update available shelter beds in real time.</p>
    </div>

    <?php if (!empty($message)) echo "<p class='success'>$message</p>"; ?>

    <div class="resource-grid">
        <?php while ($row = $result->fetch_assoc()): ?>
            <form method="POST" class="card">
                <span class="pill">shelter</span>
                <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                <p><?php echo htmlspecialchars($row['description']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($row['address']); ?></p>

                <label>Available Beds</label>
                <input type="hidden" name="resource_id" value="<?php echo $row['id']; ?>">
                <input type="number" name="available_beds" value="<?php echo $row['available_beds']; ?>" min="0">

                <button type="submit">Update Beds</button>
            </form>
        <?php endwhile; ?>
    </div>
</div>
</body>
</html>
