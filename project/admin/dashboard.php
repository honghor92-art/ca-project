<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
$page_title = 'Dashboard';
$page_subtitle = date('l, d F Y');

$total_students = $conn->query("SELECT COUNT(*) as c FROM students WHERE status='active'")->fetch_assoc()['c'];
$today = date('Y-m-d');
$present_today = $conn->query("SELECT COUNT(*) as c FROM attendance WHERE attendance_date='$today' AND status IN ('Present','Late')")->fetch_assoc()['c'];
$late_today    = $conn->query("SELECT COUNT(*) as c FROM attendance WHERE attendance_date='$today' AND status='Late'")->fetch_assoc()['c'];
$absent_today  = max(0, $total_students - $present_today);
$total_records = $conn->query("SELECT COUNT(*) as c FROM attendance")->fetch_assoc()['c'];

$weekly = [];
for ($i = 6; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    $label = date('D', strtotime("-$i days"));
    $cnt = $conn->query("SELECT COUNT(*) as c FROM attendance WHERE attendance_date='$d' AND status IN ('Present','Late')")->fetch_assoc()['c'];
    $weekly[] = ['label' => $label, 'count' => (int)$cnt];
}

$recent = $conn->query("
    SELECT a.*, s.full_name, s.student_code, s.image_student, s.class_name
    FROM attendance a JOIN students s ON a.student_id=s.id
    WHERE a.attendance_date='$today'
    ORDER BY a.attendance_time DESC LIMIT 6
");

$ratio = $total_students > 0 ? round($present_today/$total_students*100) : 0;
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/sider.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="ml-60 pt-16 p-7 rise-in">

    <!-- Stat Strip -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="panel p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="w-10 h-10 rounded-xl bg-teal-50 flex items-center justify-center"><i class="fas fa-users text-teal-600"></i></span>
                <span class="chip chip-present">Active</span>
            </div>
            <p class="stat-num text-2xl text-ink"><?= $total_students ?></p>
            <p class="text-ink/45 text-xs mt-0.5">Total Students</p>
        </div>
        <div class="panel p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center"><i class="fas fa-circle-check text-emerald-600"></i></span>
                <span class="text-emerald-600 text-xs font-semibold"><?= $ratio ?>%</span>
            </div>
            <p class="stat-num text-2xl text-ink"><?= $present_today ?></p>
            <p class="text-ink/45 text-xs mt-0.5">Present Today</p>
        </div>
        <div class="panel p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center"><i class="fas fa-clock text-amber-600"></i></span>
            </div>
            <p class="stat-num text-2xl text-ink"><?= $late_today ?></p>
            <p class="text-ink/45 text-xs mt-0.5">Arrived Late</p>
        </div>
        <div class="panel p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center"><i class="fas fa-user-xmark text-rose-500"></i></span>
            </div>
            <p class="stat-num text-2xl text-ink"><?= $absent_today ?></p>
            <p class="text-ink/45 text-xs mt-0.5">Absent Today</p>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
        <div class="lg:col-span-2 panel p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="font-display font-semibold text-ink text-sm">Attendance, last 7 days</h3>
                    <p class="text-ink/40 text-xs mt-0.5">Students checked in per day</p>
                </div>
                <span class="chip chip-present"><?= $total_records ?> total records</span>
            </div>
            <div class="h-56"><canvas id="weeklyChart"></canvas></div>
        </div>

        <div class="panel p-6 flex flex-col">
            <h3 class="font-display font-semibold text-ink text-sm mb-1">Today's pulse</h3>
            <p class="text-ink/40 text-xs mb-4">Live attendance ratio</p>
            <div class="relative flex-1 flex items-center justify-center">
                <canvas id="donutChart" width="170" height="170"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="stat-num text-3xl text-ink"><?= $ratio ?>%</span>
                    <span class="text-ink/40 text-[11px]">checked in</span>
                </div>
            </div>
            <div class="flex justify-center gap-4 mt-2">
                <div class="flex items-center gap-1.5 text-xs text-ink/55"><span class="w-2.5 h-2.5 rounded-full bg-teal-500"></span>Present</div>
                <div class="flex items-center gap-1.5 text-xs text-ink/55"><span class="w-2.5 h-2.5 rounded-full bg-[#F1E4D0]"></span>Absent</div>
            </div>
        </div>
    </div>

    <!-- Recent feed -->
    <div class="panel overflow-hidden">
        <div class="flex items-center justify-between px-6 py-5 border-b border-[#F1ECE0]">
            <div>
                <h3 class="font-display font-semibold text-ink text-sm">Today's check-ins</h3>
                <p class="text-ink/40 text-xs mt-0.5">Most recent scans first</p>
            </div>
            <a href="<?= BASE_URL ?>/attendance/today.php" class="text-teal-600 text-xs font-semibold hover:text-teal-700">View all →</a>
        </div>
        <div class="divide-y divide-[#F4F0E6]">
            <?php if ($recent && $recent->num_rows > 0): while ($row = $recent->fetch_assoc()): ?>
            <div class="panel-row flex items-center gap-4 px-6 py-3.5 transition">
                <?php if ($row['image_student']): ?>
                <img src="<?= BASE_URL ?>/asset/images/<?= htmlspecialchars($row['image_student']) ?>" class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                <?php else: ?>
                <div class="w-10 h-10 rounded-full bg-teal-600 flex items-center justify-center text-white text-sm font-semibold flex-shrink-0">
                    <?= strtoupper(substr($row['full_name'],0,1)) ?>
                </div>
                <?php endif; ?>
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-ink text-sm truncate"><?= htmlspecialchars($row['full_name']) ?></p>
                    <p class="text-ink/40 text-xs font-mono"><?= htmlspecialchars($row['student_code']) ?> · <?= htmlspecialchars($row['class_name']) ?></p>
                </div>
                <span class="text-ink/45 text-xs font-mono"><?= date('h:i A', strtotime($row['attendance_time'])) ?></span>
                <span class="chip <?= $row['status']==='Present'?'chip-present':($row['status']==='Late'?'chip-late':'chip-absent') ?>"><?= $row['status'] ?></span>
            </div>
            <?php endwhile; else: ?>
            <div class="text-center py-14 text-ink/30">
                <i class="fas fa-inbox text-3xl mb-3 block"></i>
                No check-ins yet today.
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const weeklyLabels = <?= json_encode(array_column($weekly,'label')) ?>;
const weeklyCounts = <?= json_encode(array_column($weekly,'count')) ?>;

new Chart(document.getElementById('weeklyChart'), {
    type:'bar',
    data:{ labels: weeklyLabels, datasets:[{
        data: weeklyCounts,
        backgroundColor: '#1F7A6C',
        hoverBackgroundColor: '#F2A93B',
        borderRadius: 8,
        maxBarThickness: 38
    }]},
    options:{
        responsive:true, maintainAspectRatio:false,
        plugins:{ legend:{display:false}, tooltip:{backgroundColor:'#0B2027', padding:10, cornerRadius:8, titleFont:{family:'Sora'}, bodyFont:{family:'Inter'}} },
        scales:{
            y:{ beginAtZero:true, grid:{color:'#F1ECE0'}, ticks:{font:{size:11,family:'Inter'},color:'#0B2027' ,callback:v=>Number.isInteger(v)?v:null} },
            x:{ grid:{display:false}, ticks:{font:{size:11,family:'Inter'},color:'#0B2027'} }
        }
    }
});

const present = <?= $present_today ?>, absent = <?= $absent_today ?>;
new Chart(document.getElementById('donutChart'), {
    type:'doughnut',
    data:{ datasets:[{ data:[present || 0.0001, absent], backgroundColor:['#1F7A6C','#F1E4D0'], borderWidth:0, cutout:'76%' }]},
    options:{ plugins:{legend:{display:false}}, animation:{duration:900} }
});
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
