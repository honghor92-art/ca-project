<?php
define('ESP32_IP', '192.168.43.100'); // must match local_IP in CameraWebServer.ino
define('ESP32_CAM_URL', 'http://' . ESP32_IP);
define('ESP32_STREAM_URL', 'http://' . ESP32_IP . ':81');

// Fetch a single JPEG photo and save it
function capturePhoto($savePath = 'capture.jpg') {
    $ch = curl_init(ESP32_CAM_URL . '/capture');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $data = curl_exec($ch);
    $err  = curl_error($ch);
    curl_close($ch);
    if ($err || !$data) return false;
    file_put_contents($savePath, $data);
    return $savePath;
}

// Get camera status as an associative array
function getCameraStatus() {
    $json = @file_get_contents(ESP32_CAM_URL . '/status');
    return $json ? json_decode($json, true) : null;
}

// Change a camera setting (var: framesize, quality, brightness, etc.)
function setControl($var, $val) {
    $url = ESP32_CAM_URL . '/control?var=' . urlencode($var) . '&val=' . intval($val);
    @file_get_contents($url);
}

// ── Example usage ────────────────────────────────────────────────────────────

// Show live stream (use directly in HTML, not through PHP)
// <img src="http://192.168.1.100:81/stream">

// Capture a photo and display it
$photo = capturePhoto('capture.jpg');
if ($photo) {
    echo '<img src="capture.jpg">';
} else {
    echo 'Could not reach ESP32-CAM at ' . ESP32_IP;
}

// Show camera status
$status = getCameraStatus();
if ($status) {
    echo '<pre>' . print_r($status, true) . '</pre>';
}
?>
