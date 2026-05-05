<?php
session_start();
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $password = $_POST['password'];
    $role     = $_POST['role'];
    $gender   = $_POST['gender'];
    $about    = $_POST['about'];

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, gender, about) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $email, $password, $role, $gender, $about);

    if ($stmt->execute()) {
        header("Location: login.php");
        exit();
    } else {
        $error = "Could not create account. That email may already be in use.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Create Account — BridgeConnect</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,600;1,400&family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        lora:   ['Lora', 'serif'],
                        nunito: ['Nunito', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Nunito', sans-serif; background: #fdf8f3; }
        .input-field {
            width: 100%; padding: 12px 16px;
            border: 2px solid #d1fae5; border-radius: 12px;
            font-size: 15px; font-family: 'Nunito', sans-serif;
            background: #f8fffe; color: #1a3d2b;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none; box-sizing: border-box; display: block; margin-bottom: 16px;
        }
        .input-field:focus {
            border-color: #52b788;
            box-shadow: 0 0 0 3px rgba(82,183,136,0.15);
            background: white;
        }
        .submit-btn {
            width: 100%; padding: 14px;
            background: linear-gradient(135deg, #1a3d2b, #2d6a4f, #52b788);
            color: white; border: none; border-radius: 12px;
            font-size: 16px; font-weight: 800; font-family: 'Nunito', sans-serif;
            cursor: pointer; transition: transform 0.15s, box-shadow 0.15s;
            box-shadow: 0 4px 16px rgba(45,106,79,0.35); margin-top: 8px;
        }
        .submit-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(45,106,79,0.45); }
        .fade-in { animation: fadeIn 0.5s ease both; }
        @keyframes fadeIn { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
        .left-panel { background: linear-gradient(155deg, #0f2419 0%, #1a3d2b 40%, #2d6a4f 75%, #52b788 100%); }
    </style>
</head>
<body class="min-h-screen flex bg-[#fdf8f3]">

    <!-- Left Panel -->
    <div class="left-panel hidden md:flex w-5/12 min-h-screen flex-col justify-between p-12 relative overflow-hidden">
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-white opacity-5 pointer-events-none"></div>
        <div class="absolute -bottom-20 -left-20 w-80 h-80 rounded-full bg-white opacity-5 pointer-events-none"></div>

        <a href="index.php" class="font-lora text-white text-xl font-semibold tracking-wide relative z-10" style="text-decoration:none;">
            🌿 BridgeConnect
        </a>

        <div class="relative z-10">
            <h1 class="font-lora text-white text-4xl font-semibold leading-tight mb-5">
                Your community<br>is <em class="opacity-75">here for you.</em>
            </h1>
            <p class="text-white font-nunito text-base leading-relaxed mb-10" style="opacity:0.65;">
                Tell us a little about yourself so we can connect you with the resources that matter most for your situation.
            </p>
            <div class="space-y-4">
                <?php
                $steps = [
                    ['🏠', 'Find shelter with real-time bed availability'],
                    ['🍽️', 'Locate food pantries and meal programs'],
                    ['🤖', 'Get personalized AI-powered resource help'],
                    ['💼', 'Access job support and hygiene services'],
                ];
                foreach ($steps as [$icon, $text]):
                ?>
                <div class="flex items-center gap-3">
                    <span class="text-lg"><?php echo $icon; ?></span>
                    <span class="text-white font-nunito text-sm" style="opacity:0.75;"><?php echo $text; ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="relative z-10 font-nunito text-xs" style="color:rgba(255,255,255,0.3);">
            © <?php echo date('Y'); ?> BridgeConnect · Baltimore, MD
        </div>
    </div>

    <!-- Right Panel -->
    <div class="flex-1 flex flex-col justify-center items-center px-6 py-12 overflow-y-auto">
        <div class="w-full max-w-md fade-in">

            <a href="index.php" class="inline-flex items-center gap-2 text-sm font-bold mb-8 transition-opacity no-underline"
               style="color:#2d6a4f; opacity:0.6; text-decoration:none;"
               onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.6'">
                ← Back to Home
            </a>

            <div class="mb-8">
                <h2 class="font-lora text-3xl font-semibold mb-2" style="color:#1a3d2b;">Create your account</h2>
                <p class="text-sm" style="color:#9ca3af;">We'll use your info to find the best resources for you.</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="rounded-xl px-4 py-3 text-sm font-bold mb-6" style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b;">
                    ⚠️ <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <label class="block text-xs font-extrabold uppercase tracking-wider mb-2" style="color:#1a3d2b; letter-spacing:0.06em;">Full Name</label>
                <input class="input-field" name="name" placeholder="Your name" required>

                <label class="block text-xs font-extrabold uppercase tracking-wider mb-2" style="color:#1a3d2b; letter-spacing:0.06em;">Email Address</label>
                <input class="input-field" type="email" name="email" placeholder="you@example.com" required>

                <label class="block text-xs font-extrabold uppercase tracking-wider mb-2" style="color:#1a3d2b; letter-spacing:0.06em;">Password</label>
                <input class="input-field" type="password" name="password" placeholder="Choose a password" required>

                <label class="block text-xs font-extrabold uppercase tracking-wider mb-2" style="color:#1a3d2b; letter-spacing:0.06em;">I am a...</label>
                <select class="input-field" name="role" required>
                    <option value="person_in_need">Person Looking for Help</option>
                    <option value="volunteer">Volunteer</option>
                    <option value="shelter_provider">Shelter Provider</option>
                    <option value="admin">Admin</option>
                </select>

                <label class="block text-xs font-extrabold uppercase tracking-wider mb-2" style="color:#1a3d2b; letter-spacing:0.06em;">
                    Gender <span class="normal-case font-normal" style="color:#9ca3af;">(helps us find relevant resources)</span>
                </label>
                <select class="input-field" name="gender">
                    <option value="prefer_not_to_say">Prefer not to say</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="non_binary">Non-binary</option>
                </select>

                <label class="block text-xs font-extrabold uppercase tracking-wider mb-2" style="color:#1a3d2b; letter-spacing:0.06em;">
                    Your Situation <span class="normal-case font-normal" style="color:#9ca3af;">(optional)</span>
                </label>
                <textarea class="input-field" name="about" rows="3"
                    placeholder="e.g. I'm a veteran looking for temporary housing, I have kids and need food assistance... Share as much or as little as you're comfortable with."
                    style="min-height:90px; resize:vertical;"></textarea>

                <button class="submit-btn" type="submit">Create My Account →</button>
            </form>

            <p class="text-center text-sm mt-6" style="color:#9ca3af;">
                Already have an account?
                <a href="login.php" class="font-bold" style="color:#2d6a4f; text-decoration:none;"
                   onmouseover="this.style.color='#1a3d2b'" onmouseout="this.style.color='#2d6a4f'">Sign in</a>
            </p>
        </div>
    </div>

</body>
</html>