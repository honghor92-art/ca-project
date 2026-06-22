# Pulse Attendance v2
**PHP + MySQL + ESP32-CAM | Redesigned UI (Tailwind, Sora/Inter)**

A complete restyle of the Smart Attendance System: deep teal + warm amber palette,
a live-scan login hero, animated dashboard charts, and a cleaner data layer.

---

## Installation

1. Copy the `smart_attendance_v2` folder into your web root:
   `C:/xampp/htdocs/smart_attendance_v2`
2. Open phpMyAdmin → Import `database.sql` (creates DB `smart_attendance_v2` automatically)
3. Check `config/db.php` matches your MySQL credentials
4. Create the images folder if missing: `asset/images/`
5. Visit `http://localhost/smart_attendance_v2/login.php`

**Default login:** `admin` / `admin123`

---

## What's new vs v1
- New visual identity: deep teal (#114A42) + amber (#F2A93B) instead of generic blue/indigo
- Sora display font for headings, Inter for body, JetBrains Mono for codes/timestamps
- Login page has an animated "QR scan" hero panel
- Dashboard adds a Late-arrival stat and a live clock in the navbar
- Attendance status now supports Present / Late / Absent (auto-detected by check-in time)
- QR generation page redesigned as a print-ready badge grid

---

## ESP32-CAM
Update `esp32cam_qr_scanner.ino` with your WiFi + server IP, flash to an
AI-Thinker ESP32-CAM, and it will POST scanned codes to:
```
GET /smart_attendance_v2/api/attendance.php?student_code=ST001
```

## File Map
```
smart_attendance_v2/
├── config/db.php
├── includes/        header, sider, navbar, footer, auth
├── admin/            dashboard, profile, settings
├── students/          index, add, edit, generate_qr
├── attendance/        today, report, export_excel
├── api/                attendance.php (ESP32 endpoint)
├── login.php / logout.php / index.php
└── database.sql
```
