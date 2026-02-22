<?php
require_once __DIR__ . '/config/database.php';

try {
    $pdo = db();
    echo '=== attendance_report_items table structure ===' . PHP_EOL;
    $stmt = $pdo->query('DESCRIBE attendance_report_items');
    $columns = $stmt->fetchAll();
    foreach ($columns as $col) {
        echo $col['Field'] . ' ' . $col['Type'] . ' ' . ($col['Key'] ? $col['Key'] : '') . PHP_EOL;
    }
    
    echo PHP_EOL . '=== attendance_reports table structure ===' . PHP_EOL;
    $stmt = $pdo->query('DESCRIBE attendance_reports');
    $columns = $stmt->fetchAll();
    foreach ($columns as $col) {
        echo $col['Field'] . ' ' . $col['Type'] . ' ' . ($col['Key'] ? $col['Key'] : '') . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}