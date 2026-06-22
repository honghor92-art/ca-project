<?php $admin_name = $_SESSION['admin_name'] ?? 'Admin'; ?>
<header class="fixed top-0 left-60 right-0 h-16 bg-sand/80 backdrop-blur-md border-b border-[#ECE6D9] z-30 flex items-center justify-between px-7">
    <div>
        <h1 class="font-display font-semibold text-ink text-[15px]"><?= $page_title ?? 'Dashboard' ?></h1>
        <p class="text-[11px] text-ink/40 -mt-0.5"><?= $page_subtitle ?? date('l, d F Y') ?></p>
    </div>
    <div class="flex items-center gap-4">
        <div class="hidden sm:flex items-center gap-2 bg-white border border-[#ECE6D9] rounded-full pl-3 pr-1 py-1">
            <i class="fas fa-clock text-amber-600 text-xs"></i>
            <span id="live-clock" class="font-mono text-xs text-ink/70 pr-2"></span>
        </div>
        <div class="flex items-center gap-2.5 bg-white border border-[#ECE6D9] rounded-full pl-1.5 pr-3.5 py-1">
            <div class="w-7 h-7 rounded-full bg-teal-600 flex items-center justify-center text-white text-xs font-semibold">
                <?= strtoupper(substr($admin_name,0,1)) ?>
            </div>
            <span class="text-sm font-medium text-ink/80"><?= htmlspecialchars($admin_name) ?></span>
        </div>
    </div>
</header>
<script>
function tickClock(){
    const d = new Date();
    document.getElementById('live-clock').textContent = d.toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit',second:'2-digit'});
}
tickClock(); setInterval(tickClock,1000);
</script>
