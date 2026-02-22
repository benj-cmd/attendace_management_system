<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/AttendanceReport.php';
require_once __DIR__ . '/models/Attendance.php';

$today = date('Y-m-d');
echo "Testing AttendanceReport::dailyTotals for date: $today\n";

$summary = AttendanceReport::dailyTotals($today);
echo "Report Totals - Present: " . $summary['present'] . ", Absent: " . $summary['absent'] . ", Late: " . $summary['late'] . "\n";

echo "\nTesting Attendance::dailySummaryApproved for date: $today\n";
$summary2 = Attendance::dailySummaryApproved($today);
echo "Direct Attendance Totals - Present: " . $summary2['present'] . ", Absent: " . $summary2['absent'] . ", Late: " . $summary2['late'] . "\n";

// Let's also check the raw data for today from both sources
echo "\nRaw data from attendance table for today:\n";
$stmt = db()->prepare("SELECT status, COUNT(*) as count FROM attendance WHERE date = :date GROUP BY status");
$stmt->execute(['date' => $today]);
$results = $stmt->fetchAll();
foreach ($results as $row) {
    echo $row['status'] . ": " . $row['count'] . "\n";
}

echo "\nRaw data from attendance_report_items for today's reports:\n";
$stmt = db()->prepare(
    "SELECT 
        i.status, 
        COUNT(*) as count 
    FROM attendance_reports r
    JOIN attendance_report_items i ON i.report_id = r.id
    WHERE (r.report_name = ? OR DATE(r.submitted_at) = ?)
    GROUP BY i.status"
);
$stmt->execute([$today, $today]);
$results = $stmt->fetchAll();
foreach ($results as $row) {
    echo $row['status'] . ": " . $row['count'] . "\n";
}