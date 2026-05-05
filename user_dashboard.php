<?php
require_once "auth.php";
require_once "db.php";
requireRole(['person_in_need']);

$type   = $_GET['type'] ?? '';
$search = $_GET['search'] ?? '';

$query  = "SELECT * FROM resources WHERE 1=1";
$params = [];
$types  = "";

if ($type != '') {
    $query   .= " AND type=?";
    $params[] = $type;
    $types   .= "s";
}
if ($search != '') {
    $query   .= " AND (name LIKE ? OR description LIKE ? OR address LIKE ?)";
    $like     = "%$search%";
    $params[] = $like; $params[] = $like; $params[] = $like;
    $types   .= "sss";
}
$query .= " ORDER BY CASE WHEN type='shelter' THEN available_beds ELSE 0 END DESC, name ASC";

$stmt = $conn->prepare($query);
if (!empty($params)) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$first_name = explode(' ', $_SESSION['name'])[0];

$pill_colors = [
    'shelter'     => 'bg-blue-100 text-blue-700',
    'food'        => 'bg-yellow-100 text-yellow-700',
    'medical'     => 'bg-pink-100 text-pink-700',
    'hygiene'     => 'bg-purple-100 text-purple-700',
    'job_support' => 'bg-green-100 text-green-700',
    'other'       => 'bg-gray-100 text-gray-600',
];
$icons = ['shelter'=>'🏠','food'=>'🍽️','medical'=>'🩺','hygiene'=>'🚿','job_support'=>'💼','other'=>'📌'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Find Resources — BridgeConnect</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,600;1,400&family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { lora: ['Lora','serif'], nunito: ['Nunito','sans-serif'] } } }
        }
    </script>
    <style>
        body { font-family: 'Nunito', sans-serif; background: #fdf8f3; }
        .topbar { background: linear-gradient(135deg, #1a3d2b, #2d6a4f); }
        .input-field {
            width: 100%; padding: 11px 14px; border: 2px solid #d1fae5;
            border-radius: 10px; font-size: 14px; font-family: 'Nunito', sans-serif;
            background: #f8fffe; color: #1a3d2b; outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .input-field:focus { border-color: #52b788; box-shadow: 0 0 0 3px rgba(82,183,136,0.15); background: white; }
        .filter-btn {
            padding: 11px 20px; background: linear-gradient(135deg, #2d6a4f, #52b788);
            color: white; border: none; border-radius: 10px; font-weight: 800;
            font-family: 'Nunito', sans-serif; cursor: pointer; white-space: nowrap;
            transition: transform 0.15s, box-shadow 0.15s;
            box-shadow: 0 4px 12px rgba(45,106,79,0.3);
        }
        .filter-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(45,106,79,0.4); }
        .card { background: white; border-radius: 18px; padding: 22px; border: 1px solid #e8f5ee; box-shadow: 0 4px 16px rgba(45,106,79,0.07); transition: transform 0.2s, box-shadow 0.2s; }
        .card:hover { transform: translateY(-3px); box-shadow: 0 10px 28px rgba(45,106,79,0.12); }
        .dir-btn {
            display: inline-block; padding: 9px 14px; font-size: 13px; font-weight: 800;
            background: linear-gradient(135deg, #2d6a4f, #52b788); color: white;
            border-radius: 10px; text-decoration: none; transition: transform 0.15s;
            box-shadow: 0 3px 10px rgba(45,106,79,0.25);
        }
        .dir-btn:hover { transform: translateY(-1px); }
        .web-btn {
            display: inline-block; padding: 9px 14px; font-size: 13px; font-weight: 800;
            background: linear-gradient(135deg, #1a3d2b, #2d6a4f); color: white;
            border-radius: 10px; text-decoration: none; margin-left: 6px; transition: transform 0.15s;
            box-shadow: 0 3px 10px rgba(26,61,43,0.25);
        }
        .web-btn:hover { transform: translateY(-1px); }
        @keyframes blink { 0%,100%{opacity:0.2} 50%{opacity:1} }
        .fade-in { animation: fadeIn 0.4s ease both; }
        @keyframes fadeIn { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }
    </style>
</head>
<body class="min-h-screen">

<!-- Topbar -->
<div class="topbar text-white px-8 py-4 flex justify-between items-center shadow-lg">
    <span class="font-lora text-xl font-semibold">🌿 BridgeConnect</span>
    <div class="flex items-center gap-4">
        <span class="text-sm font-nunito" style="opacity:0.85;"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
        <a href="logout.php" class="text-sm font-bold font-nunito px-4 py-2 rounded-lg transition-all"
           style="background:rgba(255,255,255,0.15); color:white; text-decoration:none;"
           onmouseover="this.style.background='rgba(255,255,255,0.25)'"
           onmouseout="this.style.background='rgba(255,255,255,0.15)'">Logout</a>
    </div>
</div>

<div class="max-w-6xl mx-auto px-6 py-8 fade-in">

    <!-- Welcome Banner -->
    <div class="rounded-2xl p-6 mb-8 flex items-center gap-5 text-white"
         style="background: linear-gradient(135deg, #1a3d2b, #2d6a4f); box-shadow: 0 6px 24px rgba(45,106,79,0.25);">
        <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl flex-shrink-0"
             style="background:rgba(255,255,255,0.15);">👋</div>
        <div>
            <h2 class="font-lora text-xl font-semibold mb-1">Hi, <?php echo htmlspecialchars($first_name); ?>!</h2>
            <p class="text-sm font-nunito" style="opacity:0.8;">Here are the resources available to help you today. Use the chatbot 💬 for personalized assistance.</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl p-4 mb-8 flex flex-wrap gap-3 items-center"
         style="border:1px solid #e8f5ee; box-shadow:0 4px 16px rgba(45,106,79,0.06);">
        <form method="GET" class="flex flex-wrap gap-3 w-full items-center">
            <input class="input-field flex-1 min-w-48" name="search"
                   placeholder="🔍 Search resources..."
                   value="<?php echo htmlspecialchars($search); ?>">
            <select class="input-field" name="type" style="width:auto; flex-shrink:0;">
                <option value="">All Types</option>
                <option value="shelter"     <?php if($type=='shelter')     echo 'selected'; ?>>🏠 Shelter</option>
                <option value="food"        <?php if($type=='food')        echo 'selected'; ?>>🍽️ Food</option>
                <option value="medical"     <?php if($type=='medical')     echo 'selected'; ?>>🩺 Medical</option>
                <option value="hygiene"     <?php if($type=='hygiene')     echo 'selected'; ?>>🚿 Hygiene</option>
                <option value="job_support" <?php if($type=='job_support') echo 'selected'; ?>>💼 Job Support</option>
            </select>
            <button class="filter-btn" type="submit">Filter</button>
            <?php if ($type || $search): ?>
                <a href="user_dashboard.php" class="text-sm font-bold" style="color:#2d6a4f; text-decoration:none;">✕ Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Resource Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <?php while ($row = $result->fetch_assoc()):
            $icon      = $icons[$row['type']] ?? '📌';
            $pill_cls  = $pill_colors[$row['type']] ?? 'bg-gray-100 text-gray-600';
        ?>
            <div class="card">
                <span class="inline-block text-xs font-extrabold px-3 py-1 rounded-full mb-3 <?php echo $pill_cls; ?>">
                    <?php echo $icon . ' ' . ucfirst(str_replace('_',' ',$row['type'])); ?>
                </span>

                <h3 class="font-lora text-lg font-semibold mb-2" style="color:#1a3d2b;">
                    <?php echo htmlspecialchars($row['name']); ?>
                </h3>

                <?php if (!empty($row['description'])): ?>
                    <p class="text-sm mb-3 leading-relaxed" style="color:#6b7280;">
                        <?php echo htmlspecialchars($row['description']); ?>
                    </p>
                <?php endif; ?>

                <div class="space-y-1 mb-3">
                    <p class="text-sm font-nunito" style="color:#374151;">
                        📍 <strong><?php echo htmlspecialchars($row['address']); ?></strong>
                    </p>
                    <p class="text-sm font-nunito" style="color:#374151;">
                        📞 <?php echo htmlspecialchars($row['phone']); ?>
                    </p>
                </div>

                <?php if ($row['type'] == 'shelter'): ?>
                    <div class="rounded-xl px-4 py-2 mb-3 text-sm font-bold"
                         style="background:#f0fdf4; border:1px solid #bbf7d0; color:#166534;">
                        🛏️ <strong><?php echo $row['available_beds']; ?></strong> beds available
                    </div>
                <?php endif; ?>

                <div class="mt-2">
                    <a class="dir-btn" target="_blank"
                       href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($row['address']); ?>">
                        📍 Directions
                    </a>
                    <?php if (!empty($row['link'])): ?>
                        <a class="web-btn" target="_blank" href="<?php echo htmlspecialchars($row['link']); ?>">
                            🌐 Website
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Chatbot Toggle -->
<button id="chat-toggle" style="
    position:fixed; bottom:28px; right:28px; width:58px; height:58px;
    border-radius:50%; background:linear-gradient(135deg,#1a3d2b,#52b788);
    color:white; font-size:26px; border:none; cursor:pointer;
    box-shadow:0 6px 24px rgba(45,106,79,0.45); z-index:1000;
    display:flex; align-items:center; justify-content:center;
    margin:0; padding:0; transition:transform 0.2s ease;
" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">💬</button>

<!-- Chatbot Window -->
<div id="chat-window" style="
    display:none; position:fixed; bottom:100px; right:28px; width:340px;
    max-height:500px; background:white; border-radius:20px;
    box-shadow:0 16px 48px rgba(45,106,79,0.2); border:1px solid #e8f5ee;
    z-index:1000; flex-direction:column; overflow:hidden; font-family:'Nunito',sans-serif;
">
    <div style="background:linear-gradient(135deg,#1a3d2b,#52b788); color:white; padding:16px 18px; display:flex; justify-content:space-between; align-items:center;">
        <div>
            <div style="font-weight:800; font-size:15px;">🌿 BridgeConnect Assistant</div>
            <div style="font-size:12px; opacity:0.8;">Ask about shelters, food & more</div>
        </div>
        <button id="chat-close" style="background:rgba(255,255,255,0.2); border:none; color:white; width:28px; height:28px; border-radius:50%; cursor:pointer; font-size:14px; display:flex; align-items:center; justify-content:center; margin:0; padding:0; box-shadow:none;">✕</button>
    </div>
    <div id="chat-messages" style="flex:1; overflow-y:auto; padding:16px; display:flex; flex-direction:column; gap:10px; max-height:320px; background:#f8fffe;">
        <div style="background:white; border:1px solid #e8f5ee; border-radius:14px 14px 14px 4px; padding:11px 14px; font-size:14px; color:#1a3d2b; max-width:88%; line-height:1.5; box-shadow:0 2px 8px rgba(45,106,79,0.06);">
            👋 Hi <?php echo htmlspecialchars($first_name); ?>! I'm here to help. What do you need today?
        </div>
    </div>
    <div style="padding:12px; border-top:1px solid #e8f5ee; display:flex; gap:8px; background:white;">
        <input id="chat-input" type="text" placeholder="Ask about resources..."
            style="flex:1; padding:10px 14px; border:2px solid #d1fae5; border-radius:10px; font-size:14px; outline:none; margin:0; width:auto; font-family:inherit; background:#f8fffe;">
        <button id="send-btn" style="background:linear-gradient(135deg,#2d6a4f,#52b788); color:white; border:none; border-radius:10px; padding:10px 14px; cursor:pointer; font-size:18px; margin:0; width:auto; box-shadow:none;">➤</button>
    </div>
</div>

<script>
const userContext = {
    name:   <?php echo json_encode($_SESSION['name'] ?? ''); ?>,
    gender: <?php echo json_encode($_SESSION['gender'] ?? ''); ?>,
    about:  <?php echo json_encode($_SESSION['about'] ?? ''); ?>
};

function toggleChat() {
    const win = document.getElementById('chat-window');
    const hidden = win.style.display === 'none' || win.style.display === '';
    win.style.display = hidden ? 'flex' : 'none';
    if (hidden) document.getElementById('chat-input').focus();
}

function addMessage(text, isUser) {
    const c = document.getElementById('chat-messages');
    const m = document.createElement('div');
    m.innerHTML = text.replace(/\*\*(.*?)\*\*/g,'<strong>$1</strong>').replace(/\n- /g,'<br>• ').replace(/\n/g,'<br>');
    m.style.cssText = `padding:11px 14px; border-radius:${isUser?'14px 14px 4px 14px':'14px 14px 14px 4px'}; font-size:14px; max-width:88%; line-height:1.5; box-shadow:0 2px 8px rgba(45,106,79,0.08); align-self:${isUser?'flex-end':'flex-start'}; background:${isUser?'linear-gradient(135deg,#2d6a4f,#52b788)':'white'}; color:${isUser?'white':'#1a3d2b'}; border:${isUser?'none':'1px solid #e8f5ee'};`;
    c.appendChild(m); c.scrollTop = c.scrollHeight;
}

function addTyping() {
    const c = document.getElementById('chat-messages');
    const t = document.createElement('div'); t.id = 'typing';
    t.innerHTML = '<span style="animation:blink 1s infinite">●</span> <span style="animation:blink 1s infinite 0.2s">●</span> <span style="animation:blink 1s infinite 0.4s">●</span>';
    t.style.cssText = 'padding:11px 16px; border-radius:14px 14px 14px 4px; font-size:12px; background:white; border:1px solid #e8f5ee; color:#94a3b8; max-width:80px; letter-spacing:3px;';
    c.appendChild(t); c.scrollTop = c.scrollHeight;
}

async function sendMessage() {
    const input = document.getElementById('chat-input');
    const msg = input.value.trim(); if (!msg) return;
    input.value = ''; addMessage(msg, true); addTyping();
    document.getElementById('send-btn').disabled = true;
    try {
        const r = await fetch('chatbot.php', { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({message:msg, userContext}) });
        const d = await r.json();
        document.getElementById('typing')?.remove();
        addMessage(d.reply || 'Sorry, something went wrong.', false);
    } catch { document.getElementById('typing')?.remove(); addMessage('Could not connect. Please try again.', false); }
    document.getElementById('send-btn').disabled = false;
    document.getElementById('chat-input').focus();
}

document.getElementById('chat-toggle').addEventListener('click', toggleChat);
document.getElementById('chat-close').addEventListener('click', toggleChat);
document.getElementById('send-btn').addEventListener('click', sendMessage);
document.getElementById('chat-input').addEventListener('keydown', e => { if (e.key === 'Enter') sendMessage(); });
</script>
<style>@keyframes blink{0%,100%{opacity:0.2}50%{opacity:1}}</style>
</body>
</html>