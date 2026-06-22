<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
$page_title = 'QR Codes';
$page_subtitle = 'Generate & print student badges';

$img_dir = __DIR__ . '/../asset/images/';
if (!is_dir($img_dir)) mkdir($img_dir, 0777, true);
$msg = '';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $student = $conn->query("SELECT * FROM students WHERE id=$id")->fetch_assoc();
    if ($student) {
        $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($student['student_code']) . "&format=png";
        $ch = curl_init($qr_url);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_SSL_VERIFYPEER=>false, CURLOPT_TIMEOUT=>15]);
        $qr_data = curl_exec($ch); $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
        if ($qr_data && $http_code == 200) {
            $qr_filename = 'qr_' . $student['student_code'] . '.png';
            file_put_contents($img_dir . $qr_filename, $qr_data);
            $conn->query("UPDATE students SET qr_image='$qr_filename' WHERE id=$id");
            header('Location: generate_qr.php?msg=qr_generated'); exit;
        }
    }
}
if (isset($_GET['all'])) {
    $students = $conn->query("SELECT * FROM students");
    $count = 0; $errors = [];
    while ($s = $students->fetch_assoc()) {
        $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($s['student_code']) . "&format=png";
        $ch = curl_init($qr_url);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_SSL_VERIFYPEER=>false, CURLOPT_TIMEOUT=>15]);
        $qr_data = curl_exec($ch); $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
        if ($qr_data && $http_code == 200) {
            $qr_filename = 'qr_' . $s['student_code'] . '.png';
            file_put_contents($img_dir . $qr_filename, $qr_data);
            $esc = $conn->real_escape_string($qr_filename);
            $conn->query("UPDATE students SET qr_image='$esc' WHERE id={$s['id']}");
            $count++;
        } else { $errors[] = $s['student_code']; }
        usleep(250000);
    }
    $msg = "Generated $count QR codes." . (count($errors) ? " Failed: ".implode(', ',$errors) : '');
}
$students = $conn->query("SELECT * FROM students ORDER BY id ASC");
$qmsg = $_GET['msg'] ?? '';
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/sider.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="ml-60 pt-16 p-7 rise-in">
    <div class="flex items-center justify-between mb-6">
        <p class="text-ink/45 text-sm"><i class="fas fa-circle-info mr-1.5"></i>Each QR encodes the student's unique code for ESP32-CAM scanning.</p>
        <a href="generate_qr.php?all=1" onclick="return confirm('Generate QR for all students? This may take a moment.')"
           class="bg-amber-500 hover:bg-amber-600 text-ink font-semibold text-sm px-4 py-2.5 rounded-xl flex items-center gap-2 transition shadow-sm">
            <i class="fas fa-bolt text-xs"></i> Generate All
        </a>
    </div>

    <?php if ($msg || $qmsg==='qr_generated'): ?>
    <div class="bg-teal-50 border border-teal-100 text-teal-700 text-sm rounded-xl px-4 py-3 mb-5 flex items-center gap-2">
        <i class="fas fa-circle-check"></i><?= $msg ? htmlspecialchars($msg) : 'QR code generated.' ?>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        <?php while ($row = $students->fetch_assoc()): ?>
        <div class="panel p-4 text-center">
            <div class="bg-sand rounded-xl p-3 mb-3 flex items-center justify-center" style="aspect-ratio:1">
                <?php if ($row['qr_image'] && file_exists($img_dir . $row['qr_image'])): ?>
                <img src="<?= BASE_URL ?>/asset/images/<?= htmlspecialchars($row['qr_image']) ?>"
                     class="w-full h-full object-contain cursor-pointer hover:scale-105 transition"
                     onclick="showQR('<?= htmlspecialchars($row['student_code']) ?>','<?= htmlspecialchars($row['full_name']) ?>','<?= BASE_URL ?>/asset/images/<?= htmlspecialchars($row['qr_image']) ?>')">
                <?php else: ?>
                <i class="fas fa-qrcode text-3xl text-ink/15"></i>
                <?php endif; ?>
            </div>
            <p class="font-medium text-ink text-sm truncate"><?= htmlspecialchars($row['full_name']) ?></p>
            <p class="text-ink/40 text-xs font-mono mb-3"><?= htmlspecialchars($row['student_code']) ?></p>
            <a href="generate_qr.php?id=<?= $row['id'] ?>" class="block w-full bg-teal-50 hover:bg-teal-100 text-teal-700 text-xs font-semibold py-2 rounded-lg transition">
                <i class="fas fa-rotate mr-1"></i><?= $row['qr_image'] ? 'Regenerate' : 'Generate' ?>
            </a>
        </div>
        <?php endwhile; ?>
    </div>
</main>

<div id="qr-modal" class="hidden fixed inset-0 bg-ink/60 z-50 flex items-center justify-center p-4" onclick="closeQR()">
    <div class="bg-white rounded-3xl p-7 text-center max-w-xs w-full" onclick="event.stopPropagation()">
        <h3 id="modal-name" class="font-display font-semibold text-ink text-lg mb-0.5"></h3>
        <p id="modal-code" class="text-teal-600 text-xs font-mono mb-5"></p>
        <img id="modal-qr" src="" class="w-52 h-52 mx-auto border border-[#ECE6D9] rounded-2xl p-3">
        <div class="flex gap-2 mt-5">
            <a id="modal-download" href="" download class="flex-1 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold py-2.5 rounded-xl transition"><i class="fas fa-download mr-1.5"></i>Download</a>
            <button onclick="printQR()" class="flex-1 bg-[#F1ECE0] hover:bg-[#E9E2D2] text-ink/70 text-sm font-semibold py-2.5 rounded-xl transition"><i class="fas fa-print mr-1.5"></i>Print</button>
        </div>
        <button onclick="closeQR()" class="mt-3 text-ink/35 hover:text-ink/60 text-sm">Close</button>
    </div>
</div>
<script>
function showQR(code,name,src){
    document.getElementById('modal-name').textContent = name;
    document.getElementById('modal-code').textContent = code;
    document.getElementById('modal-qr').src = src;
    document.getElementById('modal-download').href = src;
    document.getElementById('modal-download').download = 'QR_' + code + '.png';
    document.getElementById('qr-modal').classList.remove('hidden');
}
function closeQR(){ document.getElementById('qr-modal').classList.add('hidden'); }
function printQR(){
    const img = document.getElementById('modal-qr').src;
    const name = document.getElementById('modal-name').textContent;
    const code = document.getElementById('modal-code').textContent;
    const w = window.open('');
    w.document.write(`<html><body style="text-align:center;font-family:sans-serif;padding:30px">
        <img src="${img}" style="width:200px;height:200px"><br>
        <b style="font-size:18px">${code}</b><br><span style="font-size:14px;color:#555">${name}</span>
        <script>window.onload=()=>window.print()<\/script></body></html>`);
    w.document.close();
}
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
