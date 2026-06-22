<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/config/db.php';
if (isset($_SESSION['admin_id'])) { header('Location: ' . BASE_URL . '/admin/dashboard.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if ($username && $password) {
        $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $admin = $stmt->get_result()->fetch_assoc();
        if ($admin && ($admin['password'] === $password || password_verify($password, $admin['password']))) {
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_name'] = $admin['full_name'];
            header('Location: ' . BASE_URL . '/admin/dashboard.php');
            exit;
        } else { $error = 'Invalid username or password.'; }
    } else { $error = 'Please fill in all fields.'; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign in · Pulse Attendance</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<script>
tailwind.config = { theme: { extend: {
    fontFamily: { display:['Sora','sans-serif'], body:['Inter','sans-serif'], mono:['JetBrains Mono','monospace'] },
    colors: { ink:'#0B2027', teal:{50:'#EAF6F4',300:'#7FC4B8',500:'#1F7A6C',600:'#176358',700:'#114A42',900:'#0B2027'}, amber:{300:'#FFD27D',500:'#F2A93B',600:'#DB8A1B'}, coral:'#E8604C', sand:'#FBF8F3' }
}}}
</script>
<style>
*{font-family:'Inter',sans-serif}
.font-display{font-family:'Sora',sans-serif}
.font-mono{font-family:'JetBrains Mono',monospace}
body{background:#0B2027}

/* Scan visual */
.scan-frame{position:relative;width:230px;height:230px}
.corner{position:absolute;width:28px;height:28px;border-color:#F2A93B}
.corner-tl{top:0;left:0;border-top:3px solid;border-left:3px solid;border-radius:8px 0 0 0}
.corner-tr{top:0;right:0;border-top:3px solid;border-right:3px solid;border-radius:0 8px 0 0}
.corner-bl{bottom:0;left:0;border-bottom:3px solid;border-left:3px solid;border-radius:0 0 0 8px}
.corner-br{bottom:0;right:0;border-bottom:3px solid;border-right:3px solid;border-radius:0 0 8px 0}
.scan-beam{position:absolute;left:8px;right:8px;height:2px;background:linear-gradient(90deg,transparent,#F2A93B,transparent);box-shadow:0 0 12px #F2A93B;animation:sweep 2.6s ease-in-out infinite}
@keyframes sweep{0%,100%{top:14px;opacity:.9}50%{top:calc(100% - 16px);opacity:.5}}
.grid-dot{background-image:radial-gradient(circle,rgba(255,255,255,.06) 1px,transparent 1px);background-size:22px 22px}

@keyframes riseIn{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
.rise-in{animation:riseIn .5s cubic-bezier(.16,1,.3,1) forwards}
@keyframes floaty{0%,100%{transform:translateY(0)}50%{transform:translateY(-6px)}}
.floaty{animation:floaty 4s ease-in-out infinite}
</style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 grid-dot">
    <div class="w-full max-w-4xl bg-[#0F2D33] rounded-[28px] overflow-hidden flex flex-col md:flex-row shadow-2xl rise-in" style="box-shadow:0 30px 80px rgba(0,0,0,.5)">

        <!-- Left: Scan hero -->
        <div class="hidden md:flex md:w-[46%] relative flex-col items-center justify-center p-10" style="background:linear-gradient(160deg,#114A42 0%,#0B2027 100%)">
            <div class="absolute top-7 left-7 flex items-center gap-2.5">
                <div class="w-7 h-7 bg-amber-500 rounded-lg rotate-6"></div>
                <span class="font-display font-bold text-white text-sm">Pulse</span>
            </div>
            <div class="scan-frame floaty">
                <div class="corner corner-tl"></div>
                <div class="corner corner-tr"></div>
                <div class="corner corner-bl"></div>
                <div class="corner corner-br"></div>
                <div class="scan-beam"></div>
                <div class="absolute inset-7 flex items-center justify-center">
                    <i class="fas fa-qrcode text-teal-300/25 text-7xl"></i>
                </div>
            </div>
            <div class="mt-9 text-center max-w-[260px]">
                <p class="font-display text-white text-lg font-semibold leading-snug">Attendance, captured<br>the instant they arrive</p>
                <p class="text-teal-300/60 text-xs mt-2 leading-relaxed">ESP32-CAM reads each student's QR badge and logs it straight to your dashboard.</p>
            </div>
        </div>

        <!-- Right: Form -->
        <div class="flex-1 bg-sand p-8 sm:p-12 flex flex-col justify-center">
            <div class="md:hidden flex items-center gap-2.5 mb-8">
                <div class="w-7 h-7 bg-amber-500 rounded-lg rotate-6"></div>
                <span class="font-display font-bold text-ink text-sm">Pulse Attendance</span>
            </div>

            <h2 class="font-display font-bold text-ink text-2xl">Welcome back</h2>
            <p class="text-ink/45 text-sm mt-1 mb-7">Sign in to manage today's attendance.</p>

            <?php if ($error): ?>
            <div class="bg-coral/10 border border-coral/20 text-coral text-sm rounded-xl px-4 py-3 mb-5 flex items-center gap-2.5">
                <i class="fas fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-ink/60 mb-1.5 uppercase tracking-wide">Username</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-ink/30"><i class="fas fa-user text-sm"></i></span>
                        <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" placeholder="admin"
                               class="w-full pl-11 pr-4 py-3 bg-white border border-[#ECE6D9] rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-500 transition"/>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-ink/60 mb-1.5 uppercase tracking-wide">Password</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-ink/30"><i class="fas fa-lock text-sm"></i></span>
                        <input type="password" name="password" id="pwd" placeholder="••••••••"
                               class="w-full pl-11 pr-11 py-3 bg-white border border-[#ECE6D9] rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-500 transition"/>
                        <button type="button" onclick="togglePwd()" class="absolute right-4 top-1/2 -translate-y-1/2 text-ink/30 hover:text-ink/60">
                            <i class="fas fa-eye text-sm" id="eye"></i>
                        </button>
                    </div>
                </div>
                <button type="submit"
                        class="w-full bg-teal-600 hover:bg-teal-700 text-white font-semibold py-3.5 rounded-2xl transition-all shadow-lg shadow-teal-600/20 active:scale-[.98] mt-2">
                    Sign in <i class="fas fa-arrow-right ml-1.5 text-xs"></i>
                </button>
            </form>

            <div class="flex items-center gap-2 mt-6 text-ink/35 text-xs">
                <i class="fas fa-circle-info"></i>
                <span class="font-mono">sinh / snhna15042024</span>
            </div>
        </div>
    </div>

    <script>
    function togglePwd(){
        const p=document.getElementById('pwd'), e=document.getElementById('eye');
        if(p.type==='password'){p.type='text';e.className='fas fa-eye-slash text-sm';}
        else{p.type='password';e.className='fas fa-eye text-sm';}
    }
    </script>
</body>
</html>
