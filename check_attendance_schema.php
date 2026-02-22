<?php
require_once __DIR__ . '/config/database.php';

try {
    $stmt = db()->query('DESCRIBE attendance');
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo 'Attendance table columns: ' . implode(', ', $columns) . PHP_EOL;
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}