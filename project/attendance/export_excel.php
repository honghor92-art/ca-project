<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';

$date = $_GET['date'] ?? '';
$from = $date ? $date : ($_GET['from'] ?? date('Y-m-01'));
$to   = $date ? $date : ($_GET['to'] ?? date('Y-m-d'));
$search = trim($_GET['search'] ?? '');
$from = date('Y-m-d', strtotime($from));
$to   = date('Y-m-d', strtotime($to));

$where = "WHERE a.attendance_date BETWEEN '$from' AND '$to'";
if ($search) { $s = $conn->real_escape_string($search); $where .= " AND (s.full_name LIKE '%$s%' OR s.student_code LIKE '%$s%')"; }

$records = $conn->query("
    SELECT a.attendance_date,a.attendance_time,a.status,s.student_code,s.full_name,s.class_name,s.gender
    FROM attendance a JOIN students s ON a.student_id=s.id $where
    ORDER BY a.attendance_date ASC, a.attendance_time ASC
");

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="attendance_' . $from . '_to_' . $to . '.csv"');
$out = fopen('php://output', 'w');
fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
fputcsv($out, ['No','Student Code','Full Name','Class','Gender','Date','Time','Status']);
$i = 1;
while ($row = $records->fetch_assoc()) {
    fputcsv($out, [$i++, $row['student_code'], $row['full_name'], $row['class_name'], $row['gender'],
        date('d/m/Y', strtotime($row['attendance_date'])), date('h:i:s A', strtotime($row['attendance_time'])), $row['status']]);
}
fclose($out);
