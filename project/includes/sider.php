<?php
$cur_page = basename($_SERVER['PHP_SELF']);
$cur_dir  = basename(dirname($_SERVER['PHP_SELF']));
function isActive($page, $dir = '') {
    global $cur_page, $cur_dir;
    if ($dir && $cur_dir === $dir) return 'active';
    if (!$dir && $cur_page === $page) return 'active';
    return '';
}
?>
<aside class="fixed left-0 top-0 h-full w-60 z-40 flex flex-col" style="background:linear-gradient(195deg,#0F2D33 0%,#0B2027 100%)">
    <!-- Brand -->
    <div class="px-5 py-5 flex items-center gap-3">
        <div class="relative w-9 h-9 flex items-center justify-center">
            <div class="absolute inset-0 bg-amber-500 rounded-xl rotate-6"></div>
            <i class="fas fa-radar relative text-ink text-sm"></i>
        </div>
        <div class="leading-tight">
            <p class="font-display font-bold text-white text-[15px]">Pulse</p>
            <p class="text-teal-300 text-[11px] -mt-0.5 tracking-wide">ATTENDANCE</p>
        </div>
    </div>
    <div class="h-px bg-white/8 mx-5"></div>

    <!-- Nav -->
    <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
        <p class="text-white/30 text-[10px] font-semibold uppercase tracking-widest px-3 pb-1.5 pt-1">Overview</p>
        <a href="<?= BASE_URL ?>/admin/dashboard.php" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm <?= isActive('dashboard.php','admin') ?>">
            <i class="fas fa-grid-2 w-4 text-center text-xs"></i> Dashboard
        </a>

        <p class="text-white/30 text-[10px] font-semibold uppercase tracking-widest px-3 pb-1.5 pt-4">People</p>
        <a href="<?= BASE_URL ?>/students/index.php" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm <?= isActive('index.php','students') ?>">
            <i class="fas fa-user-graduate w-4 text-center text-xs"></i> Students
        </a>
        <a href="<?= BASE_URL ?>/students/generate_qr.php" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm <?= isActive('generate_qr.php','students') ?>">
            <i class="fas fa-qrcode w-4 text-center text-xs"></i> QR Codes
        </a>

        <p class="text-white/30 text-[10px] font-semibold uppercase tracking-widest px-3 pb-1.5 pt-4">Records</p>
        <a href="<?= BASE_URL ?>/attendance/today.php" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm <?= isActive('today.php','attendance') ?>">
            <i class="fas fa-calendar-day w-4 text-center text-xs"></i> Today
        </a>
        <a href="<?= BASE_URL ?>/attendance/report.php" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm <?= isActive('report.php','attendance') ?>">
            <i class="fas fa-chart-column w-4 text-center text-xs"></i> Reports
        </a>

        <p class="text-white/30 text-[10px] font-semibold uppercase tracking-widest px-3 pb-1.5 pt-4">Account</p>
        <a href="<?= BASE_URL ?>/admin/profile.php" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm <?= isActive('profile.php','admin') ?>">
            <i class="fas fa-circle-user w-4 text-center text-xs"></i> Profile
        </a>
        <a href="<?= BASE_URL ?>/admin/settings.php" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm <?= isActive('settings.php','admin') ?>">
            <i class="fas fa-sliders w-4 text-center text-xs"></i> Settings
        </a>
    </nav>

    <!-- ESP32 status + logout -->
    <div class="px-4 pb-4">
        <div class="bg-white/5 rounded-xl px-3 py-2.5 mb-2 flex items-center gap-2.5">
            <span class="w-2 h-2 rounded-full bg-emerald-400 pulse-dot"></span>
            <div class="leading-tight">
                <p class="text-white/80 text-[11px] font-medium">ESP32-CAM</p>
                <p class="text-white/35 text-[10px]">Listening for scans</p>
            </div>
        </div>
        <a href="<?= BASE_URL ?>/logout.php" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-white/50 hover:text-coral">
            <i class="fas fa-arrow-right-from-bracket w-4 text-center text-xs"></i> Logout
        </a>
    </div>
</aside>
