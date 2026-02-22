<?php
require_once __DIR__ . '/config/database.php';

echo "Checking attendance table structure...\n";
try {
    $stmt = db()->query('DESCRIBE attendance');
    $attendance_cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo 'Attendance table columns: ' . implode(', ', $attendance_cols) . "\n";
} catch (Exception $e) {
    echo 'Error accessing attendance table: ' . $e->getMessage() . "\n";
}

echo "\nChecking attendance_reports table structure...\n";
try {
    $stmt = db()->query('DESCRIBE attendance_reports');
    $reports_cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo 'Attendance_reports table columns: ' . implode(', ', $reports_cols) . "\n";
} catch (Exception $e) {
    echo 'Error accessing attendance_reports table: ' . $e->getMessage() . "\n";
}

echo "\nChecking attendance_report_items table structure...\n";
try {
    $stmt = db()->query('DESCRIBE attendance_report_items');
    $items_cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo 'Attendance_report_items table columns: ' . implode(', ', $items_cols) . "\n";
} catch (Exception $e) {
    echo 'Error accessing attendance_report_items table: ' . $e->getMessage() . "\n";
}