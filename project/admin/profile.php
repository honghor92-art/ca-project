<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
$page_title = 'Profile';
$page_subtitle = 'Your account information';

$id = $_SESSION['admin_id'];
$admin = $conn->query("SELECT * FROM admins WHERE id=$id")->fetch_assoc();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $new_pass  = trim($_POST['new_password'] ?? '');

    if ($new_pass) {
        $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE admins SET full_name=?, email=?, password=? WHERE id=?");
        $stmt->bind_param('sssi', $full_name, $email, $hashed, $id);
    } else {
        $stmt = $conn->prepare("UPDATE admins SET full_name=?, email=? WHERE id=?");
        $stmt->bind_param('ssi', $full_name, $email, $id);
    }
    $stmt->execute();
    $_SESSION['admin_name'] = $full_name;
    $admin['full_name'] = $full_name; $admin['email'] = $email;
    $msg = 'Profile updated successfully.';
}
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/sider.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="ml-60 pt-16 p-7 rise-in max-w-xl">
    <?php if ($msg): ?>
    <div class="bg-teal-50 border border-teal-100 text-teal-700 text-sm rounded-xl px-4 py-3 mb-5 flex items-center gap-2"><i class="fas fa-circle-check"></i><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <div class="panel overflow-hidden">
        <div class="px-7 py-8 text-center" style="background:linear-gradient(160deg,#114A42 0%,#0B2027 100%)">
            <div class="w-20 h-20 rounded-full bg-amber-500 flex items-center justify-center mx-auto mb-3 text-ink font-display font-bold text-2xl">
                <?= strtoupper(substr($admin['full_name'],0,1)) ?>
            </div>
            <h3 class="font-display font-semibold text-white text-lg"><?= htmlspecialchars($admin['full_name']) ?></h3>
            <p class="text-teal-300 text-sm font-mono">@<?= htmlspecialchars($admin['username']) ?></p>
        </div>
        <form method="POST" class="p-7 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[11px] font-semibold text-ink/45 mb-1.5 uppercase tracking-wide">Username</label>
                    <input type="text" value="<?= htmlspecialchars($admin['username']) ?>" readonly class="w-full px-4 py-2.5 bg-[#F1ECE0] border border-[#ECE6D9] rounded-xl text-sm text-ink/45 font-mono"/>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-ink/45 mb-1.5 uppercase tracking-wide">Full Name</label>
                    <input type="text" name="full_name" value="<?= htmlspecialchars($admin['full_name']) ?>" class="w-full px-4 py-2.5 bg-sand border border-[#ECE6D9] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500/30"/>
                </div>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-ink/45 mb-1.5 uppercase tracking-wide">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($admin['email'] ?? '') ?>" class="w-full px-4 py-2.5 bg-sand border border-[#ECE6D9] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500/30"/>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-ink/45 mb-1.5 uppercase tracking-wide">New Password <span class="text-ink/30">(optional)</span></label>
                <input type="password" name="new_password" placeholder="Leave blank to keep current" class="w-full px-4 py-2.5 bg-sand border border-[#ECE6D9] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500/30"/>
            </div>
            <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-semibold py-3 rounded-xl text-sm transition shadow-sm mt-2"><i class="fas fa-save mr-2 text-xs"></i>Update Profile</button>
        </form>
    </div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
