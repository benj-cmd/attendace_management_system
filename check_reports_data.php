<?php
require_once __DIR__ . '/config/database.php';

echo 'Checking attendance reports data...' . PHP_EOL;
$stmt = db()->query('SELECT COUNT(*) as total FROM attendance_reports');
$result = $stmt->fetch();
echo 'Total attendance reports: ' . $result['total'] . PHP_EOL;

$stmt = db()->query('SELECT COUNT(*) as total FROM attendance_report_items');
$result = $stmt->fetch();
echo 'Total attendance report items: ' . $result['total'] . PHP_EOL;

if ($result['total'] > 0) {
    $stmt = db()->query('SELECT * FROM attendance_report_items LIMIT 5');
    $items = $stmt->fetchAll();
    echo 'Sample report items:' . PHP_EOL;
    foreach ($items as $item) {
        print_r($item);
    }
    
    // Check the latest report
    $stmt = db()->query('SELECT * FROM attendance_reports ORDER BY submitted_at DESC LIMIT 1');
    $latest_report = $stmt->fetch();
    if ($latest_report) {
        echo 'Latest report:' . PHP_EOL;
        print_r($latest_report);
        
        // Check items for the latest report
        $stmt = db()->prepare('SELECT * FROM attendance_report_items WHERE report_id = :report_id');
        $stmt->execute(['report_id' => $latest_report['id']]);
        $report_items = $stmt->fetchAll();
        echo 'Items in latest report:' . PHP_EOL;
        foreach ($report_items as $item) {
            print_r($item);
        }
    }
} else {
    echo "No attendance report items found." . PHP_EOL;
}