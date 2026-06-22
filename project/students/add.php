<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
$page_title = 'Add Student';
$page_subtitle = 'QR code is generated automatically';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code   = trim($_POST['student_code'] ?? '');
    $name   = trim($_POST['full_name'] ?? '');
    $gender = $_POST['gender'] ?? 'Male';
    $class  = trim($_POST['class_name'] ?? '');

    if (!$code || !$name) {
        $error = 'Student code and full name are required.';
    } else {
        $chk = $conn->prepare("SELECT id FROM students WHERE student_code=?");
        $chk->bind_param('s', $code); $chk->execute();
        if ($chk->get_result()->num_rows > 0) {
            $error = 'This student code already exists.';
        } else {
            $img_dir = __DIR__ . '/../asset/images/';
            if (!is_dir($img_dir)) mkdir($img_dir, 0777, true);

            $img_name = '';
            if (!empty($_FILES['photo']['name'])) {
                $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
                    $img_name = 'student_' . time() . '.' . $ext;
                    move_uploaded_file($_FILES['photo']['tmp_name'], $img_dir . $img_name);
                }
            }

            $qr_filename = '';
            $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($code) . "&format=png";
            $ch = curl_init($qr_url);
            curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_SSL_VERIFYPEER=>false, CURLOPT_TIMEOUT=>15]);
            $qr_data = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($qr_data && $http_code == 200) {
                $qr_filename = 'qr_' . $code . '.png';
                file_put_contents($img_dir . $qr_filename, $qr_data);
            }

            $stmt = $conn->prepare("INSERT INTO students (student_code,full_name,gender,class_name,image_student,qr_image) VALUES (?,?,?,?,?,?)");
            $stmt->bind_param('ssssss', $code, $name, $gender, $class, $img_name, $qr_filename);
            if ($stmt->execute()) { header('Location: index.php?msg=added'); exit; }
            else { $error = 'Database error: ' . $conn->error; }
        }
    }
}
$classes = $conn->query("SELECT * FROM classes ORDER BY class_name");
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/sider.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="ml-60 pt-16 p-7 rise-in">
    <a href="index.php" class="inline-flex items-center gap-2 text-ink/45 hover:text-ink text-sm mb-5 transition">
        <i class="fas fa-arrow-left text-xs"></i> Back to students
    </a>

    <?php if ($error): ?>
    <div class="bg-coral/10 border border-coral/20 text-coral text-sm rounded-xl px-4 py-3 mb-5 flex items-center gap-2"><i class="fas fa-circle-exclamation"></i><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Form -->
        <form method="POST" enctype="multipart/form-data" class="lg:col-span-2 panel p-6 space-y-4">
            <h3 class="font-display font-semibold text-ink text-sm border-b border-[#F1ECE0] pb-3 mb-2">Student details</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-ink/55 mb-1.5 uppercase tracking-wide">Student Code <span class="text-coral">*</span></label>
                    <input type="text" name="student_code" id="inp_code" placeholder="e.g. ST008"
                           value="<?= htmlspecialchars($_POST['student_code'] ?? '') ?>"
                           class="w-full px-4 py-2.5 bg-sand border border-[#ECE6D9] rounded-xl text-sm font-mono focus:outline-none focus:ring-2 focus:ring-teal-500/30"/>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-ink/55 mb-1.5 uppercase tracking-wide">Full Name <span class="text-coral">*</span></label>
                    <input type="text" name="full_name" placeholder="Full name"
                           value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>"
                           class="w-full px-4 py-2.5 bg-sand border border-[#ECE6D9] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500/30"/>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-ink/55 mb-1.5 uppercase tracking-wide">Gender</label>
                    <select name="gender" class="w-full px-4 py-2.5 bg-sand border border-[#ECE6D9] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500/30">
                        <option value="Male">Male</option><option value="Female">Female</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-ink/55 mb-1.5 uppercase tracking-wide">Class</label>
                    <select name="class_name" class="w-full px-4 py-2.5 bg-sand border border-[#ECE6D9] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500/30">
                        <option value="">— Select class —</option>
                        <?php while ($c = $classes->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($c['class_name']) ?>"><?= htmlspecialchars($c['class_name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-ink/55 mb-1.5 uppercase tracking-wide">Photo</label>
                <input type="file" name="photo" accept="image/*" onchange="previewImg(this)"
                       class="w-full text-sm text-ink/50 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 cursor-pointer"/>
            </div>
            <div class="flex gap-3 pt-3 border-t border-[#F1ECE0]">
                <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white font-semibold px-6 py-2.5 rounded-xl text-sm transition shadow-sm flex items-center gap-2">
                    <i class="fas fa-qrcode text-xs"></i> Save & generate QR
                </button>
                <a href="index.php" class="bg-[#F1ECE0] hover:bg-[#E9E2D2] text-ink/60 font-medium px-6 py-2.5 rounded-xl text-sm transition">Cancel</a>
            </div>
        </form>

        <!-- Live previews -->
        <div class="space-y-5">
            <div class="panel p-5 text-center">
                <p class="text-xs font-semibold text-ink/45 uppercase tracking-wide mb-3">Photo</p>
                <div class="w-28 h-28 mx-auto rounded-2xl border-2 border-dashed border-[#ECE6D9] bg-sand flex items-center justify-center overflow-hidden">
                    <img id="preview" src="#" class="hidden w-full h-full object-cover">
                    <i id="preview-ph" class="fas fa-user text-3xl text-ink/15"></i>
                </div>
            </div>
            <div class="panel p-5 text-center">
                <p class="text-xs font-semibold text-ink/45 uppercase tracking-wide mb-3">QR Preview</p>
                <div class="bg-white border border-[#ECE6D9] rounded-2xl p-3 inline-block">
                    <img id="qr-preview" src="https://api.qrserver.com/v1/create-qr-code/?size=130x130&data=STUDENT" class="w-28 h-28 opacity-20">
                </div>
                <p class="text-ink/35 text-[11px] mt-3"><i class="fas fa-arrows-rotate mr-1"></i>Updates as you type</p>
            </div>
        </div>
    </div>
</main>
<script>
const codeInput = document.getElementById('inp_code'), qrImg = document.getElementById('qr-preview');
codeInput.addEventListener('input', function(){
    const code = this.value.trim();
    qrImg.src = 'https://api.qrserver.com/v1/create-qr-code/?size=130x130&data=' + encodeURIComponent(code || 'STUDENT');
    qrImg.classList.toggle('opacity-20', !code);
});
function previewImg(input){
    if (input.files && input.files[0]) {
        const r = new FileReader();
        r.onload = e => {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('preview').classList.remove('hidden');
            document.getElementById('preview-ph').classList.add('hidden');
        };
        r.readAsDataURL(input.files[0]);
    }
}
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
