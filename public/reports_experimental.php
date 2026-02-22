<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../controllers/ReportController.php';
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Attendance.php';

require_auth();

$pageTitle = 'Experimental: Student Performance Overview';
$date = get('date', date('Y-m-d'));

// Get data for the experimental report
$students = Student::all();
$attendanceData = [];

// Get attendance data for the last 30 days for each student
$endDate = $date;
$startDate = date('Y-m-d', strtotime('-30 days', strtotime($endDate)));

foreach ($students as $student) {
    $studentId = (int)$student['id'];
    
    // Get attendance records for this student in the last 30 days
    $stmt = db()->prepare(
        "SELECT date, status 
         FROM attendance 
         WHERE student_id = :student_id 
         AND date BETWEEN :start_date AND :end_date 
         ORDER BY date DESC"
    );
    $stmt->execute([
        'student_id' => $studentId,
        'start_date' => $startDate,
        'end_date' => $endDate
    ]);
    
    $records = $stmt->fetchAll();
    
    // Calculate statistics
    $totalDays = count($records);
    $presentCount = 0;
    $absentCount = 0;
    $lateCount = 0;
    $attendanceRate = 0;
    
    foreach ($records as $record) {
        switch ($record['status']) {
            case 'Present':
                $presentCount++;
                break;
            case 'Absent':
                $absentCount++;
                break;
            case 'Late':
                $lateCount++;
                break;
        }
    }
    
    if ($totalDays > 0) {
        $attendanceRate = round(($presentCount / $totalDays) * 100, 1);
    }
    
    // Get recent pattern (last 7 days)
    $recentRecords = array_slice($records, 0, 7);
    $recentPattern = [];
    foreach ($recentRecords as $record) {
        $recentPattern[] = substr($record['status'], 0, 1); // P, A, L
    }
    
    $attendanceData[] = [
        'student' => $student,
        'total_days' => $totalDays,
        'present' => $presentCount,
        'absent' => $absentCount,
        'late' => $lateCount,
        'attendance_rate' => $attendanceRate,
        'recent_pattern' => $recentPattern,
        'trend' => $attendanceRate >= 90 ? 'excellent' : 
                  ($attendanceRate >= 80 ? 'good' : 
                  ($attendanceRate >= 70 ? 'fair' : 'poor'))
    ];
}

// Overall statistics
$overallStats = [
    'total_students' => count($students),
    'avg_attendance_rate' => count($attendanceData) > 0 ? 
        round(array_sum(array_column($attendanceData, 'attendance_rate')) / count($attendanceData), 1) : 0,
    'excellent_students' => count(array_filter($attendanceData, fn($d) => $d['trend'] === 'excellent')),
    'good_students' => count(array_filter($attendanceData, fn($d) => $d['trend'] === 'good')),
    'needs_improvement' => count(array_filter($attendanceData, fn($d) => $d['trend'] === 'fair' || $d['trend'] === 'poor'))
];

require_once __DIR__ . '/../views/reports/experimental.php';