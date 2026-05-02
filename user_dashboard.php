<?php
require_once "auth.php";
require_once "db.php";
requireRole(['person_in_need']);

$type = $_GET['type'] ?? '';
$search = $_GET['search'] ?? '';

$query = "SELECT * FROM resources WHERE 1=1";
$params = [];
$types = "";

if ($type != '') {
    $query .= " AND type=?";
    $params[] = $type;
    $types .= "s";
}

if ($search != '') {
    $query .= " AND (name LIKE ? OR description LIKE ? OR address LIKE ?)";
    $like = "%$search%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= "sss";
}

$query .= " ORDER BY CASE WHEN type='shelter' THEN available_beds ELSE 0 END DESC, name ASC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Find Resources - BridgeConnect</title>
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
        <h1>Find Resources</h1>
        <p class="muted">Search and filter resources. Shelter results are prioritized by available beds.</p>
    </div>

    <form method="GET" class="filter-bar">
        <input name="search" placeholder="Search resources..." value="<?php echo htmlspecialchars($search); ?>">
        <select name="type">
            <option value="">All Types</option>
            <option value="shelter" <?php if($type=='shelter') echo 'selected'; ?>>Shelter</option>
            <option value="food" <?php if($type=='food') echo 'selected'; ?>>Food</option>
            <option value="medical" <?php if($type=='medical') echo 'selected'; ?>>Medical</option>
            <option value="hygiene" <?php if($type=='hygiene') echo 'selected'; ?>>Hygiene</option>
            <option value="job_support" <?php if($type=='job_support') echo 'selected'; ?>>Job Support</option>
        </select>
        <button type="submit">Filter</button>
    </form>

    <div class="resource-grid">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="card">
                <span class="pill"><?php echo htmlspecialchars($row['type']); ?></span>
                <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                <p><?php echo htmlspecialchars($row['description']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($row['address']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($row['phone']); ?></p>

                <?php if ($row['type'] == 'shelter'): ?>
                    <p class="beds"><strong>Available Beds:</strong> <?php echo $row['available_beds']; ?></p>
                <?php endif; ?>

                <a class="btn" target="_blank" href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($row['address']); ?>">Get Directions</a>
            </div>
        <?php endwhile; ?>
    </div>
</div>
</body>
</html>
