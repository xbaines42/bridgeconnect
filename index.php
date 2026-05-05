<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>BridgeConnect — Find Help Near You</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,600;1,400&family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { lora: ['Lora','serif'], nunito: ['Nunito','sans-serif'] } } }
        }
    </script>
    <style>
        body { font-family: 'Nunito', sans-serif; background: #fdf8f3; margin: 0; }
        .hero { background: linear-gradient(155deg, #0f2419 0%, #1a3d2b 40%, #2d6a4f 75%, #52b788 100%); position: relative; overflow: hidden; }
        .hero::before { content:''; position:absolute; top:-100px; right:-100px; width:500px; height:500px; border-radius:50%; background:rgba(255,255,255,0.05); pointer-events:none; }
        .hero::after  { content:''; position:absolute; bottom:-80px; left:-80px; width:400px; height:400px; border-radius:50%; background:rgba(255,255,255,0.04); pointer-events:none; }
        .hero-btn {
            display: inline-block; padding: 14px 28px; border-radius: 12px;
            font-weight: 800; font-size: 16px; font-family: 'Nunito', sans-serif;
            text-decoration: none; transition: transform 0.15s, box-shadow 0.15s;
        }
        .hero-btn:hover { transform: translateY(-2px); }
        .hero-btn-primary { background: white; color: #1a3d2b; box-shadow: 0 4px 20px rgba(0,0,0,0.2); }
        .hero-btn-primary:hover { box-shadow: 0 8px 28px rgba(0,0,0,0.3); }
        .hero-btn-secondary { background: rgba(255,255,255,0.15); color: white; border: 2px solid rgba(255,255,255,0.3); }
        .hero-btn-secondary:hover { background: rgba(255,255,255,0.25); }
        .feature-card {
            background: white; border-radius: 20px; padding: 28px 24px;
            border: 1px solid #e8f5ee; box-shadow: 0 4px 20px rgba(45,106,79,0.07);
            transition: transform 0.2s, box-shadow 0.2s; text-align: center;
        }
        .feature-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(45,106,79,0.13); }
        .cta-btn {
            display: inline-block; padding: 16px 32px;
            background: linear-gradient(135deg, #1a3d2b, #2d6a4f, #52b788);
            color: white; border-radius: 14px; font-weight: 800; font-size: 17px;
            font-family: 'Nunito', sans-serif; text-decoration: none;
            transition: transform 0.15s, box-shadow 0.15s;
            box-shadow: 0 6px 20px rgba(45,106,79,0.35);
        }
        .cta-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(45,106,79,0.45); }
        .fade-in { animation: fadeIn 0.6s ease both; }
        @keyframes fadeIn { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
    </style>
</head>
<body>

<!-- HERO -->
<div class="hero min-h-screen flex flex-col">

    <!-- Nav -->
    <nav class="flex justify-between items-center px-8 py-6 relative z-10">
        <span class="font-lora text-white text-xl font-semibold">🌿 BridgeConnect</span>
        <div class="flex items-center gap-3">
            <a href="login.php" class="text-white font-bold text-sm opacity-80 hover:opacity-100 transition-opacity" style="text-decoration:none;">Login</a>
            <a href="signup.php" class="hero-btn hero-btn-secondary text-sm py-2 px-5">Get Started</a>
        </div>
    </nav>

    <!-- Hero Content -->
    <div class="flex-1 flex flex-col justify-center items-center text-center px-6 pb-20 relative z-10 fade-in">
        <div class="inline-block bg-white/10 text-white text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-full mb-6 border border-white/20">
            Baltimore, MD · Free to Use
        </div>
        <h1 class="font-lora text-white text-5xl md:text-6xl font-semibold leading-tight mb-6 max-w-3xl">
            Everyone deserves a<br><em class="opacity-80">safe place to start over.</em>
        </h1>
        <p class="text-white/70 text-lg leading-relaxed mb-10 max-w-xl font-nunito">
            BridgeConnect connects people in need with shelters, food, medical care, hygiene services, and job support — updated in real time.
        </p>
        <div class="flex flex-wrap gap-4 justify-center">
            <a href="signup.php" class="hero-btn hero-btn-primary">Find Resources Near You →</a>
            <a href="login.php" class="hero-btn hero-btn-secondary">Sign In</a>
        </div>

        <!-- Quick stats -->
        <div class="flex flex-wrap gap-8 justify-center mt-16">
            <?php
            $stats = [['7+','Resources Available'],['4','User Roles'],['24/7','Real-Time Updates'],['Free','Always']];
            foreach ($stats as [$num,$label]):
            ?>
            <div class="text-center">
                <div class="font-lora text-white text-3xl font-semibold"><?php echo $num; ?></div>
                <div class="text-white/50 text-xs font-bold uppercase tracking-wider mt-1"><?php echo $label; ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Wave divider -->
    <div class="relative z-10" style="margin-bottom:-2px;">
        <svg viewBox="0 0 1440 60" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" style="display:block;width:100%;height:60px;">
            <path d="M0,30 C360,60 1080,0 1440,30 L1440,60 L0,60 Z" fill="#fdf8f3"/>
        </svg>
    </div>
</div>

<!-- FEATURES -->
<div class="max-w-6xl mx-auto px-6 py-20">
    <div class="text-center mb-14">
        <h2 class="font-lora text-4xl font-semibold mb-4" style="color:#1a3d2b;">What BridgeConnect Offers</h2>
        <p class="text-gray-400 text-base max-w-md mx-auto">Everything you need to find help or give it — all in one place.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php
        $features = [
            ['🏠', 'Find Shelter',        'View real-time shelter bed availability and get directions instantly.'],
            ['🍽️', 'Food Resources',      'Locate food pantries, meal programs, and weekly food drives near you.'],
            ['🩺', 'Medical & Hygiene',   'Access free clinics, hygiene centers, showers, and laundry support.'],
            ['💼', 'Job Support',         'Get resume help, interview prep, and job search assistance.'],
            ['🤖', 'AI Assistant',        'Chat with our AI to get personalized resource recommendations.'],
            ['🔐', 'Role-Based Access',   'Providers update beds, volunteers sign up, and admins manage everything.'],
        ];
        foreach ($features as [$icon, $title, $desc]):
        ?>
        <div class="feature-card">
            <div class="text-4xl mb-4"><?php echo $icon; ?></div>
            <h3 class="font-lora text-lg font-semibold mb-2" style="color:#1a3d2b;"><?php echo $title; ?></h3>
            <p class="text-sm leading-relaxed" style="color:#6b7280;"><?php echo $desc; ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- HOW IT WORKS -->
<div class="py-20 px-6" style="background:linear-gradient(135deg,#f0fdf4,#fdf8f3);">
    <div class="max-w-4xl mx-auto text-center mb-14">
        <h2 class="font-lora text-4xl font-semibold mb-4" style="color:#1a3d2b;">How It Works</h2>
        <p class="text-gray-400 text-base">Three simple steps to find the help you need.</p>
    </div>
    <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8">
        <?php
        $steps = [
            ['01', 'Create an Account', 'Sign up in seconds and tell us a little about your situation.'],
            ['02', 'Browse Resources',  'Search and filter shelters, food, medical, hygiene, and job support.'],
            ['03', 'Get Connected',     'Get directions, visit websites, or ask our AI assistant for help.'],
        ];
        foreach ($steps as [$num, $title, $desc]):
        ?>
        <div class="text-center">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-5 font-lora font-bold text-xl text-white"
                 style="background:linear-gradient(135deg,#1a3d2b,#52b788);"><?php echo $num; ?></div>
            <h3 class="font-lora text-lg font-semibold mb-2" style="color:#1a3d2b;"><?php echo $title; ?></h3>
            <p class="text-sm leading-relaxed" style="color:#6b7280;"><?php echo $desc; ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- CTA -->
<div class="py-24 px-6 text-center" style="background:#fdf8f3;">
    <h2 class="font-lora text-4xl font-semibold mb-4" style="color:#1a3d2b;">Ready to find help?</h2>
    <p class="text-gray-400 text-base max-w-md mx-auto mb-10">Create a free account in seconds and get connected to resources in your area.</p>
    <a href="signup.php" class="cta-btn">Create Free Account →</a>
</div>

<!-- Footer -->
<footer class="px-8 py-8 flex flex-wrap justify-between items-center gap-4" style="background:#1a3d2b;">
    <span class="font-lora text-white text-lg font-semibold">🌿 BridgeConnect</span>
    <span class="text-white/40 text-sm font-nunito">© <?php echo date('Y'); ?> BridgeConnect · Baltimore, MD · Free to use</span>
    <div class="flex gap-6">
        <a href="login.php" class="text-white/60 text-sm font-bold hover:text-white transition-colors" style="text-decoration:none;">Login</a>
        <a href="signup.php" class="text-white/60 text-sm font-bold hover:text-white transition-colors" style="text-decoration:none;">Sign Up</a>
    </div>
</footer>

</body>
</html>