<?php
session_start();
if (isset($_SESSION['admin_id'])) {
    header('Location: /smart_attendance_v2/admin/dashboard.php');
} else {
    header('Location: /smart_attendance_v2/login.php');
}
exit;
