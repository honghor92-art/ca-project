<?php
    session_start();
    session_destroy();
    header('Location: /smart_attendance_v2/login.php');
exit;
