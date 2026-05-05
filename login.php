<?php
session_start();
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=? AND password=?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name']    = $user['name'];
        $_SESSION['role']    = $user['role'];
        $_SESSION['gender']  = $user['gender'] ?? '';
        $_SESSION['about']   = $user['about'] ?? '';

        if ($user['role'] == 'person_in_need')       header("Location: user_dashboard.php");
        elseif ($user['role'] == 'shelter_provider') header("Location: provider_dashboard.php");
        elseif ($user['role'] == 'volunteer')        header("Location: volunteer_dashboard.php");
        elseif ($user['role'] == 'admin')            header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login — BridgeConnect</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,600;1,400&family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        lora:   ['Lora', 'serif'],
                        nunito: ['Nunito', 'sans-serif'],
                    },
                    colors: {
                        forest: {
                            50:  '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            600: '#52b788',
                            700: '#2d6a4f',
                            800: '#1a3d2b',
                            900: '#0f2419',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Nunito', sans-serif; }
        .left-panel {
            background: linear-gradient(155deg, #0f2419 0%, #1a3d2b 40%, #2d6a4f 75%, #52b788 100%);
        }
        .input-field {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #d1fae5;
            border-radius: 12px;
            font-size: 15px;
            font-family: 'Nunito', sans-serif;
            background: #f8fffe;
            color: #1a3d2b;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
            box-sizing: border-box;
            display: block;
            margin-bottom: 16px;
        }
        .input-field:focus {
            border-color: #52b788;
            box-shadow: 0 0 0 3px rgba(82,183,136,0.15);
            background: white;
        }
        .submit-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #1a3d2b, #2d6a4f, #52b788);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 800;
            font-family: 'Nunito', sans-serif;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s;
            box-shadow: 0 4px 16px rgba(45,106,79,0.35);
            margin-top: 8px;
        }
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(45,106,79,0.45);
        }
        .demo-pill {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            border: 1px solid #d1fae5;
            border-radius: 10px;
            padding: 10px 14px;
            margin-bottom: 8px;
            font-size: 13px;
            cursor: pointer;
            transition: background 0.15s, border-color 0.15s;
        }
        .demo-pill:hover {
            background: #f0fdf4;
            border-color: #52b788;
        }
        .fade-in {
            animation: fadeIn 0.5s ease both;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="min-h-screen flex bg-[#fdf8f3]">

    <!-- ── LEFT PANEL ── -->
    <div class="left-panel hidden md:flex w-5/12 min-h-screen flex-col justify-between p-12 relative overflow-hidden">
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-white opacity-5 pointer-events-none"></div>
        <div class="absolute -bottom-20 -left-20 w-80 h-80 rounded-full bg-white opacity-5 pointer-events-none"></div>

        <!-- Logo -->
        <a href="index.php" class="font-lora text-white text-xl font-semibold tracking-wide relative z-10" style="text-decoration:none;">
            🌿 BridgeConnect
        </a>

        <!-- Main copy -->
        <div class="relative z-10">
            <h1 class="font-lora text-white text-4xl font-semibold leading-tight mb-5">
                Help is closer<br>than you <em class="opacity-75">think.</em>
            </h1>
            <p class="text-white font-nunito text-base leading-relaxed mb-10" style="opacity:0.65;">
                BridgeConnect links people in need with shelters, food, medical care, and community support — all in one place, updated in real time.
            </p>

            <!-- Stats -->
            <div class="flex gap-8">
                <div class="pl-4" style="border-left: 2px solid rgba(255,255,255,0.2);">
                    <div class="font-lora text-white text-3xl font-semibold">7+</div>
                    <div class="font-nunito font-bold text-xs uppercase tracking-widest mt-1" style="color:rgba(255,255,255,0.45);">Resources</div>
                </div>
                <div class="pl-4" style="border-left: 2px solid rgba(255,255,255,0.2);">
                    <div class="font-lora text-white text-3xl font-semibold">4</div>
                    <div class="font-nunito font-bold text-xs uppercase tracking-widest mt-1" style="color:rgba(255,255,255,0.45);">Roles</div>
                </div>
                <div class="pl-4" style="border-left: 2px solid rgba(255,255,255,0.2);">
                    <div class="font-lora text-white text-3xl font-semibold">24/7</div>
                    <div class="font-nunito font-bold text-xs uppercase tracking-widest mt-1" style="color:rgba(255,255,255,0.45);">Available</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="relative z-10 font-nunito text-xs" style="color:rgba(255,255,255,0.3);">
            © <?php echo date('Y'); ?> BridgeConnect · Baltimore, MD
        </div>
    </div>

    <!-- ── RIGHT PANEL ── -->
    <div class="flex-1 flex flex-col justify-center items-center px-6 py-12 overflow-y-auto">
        <div class="w-full max-w-md fade-in">

            <a href="index.php" class="inline-flex items-center gap-2 text-sm font-bold mb-10 transition-opacity no-underline" style="color:#2d6a4f; opacity:0.6; text-decoration:none;"
               onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.6'">
                ← Back to Home
            </a>

            <div class="mb-8">
                <h2 class="font-lora text-3xl font-semibold mb-2" style="color:#1a3d2b;">Welcome back</h2>
                <p class="text-sm" style="color:#9ca3af;">Sign in to access your dashboard.</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="rounded-xl px-4 py-3 text-sm font-bold mb-6" style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b;">
                    ⚠️ <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <label class="block text-xs font-extrabold uppercase tracking-wider mb-2" style="color:#1a3d2b; letter-spacing:0.06em;">
                    Email Address
                </label>
                <input class="input-field" type="email" name="email" placeholder="you@example.com" required>

                <label class="block text-xs font-extrabold uppercase tracking-wider mb-2" style="color:#1a3d2b; letter-spacing:0.06em;">
                    Password
                </label>
                <input class="input-field" type="password" name="password" placeholder="••••••••" required>

                <button class="submit-btn" type="submit">Sign In →</button>
            </form>

            <p class="text-center text-sm mt-6" style="color:#9ca3af;">
                Don't have an account?
                <a href="signup.php" class="font-bold transition-colors" style="color:#2d6a4f; text-decoration:none;"
                   onmouseover="this.style.color='#1a3d2b'" onmouseout="this.style.color='#2d6a4f'">
                    Create one free
                </a>
            </p>

            <!-- Demo accounts -->
            <div class="mt-8 rounded-2xl p-5" style="background:#f0fdf4; border:1px solid #bbf7d0;">
                <p class="text-xs font-extrabold uppercase tracking-wider mb-4" style="color:#1a3d2b; letter-spacing:0.06em;">
                    🔑 Demo Accounts — click to fill
                </p>

                <div class="demo-pill" onclick="fillLogin('user@test.com','1234')">
                    <div>
                        <span class="font-bold text-sm" style="color:#1a3d2b;">Person in Need</span>
                        <span class="text-xs ml-2" style="color:#9ca3af;">user@test.com</span>
                    </div>
                    <span class="text-xs font-bold px-2 py-1 rounded-full" style="background:#dbeafe; color:#1e40af;">Fill →</span>
                </div>

                <div class="demo-pill" onclick="fillLogin('provider@test.com','1234')">
                    <div>
                        <span class="font-bold text-sm" style="color:#1a3d2b;">Shelter Provider</span>
                        <span class="text-xs ml-2" style="color:#9ca3af;">provider@test.com</span>
                    </div>
                    <span class="text-xs font-bold px-2 py-1 rounded-full" style="background:#ede9fe; color:#5b21b6;">Fill →</span>
                </div>

                <div class="demo-pill" onclick="fillLogin('volunteer@test.com','1234')">
                    <div>
                        <span class="font-bold text-sm" style="color:#1a3d2b;">Volunteer</span>
                        <span class="text-xs ml-2" style="color:#9ca3af;">volunteer@test.com</span>
                    </div>
                    <span class="text-xs font-bold px-2 py-1 rounded-full" style="background:#dcfce7; color:#166534;">Fill →</span>
                </div>

                <div class="demo-pill" onclick="fillLogin('admin@test.com','1234')" style="margin-bottom:0;">
                    <div>
                        <span class="font-bold text-sm" style="color:#1a3d2b;">Admin</span>
                        <span class="text-xs ml-2" style="color:#9ca3af;">admin@test.com</span>
                    </div>
                    <span class="text-xs font-bold px-2 py-1 rounded-full" style="background:#fef3c7; color:#92400e;">Fill →</span>
                </div>
            </div>

        </div>
    </div>

</body>
<script>
function fillLogin(email, password) {
    document.querySelector('input[name="email"]').value = email;
    document.querySelector('input[name="password"]').value = password;
}
</script>
</html>