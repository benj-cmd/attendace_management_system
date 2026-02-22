<?php
require_once __DIR__ . '/config/database.php';

try {
    $pdo = db();
    
    echo "=== Database Status Check ===\n";
    
    // Check attendance table
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM attendance');
    $result = $stmt->fetch();
    echo "Total attendance records: " . $result['count'] . "\n";
    
    if ($result['count'] > 0) {
        echo "Recent attendance records:\n";
        $stmt = $pdo->query('SELECT a.*, s.first_name, s.last_name FROM attendance a JOIN students s ON a.student_id = s.id ORDER BY a.date DESC, a.created_at DESC LIMIT 10');
        $records = $stmt->fetchAll();
        foreach ($records as $record) {
            echo "  Date: " . $record['date'] . " | Student: " . $record['first_name'] . " " . $record['last_name'] . " | Status: " . $record['status'] . " | Time: " . $record['time_marked'] . "\n";
        }
    }
    
    // Check section_students table
    echo "\nSection-Student relationships:\n";
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM section_students');
    $result = $stmt->fetch();
    echo "Total section-student relationships: " . $result['count'] . "\n";
    
    if ($result['count'] > 0) {
        $stmt = $pdo->query('SELECT sec.name as section_name, s.first_name, s.last_name FROM section_students ss JOIN sections sec ON ss.section_id = sec.id JOIN students s ON ss.student_id = s.id LIMIT 10');
        $relationships = $stmt->fetchAll();
        foreach ($relationships as $rel) {
            echo "  " . $rel['section_name'] . " - " . $rel['first_name'] . " " . $rel['last_name'] . "\n";
        }
    }
    
    // Check if data exists for reports
    echo "\n=== Report Data Availability ===\n";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM attendance WHERE date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
    $result = $stmt->fetch();
    echo "Attendance records in last 30 days: " . $result['count'] . "\n";
    
    // Check attendance reports
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM attendance_reports');
    $result = $stmt->fetch();
    echo "Total attendance reports: " . $result['count'] . "\n";
    
    if ($result['count'] > 0) {
        echo "Recent attendance reports:\n";
        $stmt = $pdo->query('SELECT r.*, s.name as section_name FROM attendance_reports r JOIN sections s ON r.section_id = s.id ORDER BY r.submitted_at DESC LIMIT 5');
        $reports = $stmt->fetchAll();
        foreach ($reports as $report) {
            echo "  Report: " . $report['report_name'] . " | Section: " . $report['section_name'] . " | Submitted: " . $report['submitted_at'] . "\n";
        }
    }
    
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM students');
    $result = $stmt->fetch();
    echo "Total students: " . $result['count'] . "\n";
    
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM sections');
    $result = $stmt->fetch();
    echo "Total sections: " . $result['count'] . "\n";
    
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}