<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
$page_title = 'Edit Student';
$page_subtitle = 'Update student information';

$id = (int)($_GET['id'] ?? 0);
$student = $conn->query("SELECT * FROM students WHERE id=$id")->fetch_assoc();
if (!$student) { header('Location: index.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code   = trim($_POST['student_code'] ?? '');
    $name   = trim($_POST['full_name'] ?? '');
    $gender = $_POST['gender'] ?? 'Male';
    $class  = trim($_POST['class_name'] ?? '');

    $img_name = $student['image_student'];
    if (!empty($_FILES['photo']['name'])) {
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
            $img_name = 'student_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['photo']['tmp_name'], __DIR__ . '/../asset/images/' . $img_name);
        }
    }

    $stmt = $conn->prepare("UPDATE students SET student_code=?,full_name=?,gender=?,class_name=?,image_student=? WHERE id=?");
    $stmt->bind_param('sssssi', $code, $name, $gender, $class, $img_name, $id);
    if ($stmt->execute()) { header('Location: index.php?msg=updated'); exit; }
    else $error = 'Update failed.';
}
$classes = $conn->query("SELECT * FROM classes ORDER BY class_name");
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/sider.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="ml-60 pt-16 p-7 rise-in">
    <a href="index.php" class="inline-flex items-center gap-2 text-ink/45 hover:text-ink text-sm mb-5 transition"><i class="fas fa-arrow-left text-xs"></i> Back to students</a>

    <?php if ($error): ?>
    <div class="bg-coral/10 border border-coral/20 text-coral text-sm rounded-xl px-4 py-3 mb-5"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <form method="POST" enctype="multipart/form-data" class="lg:col-span-2 panel p-6 space-y-4">
            <h3 class="font-display font-semibold text-ink text-sm border-b border-[#F1ECE0] pb-3 mb-2">Student details</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-ink/55 mb-1.5 uppercase tracking-wide">Student Code</label>
                    <input type="text" name="student_code" value="<?= htmlspecialchars($student['student_code']) ?>"
                           class="w-full px-4 py-2.5 bg-sand border border-[#ECE6D9] rounded-xl text-sm font-mono focus:outline-none focus:ring-2 focus:ring-teal-500/30"/>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-ink/55 mb-1.5 uppercase tracking-wide">Full Name</label>
                    <input type="text" name="full_name" value="<?= htmlspecialchars($student['full_name']) ?>"
                           class="w-full px-4 py-2.5 bg-sand border border-[#ECE6D9] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500/30"/>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-ink/55 mb-1.5 uppercase tracking-wide">Gender</label>
                    <select name="gender" class="w-full px-4 py-2.5 bg-sand border border-[#ECE6D9] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500/30">
                        <option value="Male"   <?= $student['gender']==='Male'?'selected':'' ?>>Male</option>
                        <option value="Female" <?= $student['gender']==='Female'?'selected':'' ?>>Female</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-ink/55 mb-1.5 uppercase tracking-wide">Class</label>
                    <select name="class_name" class="w-full px-4 py-2.5 bg-sand border border-[#ECE6D9] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500/30">
                        <?php while ($c = $classes->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($c['class_name']) ?>" <?= $student['class_name']===$c['class_name']?'selected':'' ?>><?= htmlspecialchars($c['class_name']) ?></option>
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
                <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white font-semibold px-6 py-2.5 rounded-xl text-sm transition shadow-sm"><i class="fas fa-save mr-2 text-xs"></i>Update</button>
                <a href="index.php" class="bg-[#F1ECE0] hover:bg-[#E9E2D2] text-ink/60 font-medium px-6 py-2.5 rounded-xl text-sm transition">Cancel</a>
            </div>
        </form>

        <div class="space-y-5">
            <div class="panel p-5 text-center">
                <p class="text-xs font-semibold text-ink/45 uppercase tracking-wide mb-3">Photo</p>
                <div class="w-28 h-28 mx-auto rounded-2xl border-2 border-[#ECE6D9] bg-sand overflow-hidden flex items-center justify-center">
                    <?php if ($student['image_student']): ?>
                    <img id="preview" src="<?= BASE_URL ?>/asset/images/<?= htmlspecialchars($student['image_student']) ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                    <img id="preview" src="#" class="hidden w-full h-full object-cover">
                    <i id="preview-ph" class="fas fa-user text-3xl text-ink/15"></i>
                    <?php endif; ?>
                </div>
            </div>
            <?php if ($student['qr_image']): ?>
            <div class="panel p-5 text-center">
                <p class="text-xs font-semibold text-ink/45 uppercase tracking-wide mb-3">QR Code</p>
                <div class="bg-white border border-[#ECE6D9] rounded-2xl p-3 inline-block">
                    <img src="<?= BASE_URL ?>/asset/images/<?= htmlspecialchars($student['qr_image']) ?>" class="w-28 h-28">
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>
<script>
function previewImg(input){
    if (input.files && input.files[0]) {
        const r = new FileReader();
        r.onload = e => {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('preview').classList.remove('hidden');
            const ph = document.getElementById('preview-ph'); if (ph) ph.classList.add('hidden');
        };
        r.readAsDataURL(input.files[0]);
    }
}
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
