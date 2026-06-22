<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
$page_title = 'Students';
$page_subtitle = 'Manage every learner in your roster';

if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM students WHERE id=" . (int)$_GET['delete']);
    header('Location: index.php?msg=deleted'); exit;
}

$search = trim($_GET['search'] ?? '');
$where = '';
if ($search) {
    $s = $conn->real_escape_string($search);
    $where = "WHERE student_code LIKE '%$s%' OR full_name LIKE '%$s%' OR class_name LIKE '%$s%'";
}
$students = $conn->query("SELECT * FROM students $where ORDER BY id ASC");
$total = $conn->query("SELECT COUNT(*) c FROM students")->fetch_assoc()['c'];
$msg = $_GET['msg'] ?? '';
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/sider.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="ml-60 pt-16 p-7 rise-in">
    <div class="flex items-center justify-between mb-6">
        <span class="chip chip-present"><?= $total ?> students enrolled</span>
        <a href="add.php" class="bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl flex items-center gap-2 transition shadow-sm">
            <i class="fas fa-plus text-xs"></i> Add Student
        </a>
    </div>

    <?php if ($msg === 'deleted'): ?>
    <div class="bg-coral/10 border border-coral/20 text-coral text-sm rounded-xl px-4 py-3 mb-5 flex items-center gap-2"><i class="fas fa-trash"></i>Student removed.</div>
    <?php elseif ($msg === 'added'): ?>
    <div class="bg-teal-50 border border-teal-100 text-teal-700 text-sm rounded-xl px-4 py-3 mb-5 flex items-center gap-2"><i class="fas fa-circle-check"></i>Student added with QR code.</div>
    <?php elseif ($msg === 'updated'): ?>
    <div class="bg-teal-50 border border-teal-100 text-teal-700 text-sm rounded-xl px-4 py-3 mb-5 flex items-center gap-2"><i class="fas fa-circle-check"></i>Student updated.</div>
    <?php endif; ?>

    <div class="panel overflow-hidden">
        <div class="p-5 border-b border-[#F1ECE0]">
            <form method="GET" class="relative max-w-xs">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-ink/30"><i class="fas fa-magnifying-glass text-xs"></i></span>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search by name, code, class..."
                       class="w-full pl-9 pr-4 py-2.5 bg-sand border border-[#ECE6D9] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500/30"/>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-ink/40 text-[11px] uppercase tracking-wide">
                        <th class="px-6 py-3 text-left font-semibold">Student</th>
                        <th class="px-6 py-3 text-left font-semibold">Code</th>
                        <th class="px-6 py-3 text-left font-semibold">Class</th>
                        <th class="px-6 py-3 text-left font-semibold">Gender</th>
                        <th class="px-6 py-3 text-left font-semibold">QR</th>
                        <th class="px-6 py-3 text-center font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F4F0E6]">
                    <?php if ($students && $students->num_rows > 0): while ($row = $students->fetch_assoc()): ?>
                    <tr class="panel-row transition">
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-3">
                                <?php if ($row['image_student']): ?>
                                <img src="<?= BASE_URL ?>/asset/images/<?= htmlspecialchars($row['image_student']) ?>" class="w-9 h-9 rounded-full object-cover">
                                <?php else: ?>
                                <div class="w-9 h-9 rounded-full bg-teal-600 flex items-center justify-center text-white text-xs font-semibold">
                                    <?= strtoupper(substr($row['full_name'],0,1)) ?>
                                </div>
                                <?php endif; ?>
                                <span class="font-medium text-ink"><?= htmlspecialchars($row['full_name']) ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-3 font-mono text-xs text-teal-700"><?= htmlspecialchars($row['student_code']) ?></td>
                        <td class="px-6 py-3"><span class="bg-amber-100 text-amber-700 text-xs font-semibold px-2.5 py-1 rounded-full"><?= htmlspecialchars($row['class_name']) ?></span></td>
                        <td class="px-6 py-3 text-ink/60"><?= htmlspecialchars($row['gender']) ?></td>
                        <td class="px-6 py-3">
                            <?php if ($row['qr_image']): ?>
                            <img src="<?= BASE_URL ?>/asset/images/<?= htmlspecialchars($row['qr_image']) ?>" class="w-9 h-9 object-contain">
                            <?php else: ?>
                            <span class="text-ink/30 text-xs">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-3 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="edit.php?id=<?= $row['id'] ?>" class="w-8 h-8 bg-teal-50 hover:bg-teal-100 text-teal-600 rounded-lg flex items-center justify-center transition"><i class="fas fa-pen text-xs"></i></a>
                                <a href="index.php?delete=<?= $row['id'] ?>" onclick="return confirm('Remove this student?')" class="w-8 h-8 bg-rose-50 hover:bg-rose-100 text-rose-500 rounded-lg flex items-center justify-center transition"><i class="fas fa-trash text-xs"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="6" class="text-center py-14 text-ink/30"><i class="fas fa-user-graduate text-3xl mb-3 block"></i>No students found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
