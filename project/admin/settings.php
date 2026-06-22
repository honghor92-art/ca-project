<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
$page_title = 'Settings';
$page_subtitle = 'School & system configuration';

$id = $_SESSION['admin_id'];
$admin = $conn->query("SELECT * FROM admins WHERE id=$id")->fetch_assoc();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $school_name    = trim($_POST['school_name'] ?? '');
    $school_address = trim($_POST['school_address'] ?? '');
    $school_phone   = trim($_POST['school_phone'] ?? '');
    $school_email   = trim($_POST['school_email'] ?? '');
    $stmt = $conn->prepare("UPDATE admins SET school_name=?, school_address=?, school_phone=?, school_email=? WHERE id=?");
    $stmt->bind_param('ssssi', $school_name, $school_address, $school_phone, $school_email, $id);
    $stmt->execute();
    foreach (['school_name','school_address','school_phone','school_email'] as $f) $admin[$f] = $$f;
    $msg = 'Settings saved.';
}
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/sider.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="ml-60 pt-16 p-7 rise-in max-w-xl">
    <?php if ($msg): ?>
    <div class="bg-teal-50 border border-teal-100 text-teal-700 text-sm rounded-xl px-4 py-3 mb-5 flex items-center gap-2"><i class="fas fa-circle-check"></i><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <div class="panel p-7">
        <div class="flex items-center gap-3 pb-5 border-b border-[#F1ECE0] mb-5">
            <span class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center"><i class="fas fa-school text-amber-600"></i></span>
            <div><h3 class="font-display font-semibold text-ink text-sm">School Information</h3><p class="text-ink/40 text-xs">Shown on reports and exports</p></div>
        </div>
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-[11px] font-semibold text-ink/45 mb-1.5 uppercase tracking-wide">School Name</label>
                <input type="text" name="school_name" value="<?= htmlspecialchars($admin['school_name'] ?? 'SETEC Institute') ?>" class="w-full px-4 py-2.5 bg-sand border border-[#ECE6D9] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500/30"/>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-ink/45 mb-1.5 uppercase tracking-wide">Address</label>
                <input type="text" name="school_address" value="<?= htmlspecialchars($admin['school_address'] ?? 'Phnom Penh, Cambodia') ?>" class="w-full px-4 py-2.5 bg-sand border border-[#ECE6D9] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500/30"/>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[11px] font-semibold text-ink/45 mb-1.5 uppercase tracking-wide">Phone</label>
                    <input type="text" name="school_phone" value="<?= htmlspecialchars($admin['school_phone'] ?? '012 345 678') ?>" class="w-full px-4 py-2.5 bg-sand border border-[#ECE6D9] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500/30"/>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-ink/45 mb-1.5 uppercase tracking-wide">Email</label>
                    <input type="email" name="school_email" value="<?= htmlspecialchars($admin['school_email'] ?? 'info@setec.edu.kh') ?>" class="w-full px-4 py-2.5 bg-sand border border-[#ECE6D9] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500/30"/>
                </div>
            </div>
            <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-semibold py-3 rounded-xl text-sm transition shadow-sm mt-2"><i class="fas fa-save mr-2 text-xs"></i>Save Changes</button>
        </form>
    </div>

    <div class="panel p-6 mt-5">
        <h3 class="font-display font-semibold text-ink text-sm mb-3"><i class="fas fa-link mr-2 text-teal-600"></i>API Endpoint</h3>
        <p class="text-ink/45 text-xs mb-3">Use this URL in your ESP32-CAM sketch to post attendance:</p>
        <code class="block bg-ink text-teal-300 text-xs font-mono px-4 py-3 rounded-xl break-all">GET <?= BASE_URL ?>/api/attendance.php?student_code=ST001</code>
    </div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
