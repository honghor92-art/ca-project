<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
$page_title = 'Today';
$page_subtitle = 'Live check-ins for the selected date';

$date = $_GET['date'] ?? date('Y-m-d');
$date = date('Y-m-d', strtotime($date));

$records = $conn->query("
    SELECT a.*, s.full_name, s.student_code, s.image_student, s.class_name
    FROM attendance a JOIN students s ON a.student_id=s.id
    WHERE a.attendance_date='$date' ORDER BY a.attendance_time ASC
");
$total_students = $conn->query("SELECT COUNT(*) c FROM students WHERE status='active'")->fetch_assoc()['c'];
$present = $conn->query("SELECT COUNT(*) c FROM attendance WHERE attendance_date='$date' AND status IN ('Present','Late')")->fetch_assoc()['c'];
$late    = $conn->query("SELECT COUNT(*) c FROM attendance WHERE attendance_date='$date' AND status='Late'")->fetch_assoc()['c'];
$absent  = max(0, $total_students - $present);
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/sider.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="ml-60 pt-16 p-7 rise-in">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <form method="GET" class="flex items-center gap-2">
            <input type="date" name="date" value="<?= $date ?>"
                   class="px-3.5 py-2.5 bg-white border border-[#ECE6D9] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500/30"/>
            <button class="bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition"><i class="fas fa-calendar-check mr-1.5 text-xs"></i>View</button>
        </form>
        <a href="<?= BASE_URL ?>/attendance/export_excel.php?date=<?= $date ?>" class="bg-[#F1ECE0] hover:bg-[#E9E2D2] text-ink/70 text-sm font-semibold px-4 py-2.5 rounded-xl transition flex items-center gap-2">
            <i class="fas fa-file-export text-xs"></i> Export CSV
        </a>
    </div>

    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="panel p-5 text-center"><p class="stat-num text-2xl text-ink"><?= $present ?></p><p class="chip chip-present mt-2 inline-block">Present</p></div>
        <div class="panel p-5 text-center"><p class="stat-num text-2xl text-ink"><?= $late ?></p><p class="chip chip-late mt-2 inline-block">Late</p></div>
        <div class="panel p-5 text-center"><p class="stat-num text-2xl text-ink"><?= $absent ?></p><p class="chip chip-absent mt-2 inline-block">Absent</p></div>
    </div>

    <div class="panel overflow-hidden">
        <div class="px-6 py-4 border-b border-[#F1ECE0]">
            <h3 class="font-display font-semibold text-ink text-sm"><?= date('d F Y', strtotime($date)) ?></h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-ink/40 text-[11px] uppercase tracking-wide">
                    <th class="px-6 py-3 text-left font-semibold">Student</th>
                    <th class="px-6 py-3 text-left font-semibold">Code</th>
                    <th class="px-6 py-3 text-left font-semibold">Class</th>
                    <th class="px-6 py-3 text-left font-semibold">Time</th>
                    <th class="px-6 py-3 text-left font-semibold">Status</th>
                </tr></thead>
                <tbody class="divide-y divide-[#F4F0E6]">
                    <?php if ($records && $records->num_rows > 0): while ($row = $records->fetch_assoc()): ?>
                    <tr class="panel-row transition">
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-3">
                                <?php if ($row['image_student']): ?>
                                <img src="<?= BASE_URL ?>/asset/images/<?= htmlspecialchars($row['image_student']) ?>" class="w-8 h-8 rounded-full object-cover">
                                <?php else: ?>
                                <div class="w-8 h-8 rounded-full bg-teal-600 flex items-center justify-center text-white text-xs font-semibold"><?= strtoupper(substr($row['full_name'],0,1)) ?></div>
                                <?php endif; ?>
                                <span class="font-medium text-ink"><?= htmlspecialchars($row['full_name']) ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-3 font-mono text-xs text-teal-700"><?= htmlspecialchars($row['student_code']) ?></td>
                        <td class="px-6 py-3 text-ink/60"><?= htmlspecialchars($row['class_name']) ?></td>
                        <td class="px-6 py-3 font-mono text-ink/60 text-xs"><?= date('h:i:s A', strtotime($row['attendance_time'])) ?></td>
                        <td class="px-6 py-3"><span class="chip <?= $row['status']==='Present'?'chip-present':($row['status']==='Late'?'chip-late':'chip-absent') ?>"><?= $row['status'] ?></span></td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="5" class="text-center py-14 text-ink/30"><i class="fas fa-calendar-xmark text-3xl mb-3 block"></i>No records for this date.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
