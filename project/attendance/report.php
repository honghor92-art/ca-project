<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
$page_title = 'Reports';
$page_subtitle = 'Filter attendance across any date range';

$from   = date('Y-m-d', strtotime($_GET['from'] ?? date('Y-m-01')));
$to     = date('Y-m-d', strtotime($_GET['to'] ?? date('Y-m-d')));
$search = trim($_GET['search'] ?? '');

$where = "WHERE a.attendance_date BETWEEN '$from' AND '$to'";
if ($search) { $s = $conn->real_escape_string($search); $where .= " AND (s.full_name LIKE '%$s%' OR s.student_code LIKE '%$s%')"; }

$records = $conn->query("
    SELECT a.*, s.full_name, s.student_code, s.image_student, s.class_name
    FROM attendance a JOIN students s ON a.student_id=s.id
    $where ORDER BY a.attendance_date DESC, a.attendance_time ASC
");
$count = $records ? $records->num_rows : 0;
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/sider.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="ml-60 pt-16 p-7 rise-in">
    <div class="panel p-5 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-[11px] font-semibold text-ink/45 mb-1.5 uppercase tracking-wide">From</label>
                <input type="date" name="from" value="<?= $from ?>" class="px-3.5 py-2.5 bg-sand border border-[#ECE6D9] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500/30"/>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-ink/45 mb-1.5 uppercase tracking-wide">To</label>
                <input type="date" name="to" value="<?= $to ?>" class="px-3.5 py-2.5 bg-sand border border-[#ECE6D9] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500/30"/>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-ink/45 mb-1.5 uppercase tracking-wide">Search</label>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Name or code" class="px-3.5 py-2.5 bg-sand border border-[#ECE6D9] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500/30"/>
            </div>
            <button class="bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition"><i class="fas fa-magnifying-glass mr-1.5 text-xs"></i>Search</button>
            <a href="export_excel.php?from=<?= $from ?>&to=<?= $to ?>&search=<?= urlencode($search) ?>" class="bg-amber-500 hover:bg-amber-600 text-ink text-sm font-semibold px-5 py-2.5 rounded-xl transition"><i class="fas fa-file-export mr-1.5 text-xs"></i>Export CSV</a>
        </form>
    </div>

    <div class="panel overflow-hidden">
        <div class="px-6 py-4 border-b border-[#F1ECE0]">
            <span class="chip chip-present"><?= $count ?> records</span>
            <span class="text-ink/40 text-xs ml-2"><?= date('d M', strtotime($from)) ?> → <?= date('d M Y', strtotime($to)) ?></span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-ink/40 text-[11px] uppercase tracking-wide">
                    <th class="px-6 py-3 text-left font-semibold">Student</th>
                    <th class="px-6 py-3 text-left font-semibold">Code</th>
                    <th class="px-6 py-3 text-left font-semibold">Date</th>
                    <th class="px-6 py-3 text-left font-semibold">Time</th>
                    <th class="px-6 py-3 text-left font-semibold">Status</th>
                </tr></thead>
                <tbody class="divide-y divide-[#F4F0E6]">
                    <?php if ($records && $records->num_rows > 0): while ($row = $records->fetch_assoc()): ?>
                    <tr class="panel-row transition">
                        <td class="px-6 py-3 font-medium text-ink"><?= htmlspecialchars($row['full_name']) ?></td>
                        <td class="px-6 py-3 font-mono text-xs text-teal-700"><?= htmlspecialchars($row['student_code']) ?></td>
                        <td class="px-6 py-3 text-ink/60"><?= date('d/m/Y', strtotime($row['attendance_date'])) ?></td>
                        <td class="px-6 py-3 font-mono text-ink/60 text-xs"><?= date('h:i:s A', strtotime($row['attendance_time'])) ?></td>
                        <td class="px-6 py-3"><span class="chip <?= $row['status']==='Present'?'chip-present':($row['status']==='Late'?'chip-late':'chip-absent') ?>"><?= $row['status'] ?></span></td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="5" class="text-center py-14 text-ink/30"><i class="fas fa-chart-column text-3xl mb-3 block"></i>No records for this period.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
