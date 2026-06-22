-- ==========================================
-- Smart Attendance System v2 - Database
-- ==========================================
CREATE DATABASE IF NOT EXISTS smart_attendance_v2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE smart_attendance_v2;

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(150) DEFAULT 'Administrator',
    email VARCHAR(150) DEFAULT '',
    school_name VARCHAR(200) DEFAULT 'SETEC Institute',
    school_address VARCHAR(300) DEFAULT 'Phnom Penh, Cambodia',
    school_phone VARCHAR(50) DEFAULT '012 345 678',
    school_email VARCHAR(150) DEFAULT 'info@setec.edu.kh',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255) DEFAULT ''
);

CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_code VARCHAR(20) NOT NULL UNIQUE,
    full_name VARCHAR(150) NOT NULL,
    gender ENUM('Male','Female') DEFAULT 'Male',
    class_name VARCHAR(50) DEFAULT '',
    phone VARCHAR(50) DEFAULT '',
    qr_image VARCHAR(255) DEFAULT '',
    image_student VARCHAR(255) DEFAULT '',
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    attendance_date DATE NOT NULL,
    attendance_time TIME NOT NULL,
    status ENUM('Present','Late','Absent') DEFAULT 'Present',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    UNIQUE KEY unique_attendance (student_id, attendance_date)
);

-- Default admin (password: admin123)
INSERT INTO admins (username, password, full_name, email) VALUES
('sinh', 'snhna15042024', 'Administrator', 'admin@gmail.com')
ON DUPLICATE KEY UPDATE username=username;

INSERT INTO classes (class_name, description) VALUES
('IT-1A','Year 1 Information Technology A'),
('IT-1B','Year 1 Information Technology B'),
('IT-2A','Year 2 Information Technology A'),
('IT-2B','Year 2 Information Technology B'),
('IT-3A','Year 3 Information Technology A'),
('CS-1','Computer Science Year 1'),
('CS-2','Computer Science Year 2')
ON DUPLICATE KEY UPDATE class_name=class_name;

INSERT INTO students (student_code, full_name, gender, class_name) VALUES
('ST001','YI YONGSINH','Male','IT-2A'),
('ST002','SOK SREYNEANG','Female','IT-2A'),
('ST003','CHHORN DAVID','Male','IT-2B'),
('ST004','NUTH SOPHEAK','Male','IT-2A'),
('ST005','TEP SOKHIM','Female','IT-2B'),
('ST006','HENG CHANTHA','Female','IT-1A'),
('ST007','VANN BORA','Male','CS-1')
ON DUPLICATE KEY UPDATE student_code=student_code;

-- sample attendance for last few days
INSERT INTO attendance (student_id, attendance_date, attendance_time, status) VALUES
(1, CURDATE(), '07:55:12', 'Present'),
(2, CURDATE(), '07:58:30', 'Present'),
(3, CURDATE(), '08:05:00', 'Late'),
(4, CURDATE(), '07:50:11', 'Present'),
(1, CURDATE()-1, '07:52:00', 'Present'),
(2, CURDATE()-1, '08:10:00', 'Late'),
(3, CURDATE()-1, '07:49:00', 'Present'),
(5, CURDATE()-1, '07:55:00', 'Present'),
(1, CURDATE()-2, '07:53:00', 'Present'),
(4, CURDATE()-2, '07:58:00', 'Present')
ON DUPLICATE KEY UPDATE status=status;

SELECT 'Smart Attendance v2 DB ready!' AS message;
