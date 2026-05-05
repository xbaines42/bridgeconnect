<?php
require_once "auth.php";
require_once "db.php";
requireRole(['shelter_provider']);

$user_id = intval($_SESSION['user_id']);
$message = '';
$error   = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name           = $_POST['name'];
        $description    = $_POST['description'];
        $address        = $_POST['address'];
        $phone          = $_POST['phone'];
        $link           = $_POST['link'] ?? '';
        $volunteer_link = $_POST['volunteer_link'] ?? '';
        $available_beds = intval($_POST['available_beds']);
        $stmt = $conn->prepare("INSERT INTO resources (name, type, description, address, phone, link, volunteer_link, available_beds, updated_by, owned_by) VALUES (?, 'shelter', ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssiii", $name, $description, $address, $phone, $link, $volunteer_link, $available_beds, $user_id, $user_id);
        $stmt->execute();
        $message = "Shelter added successfully!";
    }

    if ($action === 'update') {
        $resource_id    = intval($_POST['resource_id']);
        $name           = $_POST['name'];
        $description    = $_POST['description'];
        $address        = $_POST['address'];
        $phone          = $_POST['phone'];
        $link           = $_POST['link'] ?? '';
        $volunteer_link = $_POST['volunteer_link'] ?? '';
        $available_beds = intval($_POST['available_beds']);
        $stmt = $conn->prepare("UPDATE resources SET name=?, description=?, address=?, phone=?, link=?, volunteer_link=?, available_beds=?, updated_by=? WHERE id=? AND owned_by=? AND type='shelter'");
        $stmt->bind_param("ssssssiiii", $name, $description, $address, $phone, $link, $volunteer_link, $available_beds, $user_id, $resource_id, $user_id);
        $stmt->execute();
        $message = $stmt->affected_rows > 0 ? "Shelter updated!" : "";
        $error   = $stmt->affected_rows > 0 ? "" : "You can only edit shelters you own.";
    }

    if ($action === 'delete') {
        $resource_id = intval($_POST['resource_id']);
        $stmt = $conn->prepare("DELETE FROM resources WHERE id=? AND owned_by=? AND type='shelter'");
        $stmt->bind_param("ii", $resource_id, $user_id);
        $stmt->execute();
        $message = $stmt->affected_rows > 0 ? "Shelter removed." : "";
        $error   = $stmt->affected_rows > 0 ? "" : "You can only delete shelters you own.";
    }
}

$my_stmt = $conn->prepare("SELECT * FROM resources WHERE type='shelter' AND owned_by=? ORDER BY name ASC");
$my_stmt->bind_param("i", $user_id); $my_stmt->execute();
$my_result = $my_stmt->get_result();

$other_stmt = $conn->prepare("SELECT r.*, u.name as provider_name FROM resources r LEFT JOIN users u ON r.owned_by = u.id WHERE r.type='shelter' AND (r.owned_by != ? OR r.owned_by IS NULL) ORDER BY r.name ASC");
$other_stmt->bind_param("i", $user_id); $other_stmt->execute();
$other_result = $other_stmt->get_result();

$first_name = explode(' ', $_SESSION['name'])[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Shelter Provider — BridgeConnect</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,600;1,400&family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { lora:['Lora','serif'], nunito:['Nunito','sans-serif'] } } } }
    </script>
    <style>
        body { font-family: 'Nunito', sans-serif; background: #fdf8f3; }
        .topbar { background: linear-gradient(135deg, #1a3d2b, #2d6a4f); }
        .input-field {
            width: 100%; padding: 11px 14px; border: 2px solid #d1fae5; border-radius: 10px;
            font-size: 14px; font-family: 'Nunito', sans-serif; background: #f8fffe;
            color: #1a3d2b; outline: none; display: block; margin-bottom: 14px;
            transition: border-color 0.2s, box-shadow 0.2s; box-sizing: border-box;
        }
        .input-field:focus { border-color: #52b788; box-shadow: 0 0 0 3px rgba(82,183,136,0.15); background: white; }
        .form-label { display: block; font-size: 12px; font-weight: 800; color: #1a3d2b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px; }
        .submit-btn {
            width: 100%; padding: 12px; background: linear-gradient(135deg, #1a3d2b, #52b788);
            color: white; border: none; border-radius: 10px; font-size: 15px; font-weight: 800;
            font-family: 'Nunito', sans-serif; cursor: pointer; transition: transform 0.15s, box-shadow 0.15s;
            box-shadow: 0 4px 14px rgba(45,106,79,0.3); margin-top: 4px;
        }
        .submit-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(45,106,79,0.4); }
        .card { background: white; border-radius: 18px; padding: 22px; border: 1px solid #e8f5ee; box-shadow: 0 4px 16px rgba(45,106,79,0.07); }
        .fade-in { animation: fadeIn 0.4s ease both; }
        @keyframes fadeIn { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }
    </style>
</head>
<body class="min-h-screen">

<div class="topbar text-white px-8 py-4 flex justify-between items-center shadow-lg">
    <span class="font-lora text-xl font-semibold">🌿 BridgeConnect</span>
    <div class="flex items-center gap-4">
        <span class="text-sm" style="opacity:0.85;"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
        <a href="logout.php" class="text-sm font-bold px-4 py-2 rounded-lg"
           style="background:rgba(255,255,255,0.15); color:white; text-decoration:none;"
           onmouseover="this.style.background='rgba(255,255,255,0.25)'"
           onmouseout="this.style.background='rgba(255,255,255,0.15)'">Logout</a>
    </div>
</div>

<div class="max-w-6xl mx-auto px-6 py-8 fade-in">

    <!-- Banner -->
    <div class="rounded-2xl p-6 mb-8 flex items-center gap-5 text-white"
         style="background:linear-gradient(135deg,#1a3d2b,#2d6a4f); box-shadow:0 6px 24px rgba(45,106,79,0.25);">
        <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl flex-shrink-0"
             style="background:rgba(255,255,255,0.15);">🏠</div>
        <div>
            <h2 class="font-lora text-xl font-semibold mb-1">Welcome, <?php echo htmlspecialchars($first_name); ?>!</h2>
            <p class="text-sm" style="opacity:0.8;">Add and manage your shelters. Your updates show in real time for people seeking help tonight.</p>
        </div>
    </div>

    <?php if (!empty($message)): ?>
        <div class="rounded-xl px-4 py-3 text-sm font-bold mb-6" style="background:#f0fdf4; border:1px solid #bbf7d0; color:#166534;">✅ <?php echo $message; ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="rounded-xl px-4 py-3 text-sm font-bold mb-6" style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b;">❌ <?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Add Shelter Form -->
    <div class="card mb-10">
        <h2 class="font-lora text-2xl font-semibold mb-6" style="color:#1a3d2b;">➕ Add Your Shelter</h2>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
                <div>
                    <label class="form-label">Shelter Name</label>
                    <input class="input-field" name="name" placeholder="e.g. Hope Shelter" required>
                </div>
                <div>
                    <label class="form-label">Phone</label>
                    <input class="input-field" name="phone" placeholder="555-000-0000">
                </div>
                <div class="md:col-span-2">
                    <label class="form-label">Address</label>
                    <input class="input-field" name="address" placeholder="123 Main St, Baltimore, MD">
                </div>
                <div class="md:col-span-2">
                    <label class="form-label">Description</label>
                    <textarea class="input-field" name="description" rows="3" style="min-height:80px; resize:vertical;"
                        placeholder="Who is this shelter for? What services do you offer?"></textarea>
                </div>
                <div>
                    <label class="form-label">Website Link <span class="normal-case font-normal text-gray-400">(optional)</span></label>
                    <input class="input-field" name="link" placeholder="https://www.yourshelter.org">
                </div>
                <div>
                    <label class="form-label">Volunteer Signup Link <span class="normal-case font-normal text-gray-400">(optional)</span></label>
                    <input class="input-field" name="volunteer_link" placeholder="https://www.yourshelter.org/volunteer">
                </div>
                <div>
                    <label class="form-label">Available Beds</label>
                    <input class="input-field" type="number" name="available_beds" value="0" min="0">
                </div>
            </div>
            <button class="submit-btn" type="submit">Add Shelter</button>
        </form>
    </div>

    <!-- My Shelters -->
    <h2 class="font-lora text-2xl font-semibold mb-4" style="color:#1a3d2b;">Your Shelters</h2>

    <?php if ($my_result->num_rows === 0): ?>
        <div class="card text-center mb-10" style="color:#6b7280;">
            <p class="text-4xl mb-3">🏠</p>
            <p>You haven't added any shelters yet. Use the form above!</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-10">
            <?php while ($row = $my_result->fetch_assoc()): ?>
                <div class="card" style="border:2px solid #52b788;">
                    <span class="inline-block text-xs font-extrabold px-3 py-1 rounded-full mb-3 bg-green-100 text-green-700">✅ Your Shelter</span>
                    <h3 class="font-lora text-lg font-semibold mb-2" style="color:#1a3d2b;"><?php echo htmlspecialchars($row['name']); ?></h3>
                    <p class="text-sm mb-3" style="color:#6b7280;"><?php echo htmlspecialchars($row['description']); ?></p>
                    <p class="text-sm mb-1" style="color:#374151;">📍 <strong><?php echo htmlspecialchars($row['address']); ?></strong></p>
                    <p class="text-sm mb-3" style="color:#374151;">📞 <?php echo htmlspecialchars($row['phone']); ?></p>
                    <div class="rounded-xl px-4 py-2 mb-4 text-sm font-bold" style="background:#f0fdf4; border:1px solid #bbf7d0; color:#166534;">
                        🛏️ <strong><?php echo $row['available_beds']; ?></strong> beds available
                    </div>

                    <button type="button" onclick="toggleEdit('edit-<?php echo $row['id']; ?>')"
                            class="w-full py-2 text-sm font-bold rounded-xl mb-2 transition-all"
                            style="background:linear-gradient(135deg,#1f4f93,#2d6a4f); color:white; border:none; cursor:pointer; font-family:'Nunito',sans-serif;">
                        ✏️ Edit Shelter
                    </button>

                    <div id="edit-<?php echo $row['id']; ?>" style="display:none; margin-top:14px; border-top:1px solid #e8f5ee; padding-top:14px;">
                        <form method="POST">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="resource_id" value="<?php echo $row['id']; ?>">
                            <label class="form-label">Name</label>
                            <input class="input-field" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
                            <label class="form-label">Description</label>
                            <textarea class="input-field" name="description" rows="2" style="min-height:70px; resize:vertical;"><?php echo htmlspecialchars($row['description']); ?></textarea>
                            <label class="form-label">Address</label>
                            <input class="input-field" name="address" value="<?php echo htmlspecialchars($row['address']); ?>">
                            <label class="form-label">Phone</label>
                            <input class="input-field" name="phone" value="<?php echo htmlspecialchars($row['phone']); ?>">
                            <label class="form-label">Website Link</label>
                            <input class="input-field" name="link" value="<?php echo htmlspecialchars($row['link'] ?? ''); ?>">
                            <label class="form-label">Volunteer Link</label>
                            <input class="input-field" name="volunteer_link" value="<?php echo htmlspecialchars($row['volunteer_link'] ?? ''); ?>">
                            <label class="form-label">Available Beds</label>
                            <input class="input-field" type="number" name="available_beds" value="<?php echo $row['available_beds']; ?>" min="0">
                            <button class="submit-btn" type="submit">💾 Save Changes</button>
                        </form>
                        <form method="POST" style="margin-top:8px;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="resource_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" onclick="return confirm('Delete this shelter?')"
                                    class="w-full py-2 text-sm font-bold rounded-xl transition-all"
                                    style="background:linear-gradient(135deg,#dc2626,#ef4444); color:white; border:none; cursor:pointer; font-family:'Nunito',sans-serif; margin-top:0;">
                                🗑️ Delete Shelter
                            </button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>

    <!-- Other Shelters -->
    <?php if ($other_result->num_rows > 0): ?>
        <h2 class="font-lora text-2xl font-semibold mb-4" style="color:#1a3d2b;">
            Other Shelters <span class="text-base font-normal" style="color:#9ca3af;">(read only)</span>
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <?php while ($row = $other_result->fetch_assoc()): ?>
                <div class="card" style="opacity:0.75;">
                    <span class="inline-block text-xs font-extrabold px-3 py-1 rounded-full mb-3 bg-blue-100 text-blue-700">🏠 shelter</span>
                    <h3 class="font-lora text-lg font-semibold mb-2" style="color:#1a3d2b;"><?php echo htmlspecialchars($row['name']); ?></h3>
                    <p class="text-sm mb-3" style="color:#6b7280;"><?php echo htmlspecialchars($row['description']); ?></p>
                    <p class="text-sm mb-1" style="color:#374151;">📍 <strong><?php echo htmlspecialchars($row['address']); ?></strong></p>
                    <p class="text-sm mb-3" style="color:#374151;">📞 <?php echo htmlspecialchars($row['phone']); ?></p>
                    <div class="rounded-xl px-4 py-2 text-sm font-bold" style="background:#f0fdf4; border:1px solid #bbf7d0; color:#166534;">
                        🛏️ <strong><?php echo $row['available_beds']; ?></strong> beds available
                    </div>
                    <?php if (!empty($row['provider_name'])): ?>
                        <p class="text-xs mt-3" style="color:#9ca3af;">Managed by <?php echo htmlspecialchars($row['provider_name']); ?></p>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function toggleEdit(id) {
    const el = document.getElementById(id);
    el.style.display = el.style.display === 'block' ? 'none' : 'block';
}
</script>
</body>
</html>