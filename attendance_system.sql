-- Attendance Management System (Pure PHP + MySQL)
-- Create DB + tables + seed admin

CREATE DATABASE IF NOT EXISTS attendance_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE attendance_system;

DROP TABLE IF EXISTS attendance_report_items;
DROP TABLE IF EXISTS attendance_reports;
DROP TABLE IF EXISTS section_students;
DROP TABLE IF EXISTS sections;
DROP TABLE IF EXISTS attendance;
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS admins;

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(150) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'instructor') NOT NULL DEFAULT 'instructor',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(80) NOT NULL,
    middle_name VARCHAR(80) NULL,
    last_name VARCHAR(80) NOT NULL,
    address VARCHAR(255) NOT NULL,
    email VARCHAR(190) NOT NULL,
    contact_number VARCHAR(30) NOT NULL,
    student_number VARCHAR(20) NOT NULL UNIQUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    status ENUM('Present','Absent','Late') NOT NULL,
    approval_status ENUM('Pending','Approved') NOT NULL DEFAULT 'Pending',
    date DATE NOT NULL,
    time_marked TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    approved_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY uq_attendance_student_date (student_id, date),
    CONSTRAINT fk_attendance_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL UNIQUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE instructor_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    instructor_id INT NOT NULL,
    section_id INT NOT NULL,
    assigned_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    assigned_by INT NULL,
    UNIQUE KEY unique_instructor_section (instructor_id, section_id),
    CONSTRAINT fk_instructor_sections_instructor FOREIGN KEY (instructor_id) REFERENCES admins(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_instructor_sections_section FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_instructor_sections_assigned_by FOREIGN KEY (assigned_by) REFERENCES admins(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE section_students (
    section_id INT NOT NULL,
    student_id INT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (section_id, student_id),
    CONSTRAINT fk_section_students_section FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_section_students_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE attendance_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_id INT NOT NULL,
    report_name VARCHAR(120) NOT NULL,
    submitted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    admin_id INT NULL,
    CONSTRAINT fk_attendance_reports_section FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_attendance_reports_admin FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE attendance_report_items (
    report_id INT NOT NULL,
    student_id INT NOT NULL,
    status ENUM('Present','Absent','Late') NOT NULL,
    PRIMARY KEY (report_id, student_id),
    CONSTRAINT fk_report_items_report FOREIGN KEY (report_id) REFERENCES attendance_reports(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_report_items_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Sample admin:
-- Email: admin@example.com
-- Insert default super admin account
INSERT INTO admins (fullname, email, password, role) 
VALUES ('Super Admin', 'admin@attendance.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin');


