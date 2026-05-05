<?php
require_once "auth.php";
require_once "db.php";
requireRole(['volunteer']);

$user_id = intval($_SESSION['user_id']);
$message = '';
$error   = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $resource_id = intval($_POST['resource_id']);
    $action      = $_POST['action'] ?? '';

    if ($action === 'signup') {
        $stmt = $conn->prepare("INSERT IGNORE INTO volunteer_signups (user_id, resource_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $resource_id);
        $stmt->execute();
        $message = "You've signed up to volunteer here!";
    } elseif ($action === 'cancel') {
        $stmt = $conn->prepare("DELETE FROM volunteer_signups WHERE user_id=? AND resource_id=?");
        $stmt->bind_param("ii", $user_id, $resource_id);
        $stmt->execute();
        $message = "Your volunteer signup has been cancelled.";
    }
}

$resources = $conn->query("SELECT * FROM resources ORDER BY type, name");

$my_signups = [];
$signup_result = $conn->prepare("SELECT resource_id FROM volunteer_signups WHERE user_id=?");
$signup_result->bind_param("i", $user_id);
$signup_result->execute();
$rows = $signup_result->get_result();
while ($row = $rows->fetch_assoc()) {
    $my_signups[] = $row['resource_id'];
}

$volunteer_counts = [];
$count_result = $conn->query("SELECT resource_id, COUNT(*) as total FROM volunteer_signups GROUP BY resource_id");
while ($row = $count_result->fetch_assoc()) {
    $volunteer_counts[$row['resource_id']] = $row['total'];
}

$first_name  = explode(' ', $_SESSION['name'])[0];
$icons       = ['shelter'=>'🏠','food'=>'🍽️','medical'=>'🩺','hygiene'=>'🚿','job_support'=>'💼','other'=>'📌'];
$pill_colors = ['shelter'=>'bg-blue-100 text-blue-700','food'=>'bg-yellow-100 text-yellow-700','medical'=>'bg-pink-100 text-pink-700','hygiene'=>'bg-purple-100 text-purple-700','job_support'=>'bg-green-100 text-green-700','other'=>'bg-gray-100 text-gray-600'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Volunteer Dashboard — BridgeConnect</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,600;1,400&family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>tailwind.config={theme:{extend:{fontFamily:{lora:['Lora','serif'],nunito:['Nunito','sans-serif']}}}}</script>
    <style>
        body { font-family: 'Nunito', sans-serif; background: #fdf8f3; }
        .topbar { background: linear-gradient(135deg, #1a3d2b, #2d6a4f); }
        .card { background: white; border-radius: 18px; padding: 22px; border: 1px solid #e8f5ee; box-shadow: 0 4px 16px rgba(45,106,79,0.07); transition: transform 0.2s, box-shadow 0.2s; }
        .card:hover { transform: translateY(-3px); box-shadow: 0 10px 28px rgba(45,106,79,0.12); }
        .signup-btn {
            display: block; width: 100%; padding: 11px; text-align: center;
            background: linear-gradient(135deg, #1a3d2b, #52b788);
            color: white; border: none; border-radius: 10px;
            font-size: 14px; font-weight: 800; font-family: 'Nunito', sans-serif;
            cursor: pointer; transition: transform 0.15s, box-shadow 0.15s;
            box-shadow: 0 3px 12px rgba(45,106,79,0.3); text-decoration: none;
            margin-top: 14px;
        }
        .signup-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(45,106,79,0.4); }
        .cancel-btn {
            display: block; width: 100%; padding: 11px; text-align: center;
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: white; border: none; border-radius: 10px;
            font-size: 14px; font-weight: 800; font-family: 'Nunito', sans-serif;
            cursor: pointer; transition: transform 0.15s;
            box-shadow: 0 3px 12px rgba(220,38,38,0.25); margin-top: 14px;
        }
        .cancel-btn:hover { transform: translateY(-1px); }
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
           style="background:rgba(255,255,255,0.15);color:white;text-decoration:none;"
           onmouseover="this.style.background='rgba(255,255,255,0.25)'"
           onmouseout="this.style.background='rgba(255,255,255,0.15)'">Logout</a>
    </div>
</div>

<div class="max-w-6xl mx-auto px-6 py-8 fade-in">

    <!-- Banner -->
    <div class="rounded-2xl p-6 mb-8 flex items-center gap-5 text-white"
         style="background:linear-gradient(135deg,#1a3d2b,#2d6a4f);box-shadow:0 6px 24px rgba(45,106,79,0.25);">
        <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl flex-shrink-0"
             style="background:rgba(255,255,255,0.15);">🤝</div>
        <div>
            <h2 class="font-lora text-xl font-semibold mb-1">Welcome, <?php echo htmlspecialchars($first_name); ?>!</h2>
            <p class="text-sm" style="opacity:0.8;">Browse resources below and sign up to volunteer where you can make a difference.</p>
        </div>
    </div>

    <?php if (!empty($message)): ?>
        <div class="rounded-xl px-4 py-3 text-sm font-bold mb-6" style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;">✅ <?php echo $message; ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="rounded-xl px-4 py-3 text-sm font-bold mb-6" style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;">❌ <?php echo $error; ?></div>
    <?php endif; ?>

    <!-- My signups summary -->
    <?php if (!empty($my_signups)): ?>
        <div class="rounded-2xl px-6 py-4 mb-8 flex items-center gap-4" style="background:#f0fdf4;border:1px solid #bbf7d0;">
            <span class="text-2xl">✅</span>
            <div>
                <p class="font-lora font-semibold text-base mb-0" style="color:#166534;">
                    You're signed up at <?php echo count($my_signups); ?> resource<?php echo count($my_signups)>1?'s':''; ?>
                </p>
                <p class="text-sm" style="color:#166534;opacity:0.8;">Scroll down to see your signups highlighted in green.</p>
            </div>
        </div>
    <?php endif; ?>

    <div class="mb-6">
        <h1 class="font-lora text-3xl font-semibold mb-1" style="color:#1a3d2b;">Available Resources</h1>
        <p class="text-sm" style="color:#6b7280;">Sign up to volunteer at any resource. You can cancel anytime.</p>
    </div>

    <!-- Resource Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <?php while ($row = $resources->fetch_assoc()):
            $icon         = $icons[$row['type']] ?? '📌';
            $pc           = $pill_colors[$row['type']] ?? 'bg-gray-100 text-gray-600';
            $is_signed_up = in_array($row['id'], $my_signups);
            $vol_count    = $volunteer_counts[$row['id']] ?? 0;
        ?>
            <div class="card <?php echo $is_signed_up ? 'border-2 border-green-400' : ''; ?>"
                 style="<?php echo $is_signed_up ? 'background:#f0fdf4;' : ''; ?>">

                <?php if ($is_signed_up): ?>
                    <span class="inline-block text-xs font-extrabold px-3 py-1 rounded-full mb-3 bg-green-100 text-green-700">
                        ✅ You're volunteering here
                    </span><br>
                <?php endif; ?>

                <span class="inline-block text-xs font-extrabold px-3 py-1 rounded-full mb-3 <?php echo $pc; ?>">
                    <?php echo $icon . ' ' . ucfirst(str_replace('_',' ',$row['type'])); ?>
                </span>

                <h3 class="font-lora text-lg font-semibold mb-2" style="color:#1a3d2b;"><?php echo htmlspecialchars($row['name']); ?></h3>

                <?php if (!empty($row['description'])): ?>
                    <p class="text-sm mb-3 leading-relaxed" style="color:#6b7280;"><?php echo htmlspecialchars($row['description']); ?></p>
                <?php endif; ?>

                <p class="text-sm mb-1" style="color:#374151;">📍 <strong><?php echo htmlspecialchars($row['address']); ?></strong></p>
                <p class="text-sm mb-3" style="color:#374151;">📞 <?php echo htmlspecialchars($row['phone']); ?></p>

                <?php if ($row['type'] === 'shelter'): ?>
                    <div class="rounded-xl px-4 py-2 mb-3 text-sm font-bold" style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;">
                        🛏️ <strong><?php echo $row['available_beds']; ?></strong> beds available
                    </div>
                <?php endif; ?>

                <?php if (!empty($row['link'])): ?>
                    <a href="<?php echo htmlspecialchars($row['link']); ?>" target="_blank"
                       class="inline-block text-sm font-bold mb-2" style="color:#2d6a4f;text-decoration:none;">🌐 Visit Website</a>
                <?php endif; ?>

                <p class="text-xs mb-1" style="color:#9ca3af;">
                    👥 <strong style="color:#374151;"><?php echo $vol_count; ?></strong> volunteer<?php echo $vol_count!==1?'s':''; ?> signed up
                </p>

                <form method="POST">
                    <input type="hidden" name="resource_id" value="<?php echo $row['id']; ?>">
                    <?php if ($is_signed_up): ?>
                        <input type="hidden" name="action" value="cancel">
                        <button type="submit" class="cancel-btn">✕ Cancel Signup</button>
                    <?php elseif (!empty($row['volunteer_link'])): ?>
                        <a href="<?php echo htmlspecialchars($row['volunteer_link']); ?>" target="_blank" class="signup-btn">
                            🙋 Sign Up to Volunteer
                        </a>
                    <?php else: ?>
                        <div class="mt-4 text-center py-3 rounded-xl text-sm font-bold" style="background:#f1f5f9;color:#94a3b8;">
                            🚫 No volunteers needed
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>