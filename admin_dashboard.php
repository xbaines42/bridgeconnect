<?php
require_once "auth.php";
require_once "db.php";
requireRole(['admin']);

$user_id = intval($_SESSION['user_id']);
$message = '';
$error   = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_resource') {
        $name=$_POST['name']; $type=$_POST['type']; $description=$_POST['description'];
        $address=$_POST['address']; $phone=$_POST['phone'];
        $link=$_POST['link']??''; $volunteer_link=$_POST['volunteer_link']??'';
        $available_beds=intval($_POST['available_beds']);
        $stmt=$conn->prepare("INSERT INTO resources (name,type,description,address,phone,link,volunteer_link,available_beds,updated_by) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("sssssssii",$name,$type,$description,$address,$phone,$link,$volunteer_link,$available_beds,$user_id);
        $stmt->execute(); $message="Resource added.";
    }
    if ($action === 'edit_resource') {
        $rid=intval($_POST['resource_id']); $name=$_POST['name']; $type=$_POST['type'];
        $description=$_POST['description']; $address=$_POST['address']; $phone=$_POST['phone'];
        $link=$_POST['link']??''; $volunteer_link=$_POST['volunteer_link']??'';
        $available_beds=intval($_POST['available_beds']);
        $stmt=$conn->prepare("UPDATE resources SET name=?,type=?,description=?,address=?,phone=?,link=?,volunteer_link=?,available_beds=?,updated_by=? WHERE id=?");
        $stmt->bind_param("ssssssssii",$name,$type,$description,$address,$phone,$link,$volunteer_link,$available_beds,$user_id,$rid);
        $stmt->execute(); $message="Resource updated.";
    }
    if ($action === 'delete_resource') {
        $rid=intval($_POST['resource_id']);
        $stmt=$conn->prepare("DELETE FROM resources WHERE id=?");
        $stmt->bind_param("i",$rid); $stmt->execute(); $message="Resource deleted.";
    }
    if ($action === 'change_role') {
        $tid=intval($_POST['target_user_id']); $new_role=$_POST['new_role'];
        $allowed=['person_in_need','volunteer','shelter_provider','admin'];
        if (in_array($new_role,$allowed) && $tid!==$user_id) {
            $stmt=$conn->prepare("UPDATE users SET role=? WHERE id=?");
            $stmt->bind_param("si",$new_role,$tid); $stmt->execute(); $message="Role updated.";
        } else { $error="Cannot change your own role."; }
    }
    if ($action === 'delete_user') {
        $tid=intval($_POST['target_user_id']);
        if ($tid!==$user_id) {
            $stmt=$conn->prepare("DELETE FROM users WHERE id=?");
            $stmt->bind_param("i",$tid); $stmt->execute(); $message="User deleted.";
        } else { $error="Cannot delete your own account."; }
    }
}

$total_users     = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$total_resources = $conn->query("SELECT COUNT(*) as c FROM resources")->fetch_assoc()['c'];
$total_signups   = $conn->query("SELECT COUNT(*) as c FROM volunteer_signups")->fetch_assoc()['c'];
$total_beds      = $conn->query("SELECT SUM(available_beds) as c FROM resources WHERE type='shelter'")->fetch_assoc()['c'] ?? 0;

$resources = $conn->query("SELECT r.*, u.name as owner_name FROM resources r LEFT JOIN users u ON r.owned_by=u.id ORDER BY r.type,r.name");
$users     = $conn->query("SELECT * FROM users ORDER BY role,name");
$signups   = $conn->query("SELECT vs.*,u.name as vname,u.email as vemail,r.name as rname,r.type as rtype FROM volunteer_signups vs JOIN users u ON vs.user_id=u.id JOIN resources r ON vs.resource_id=r.id ORDER BY vs.signed_up_at DESC");

$icons = ['shelter'=>'🏠','food'=>'🍽️','medical'=>'🩺','hygiene'=>'🚿','job_support'=>'💼','other'=>'📌'];
$pill_colors = ['shelter'=>'bg-blue-100 text-blue-700','food'=>'bg-yellow-100 text-yellow-700','medical'=>'bg-pink-100 text-pink-700','hygiene'=>'bg-purple-100 text-purple-700','job_support'=>'bg-green-100 text-green-700','other'=>'bg-gray-100 text-gray-600'];
$role_colors = ['admin'=>'bg-yellow-100 text-yellow-800','person_in_need'=>'bg-blue-100 text-blue-700','volunteer'=>'bg-green-100 text-green-700','shelter_provider'=>'bg-purple-100 text-purple-700'];
$first_name = explode(' ',$_SESSION['name'])[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard — BridgeConnect</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,600;1,400&family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>tailwind.config={theme:{extend:{fontFamily:{lora:['Lora','serif'],nunito:['Nunito','sans-serif']}}}}</script>
    <style>
        body { font-family: 'Nunito', sans-serif; background: #fdf8f3; }
        .topbar { background: linear-gradient(135deg, #1a3d2b, #2d6a4f); }
        .input-field { width:100%; padding:10px 14px; border:2px solid #d1fae5; border-radius:10px; font-size:14px; font-family:'Nunito',sans-serif; background:#f8fffe; color:#1a3d2b; outline:none; display:block; margin-bottom:12px; transition:border-color 0.2s,box-shadow 0.2s; box-sizing:border-box; }
        .input-field:focus { border-color:#52b788; box-shadow:0 0 0 3px rgba(82,183,136,0.15); background:white; }
        .form-label { display:block; font-size:11px; font-weight:800; color:#1a3d2b; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:5px; }
        .submit-btn { width:100%; padding:12px; background:linear-gradient(135deg,#1a3d2b,#52b788); color:white; border:none; border-radius:10px; font-size:15px; font-weight:800; font-family:'Nunito',sans-serif; cursor:pointer; transition:transform 0.15s,box-shadow 0.15s; box-shadow:0 4px 14px rgba(45,106,79,0.3); margin-top:4px; }
        .submit-btn:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(45,106,79,0.4); }
        .tab-btn { padding:10px 20px; border-radius:10px; font-weight:800; font-size:14px; font-family:'Nunito',sans-serif; cursor:pointer; border:2px solid #e8f5ee; background:white; color:#2d6a4f; transition:all 0.15s; }
        .tab-btn.active, .tab-btn:hover { background:linear-gradient(135deg,#2d6a4f,#52b788); color:white; border-color:transparent; }
        .card { background:white; border-radius:18px; padding:22px; border:1px solid #e8f5ee; box-shadow:0 4px 16px rgba(45,106,79,0.07); }
        .fade-in { animation:fadeIn 0.4s ease both; }
        @keyframes fadeIn { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }
        table { width:100%; border-collapse:collapse; background:white; border-radius:16px; overflow:hidden; box-shadow:0 4px 16px rgba(45,106,79,0.08); }
        th { background:linear-gradient(135deg,#2d6a4f,#52b788); color:white; padding:13px 16px; text-align:left; font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:0.04em; }
        td { padding:11px 16px; border-bottom:1px solid #f0fdf4; font-size:14px; vertical-align:middle; }
        tr:last-child td { border-bottom:none; }
        tr:hover td { background:#f8fffe; }
    </style>
</head>
<body class="min-h-screen">

<div class="topbar text-white px-8 py-4 flex justify-between items-center shadow-lg">
    <span class="font-lora text-xl font-semibold">🌿 BridgeConnect</span>
    <div class="flex items-center gap-4">
        <span class="text-sm" style="opacity:0.85;"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
        <a href="logout.php" class="text-sm font-bold px-4 py-2 rounded-lg" style="background:rgba(255,255,255,0.15);color:white;text-decoration:none;" onmouseover="this.style.background='rgba(255,255,255,0.25)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">Logout</a>
    </div>
</div>

<div class="max-w-7xl mx-auto px-6 py-8 fade-in">

    <!-- Banner -->
    <div class="rounded-2xl p-6 mb-8 flex items-center gap-5 text-white" style="background:linear-gradient(135deg,#1a3d2b,#2d6a4f);box-shadow:0 6px 24px rgba(45,106,79,0.25);">
        <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl flex-shrink-0" style="background:rgba(255,255,255,0.15);">🔧</div>
        <div>
            <h2 class="font-lora text-xl font-semibold mb-1">Admin Dashboard</h2>
            <p class="text-sm" style="opacity:0.8;">Full control over BridgeConnect — manage resources, users, and monitor volunteer activity.</p>
        </div>
    </div>

    <?php if (!empty($message)): ?>
        <div class="rounded-xl px-4 py-3 text-sm font-bold mb-6" style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;">✅ <?php echo $message; ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="rounded-xl px-4 py-3 text-sm font-bold mb-6" style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;">❌ <?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <?php
        $stats = [
            ['👥', $total_users,     'Total Users'],
            ['📍', $total_resources, 'Resources'],
            ['🛏️', $total_beds,      'Beds Available'],
            ['🙋', $total_signups,   'Volunteer Signups'],
        ];
        foreach ($stats as [$icon, $val, $label]):
        ?>
        <div class="card text-center">
            <div class="font-lora font-bold mb-1" style="font-size:42px;color:#2d6a4f;line-height:1;"><?php echo $val; ?></div>
            <div class="text-xs font-bold uppercase tracking-wider" style="color:#6b7280;"><?php echo $icon . ' ' . $label; ?></div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Tabs -->
    <div class="flex flex-wrap gap-2 mb-6">
        <button class="tab-btn active" onclick="switchTab('resources',this)">📍 Resources</button>
        <button class="tab-btn" onclick="switchTab('add',this)">➕ Add Resource</button>
        <button class="tab-btn" onclick="switchTab('users',this)">👥 Users</button>
        <button class="tab-btn" onclick="switchTab('volunteers',this)">🙋 Signups</button>
    </div>

    <!-- TAB: Resources -->
    <div id="tab-resources" class="tab-content">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <?php while ($row=$resources->fetch_assoc()):
                $icon=$icons[$row['type']]??'📌';
                $pc=$pill_colors[$row['type']]??'bg-gray-100 text-gray-600';
            ?>
            <div class="card">
                <span class="inline-block text-xs font-extrabold px-3 py-1 rounded-full mb-3 <?php echo $pc; ?>"><?php echo $icon.' '.ucfirst(str_replace('_',' ',$row['type'])); ?></span>
                <h3 class="font-lora text-lg font-semibold mb-1" style="color:#1a3d2b;"><?php echo htmlspecialchars($row['name']); ?></h3>
                <p class="text-sm mb-2" style="color:#6b7280;"><?php echo htmlspecialchars($row['description']); ?></p>
                <p class="text-sm" style="color:#374151;">📍 <?php echo htmlspecialchars($row['address']); ?></p>
                <p class="text-sm mb-2" style="color:#374151;">📞 <?php echo htmlspecialchars($row['phone']); ?></p>
                <?php if ($row['type']=='shelter'): ?>
                    <div class="rounded-xl px-3 py-2 text-sm font-bold mb-3" style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;">🛏️ <strong><?php echo $row['available_beds']; ?></strong> beds</div>
                <?php endif; ?>
                <?php if (!empty($row['owner_name'])): ?>
                    <p class="text-xs mb-3" style="color:#9ca3af;">Managed by <?php echo htmlspecialchars($row['owner_name']); ?></p>
                <?php endif; ?>
                <div class="flex gap-2 mt-2">
                    <button class="flex-1 py-2 text-xs font-bold rounded-xl cursor-pointer" style="background:#dbeafe;color:#1e40af;border:none;font-family:'Nunito',sans-serif;" onclick="toggleEdit('res-<?php echo $row['id']; ?>')">✏️ Edit</button>
                    <form method="POST" class="flex-1">
                        <input type="hidden" name="action" value="delete_resource">
                        <input type="hidden" name="resource_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" onclick="return confirm('Delete this resource?')" class="w-full py-2 text-xs font-bold rounded-xl cursor-pointer" style="background:#fee2e2;color:#991b1b;border:none;font-family:'Nunito',sans-serif;">🗑️ Delete</button>
                    </form>
                </div>
                <div id="res-<?php echo $row['id']; ?>" style="display:none;margin-top:14px;border-top:1px solid #e8f5ee;padding-top:14px;">
                    <form method="POST">
                        <input type="hidden" name="action" value="edit_resource">
                        <input type="hidden" name="resource_id" value="<?php echo $row['id']; ?>">
                        <label class="form-label">Name</label><input class="input-field" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
                        <label class="form-label">Type</label>
                        <select class="input-field" name="type">
                            <?php foreach(['shelter','food','medical','hygiene','job_support','other'] as $t): ?>
                                <option value="<?php echo $t; ?>" <?php if($row['type']==$t) echo 'selected'; ?>><?php echo $icons[$t].' '.ucfirst(str_replace('_',' ',$t)); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label class="form-label">Description</label><textarea class="input-field" name="description" rows="2" style="min-height:65px;resize:vertical;"><?php echo htmlspecialchars($row['description']); ?></textarea>
                        <label class="form-label">Address</label><input class="input-field" name="address" value="<?php echo htmlspecialchars($row['address']); ?>">
                        <label class="form-label">Phone</label><input class="input-field" name="phone" value="<?php echo htmlspecialchars($row['phone']); ?>">
                        <label class="form-label">Website Link</label><input class="input-field" name="link" value="<?php echo htmlspecialchars($row['link']??''); ?>">
                        <label class="form-label">Volunteer Link</label><input class="input-field" name="volunteer_link" value="<?php echo htmlspecialchars($row['volunteer_link']??''); ?>">
                        <label class="form-label">Available Beds</label><input class="input-field" type="number" name="available_beds" value="<?php echo $row['available_beds']; ?>" min="0">
                        <button class="submit-btn" type="submit">💾 Save Changes</button>
                    </form>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- TAB: Add Resource -->
    <div id="tab-add" class="tab-content" style="display:none;">
        <div class="card max-w-2xl">
            <h2 class="font-lora text-2xl font-semibold mb-6" style="color:#1a3d2b;">➕ Add New Resource</h2>
            <form method="POST">
                <input type="hidden" name="action" value="add_resource">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-5">
                    <div><label class="form-label">Name</label><input class="input-field" name="name" placeholder="e.g. Hope Shelter" required></div>
                    <div><label class="form-label">Type</label>
                        <select class="input-field" name="type" required>
                            <option value="shelter">🏠 Shelter</option><option value="food">🍽️ Food</option>
                            <option value="medical">🩺 Medical</option><option value="hygiene">🚿 Hygiene</option>
                            <option value="job_support">💼 Job Support</option><option value="other">📌 Other</option>
                        </select>
                    </div>
                    <div class="md:col-span-2"><label class="form-label">Description</label><textarea class="input-field" name="description" rows="3" style="min-height:80px;resize:vertical;" placeholder="What does this resource offer?"></textarea></div>
                    <div><label class="form-label">Address</label><input class="input-field" name="address" placeholder="123 Main St, Baltimore, MD"></div>
                    <div><label class="form-label">Phone</label><input class="input-field" name="phone" placeholder="555-000-0000"></div>
                    <div><label class="form-label">Website Link</label><input class="input-field" name="link" placeholder="https://www.example.org"></div>
                    <div><label class="form-label">Volunteer Link</label><input class="input-field" name="volunteer_link" placeholder="https://www.example.org/volunteer"></div>
                    <div><label class="form-label">Available Beds</label><input class="input-field" type="number" name="available_beds" value="0" min="0"></div>
                </div>
                <button class="submit-btn" type="submit">Add Resource</button>
            </form>
        </div>
    </div>

    <!-- TAB: Users -->
    <div id="tab-users" class="tab-content" style="display:none;">
        <div style="overflow-x:auto;">
            <table>
                <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Gender</th><th>About</th><th>Actions</th></tr></thead>
                <tbody>
                <?php while ($u=$users->fetch_assoc()):
                    $rc=$role_colors[$u['role']]??'bg-gray-100 text-gray-600';
                ?>
                    <tr>
                        <td class="font-bold" style="color:#1a3d2b;"><?php echo htmlspecialchars($u['name']); ?></td>
                        <td style="color:#6b7280;"><?php echo htmlspecialchars($u['email']); ?></td>
                        <td><span class="inline-block text-xs font-extrabold px-3 py-1 rounded-full <?php echo $rc; ?>"><?php echo str_replace('_',' ',$u['role']); ?></span></td>
                        <td class="text-sm" style="color:#6b7280;"><?php echo str_replace('_',' ',$u['gender']??'—'); ?></td>
                        <td class="text-sm" style="color:#6b7280;max-width:180px;"><?php echo !empty($u['about'])?htmlspecialchars(substr($u['about'],0,55)).(strlen($u['about'])>55?'...':''):'—'; ?></td>
                        <td>
                            <?php if ($u['id']!==$user_id): ?>
                                <form method="POST" style="display:inline-flex;gap:4px;align-items:center;flex-wrap:wrap;">
                                    <input type="hidden" name="action" value="change_role">
                                    <input type="hidden" name="target_user_id" value="<?php echo $u['id']; ?>">
                                    <select name="new_role" style="padding:5px 8px;font-size:12px;margin:0;width:auto;border-radius:8px;border:1px solid #d1fae5;font-family:'Nunito',sans-serif;">
                                        <?php foreach(['person_in_need','volunteer','shelter_provider','admin'] as $r): ?>
                                            <option value="<?php echo $r; ?>" <?php if($u['role']==$r) echo 'selected'; ?>><?php echo str_replace('_',' ',$r); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" style="padding:5px 10px;font-size:12px;background:#dbeafe;color:#1e40af;border:none;border-radius:8px;font-weight:800;cursor:pointer;font-family:'Nunito',sans-serif;">Save</button>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="delete_user">
                                    <input type="hidden" name="target_user_id" value="<?php echo $u['id']; ?>">
                                    <button type="submit" onclick="return confirm('Delete this user?')" style="padding:5px 10px;font-size:12px;background:#fee2e2;color:#991b1b;border:none;border-radius:8px;font-weight:800;cursor:pointer;font-family:'Nunito',sans-serif;margin-left:4px;">🗑️</button>
                                </form>
                            <?php else: ?>
                                <span class="text-xs" style="color:#9ca3af;">That's you</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB: Volunteer Signups -->
    <div id="tab-volunteers" class="tab-content" style="display:none;">
        <?php if ($total_signups==0): ?>
            <div class="card text-center" style="color:#6b7280;"><p class="text-4xl mb-3">🙋</p><p>No volunteer signups yet.</p></div>
        <?php else: ?>
            <div style="overflow-x:auto;">
                <table>
                    <thead><tr><th>Volunteer</th><th>Email</th><th>Resource</th><th>Type</th><th>Signed Up</th></tr></thead>
                    <tbody>
                    <?php while ($s=$signups->fetch_assoc()):
                        $icon=$icons[$s['rtype']]??'📌';
                        $pc=$pill_colors[$s['rtype']]??'bg-gray-100 text-gray-600';
                    ?>
                        <tr>
                            <td class="font-bold" style="color:#1a3d2b;"><?php echo htmlspecialchars($s['vname']); ?></td>
                            <td style="color:#6b7280;"><?php echo htmlspecialchars($s['vemail']); ?></td>
                            <td><?php echo htmlspecialchars($s['rname']); ?></td>
                            <td><span class="inline-block text-xs font-extrabold px-3 py-1 rounded-full <?php echo $pc; ?>"><?php echo $icon.' '.$s['rtype']; ?></span></td>
                            <td class="text-sm" style="color:#6b7280;"><?php echo date('M j, Y g:i A',strtotime($s['signed_up_at'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

</div>

<script>
function switchTab(name, btn) {
    document.querySelectorAll('.tab-content').forEach(t => t.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + name).style.display = 'block';
    btn.classList.add('active');
}
function toggleEdit(id) {
    const el = document.getElementById(id);
    el.style.display = el.style.display === 'block' ? 'none' : 'block';
}
</script>
</body>
</html>