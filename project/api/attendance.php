<?php
/**
 * Pulse Attendance - ESP32-CAM API
 * GET /smart_attendance_v2/api/attendance.php?student_code=ST001
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/db.php';

// Accept form POST, GET query string, or raw JSON body
$student_code = trim($_GET['student_code'] ?? $_POST['student_code'] ?? '');
if (empty($student_code)) {
    $body = json_decode(file_get_contents('php://input'), true);
    $student_code = trim($body['student_code'] ?? $body['id'] ?? '');
}
if (empty($student_code)) {
    echo json_encode(['status'=>'error','message'=>'student_code is required']); exit;
}

$stmt = $conn->prepare("SELECT * FROM students WHERE student_code = ?");
$stmt->bind_param('s', $student_code);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    echo json_encode(['status'=>'error','message'=>'Student not found','code'=>$student_code]); exit;
}

$today = date('Y-m-d');
$time  = date('H:i:s');
$status = (strtotime($time) > strtotime('08:00:00')) ? 'Late' : 'Present';

$check = $conn->prepare("SELECT * FROM attendance WHERE student_id=? AND attendance_date=?");
$check->bind_param('is', $student['id'], $today);
$check->execute();
$existing = $check->get_result()->fetch_assoc();

if ($existing) {
    echo json_encode([
        'status'=>'already','message'=>'Attendance already recorded today',
        'student_code'=>$student['student_code'],'name'=>$student['full_name'],
        'time'=>$existing['attendance_time'],'date'=>$today
    ]); exit;
}

$ins = $conn->prepare("INSERT INTO attendance (student_id, attendance_date, attendance_time, status) VALUES (?,?,?,?)");
$ins->bind_param('isss', $student['id'], $today, $time, $status);

if ($ins->execute()) {
    echo json_encode([
        'status'=>'success','message'=>'Attendance saved',
        'student_code'=>$student['student_code'],'name'=>$student['full_name'],
        'class'=>$student['class_name'],'time'=>$time,'date'=>$today,'attendance_status'=>$status
    ]);
} else {
    echo json_encode(['status'=>'error','message'=>'Failed to save attendance']);
}
