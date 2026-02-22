<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/Attendance.php';

$today = date('Y-m-d');
echo "Today's date: $today\n\n";

// Test the daily summary
$summary = Attendance::dailySummaryApproved($today);
echo "Today's Attendance Summary:\n";
echo "Present: " . $summary['present'] . "\n";
echo "Absent: " . $summary['absent'] . "\n";
echo "Late: " . $summary['late'] . "\n\n";

// Check raw attendance data
$stmt = db()->prepare("SELECT status, COUNT(*) as count FROM attendance WHERE date = :date GROUP BY status");
$stmt->execute(['date' => $today]);
$results = $stmt->fetchAll();

echo "Raw attendance data for today:\n";
foreach ($results as $row) {
    echo $row['status'] . ": " . $row['count'] . "\n";
}

if (empty($results)) {
    echo "No attendance records found for today.\n";
    
    // Check if there are any attendance records at all
    $stmt = db()->query("SELECT date, status, COUNT(*) as count FROM attendance GROUP BY date, status ORDER BY date DESC LIMIT 5");
    $allRecords = $stmt->fetchAll();
    
    if (empty($allRecords)) {
        echo "No attendance records found in the database at all.\n";
    } else {
        echo "\nRecent attendance records:\n";
        foreach ($allRecords as $record) {
            echo $record['date'] . " - " . $record['status'] . ": " . $record['count'] . "\n";
        }
    }
}