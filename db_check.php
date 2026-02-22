<?php
require_once __DIR__ . '/config/database.php';

$stmt = db()->query('SELECT COUNT(*) as total FROM attendance');
$result = $stmt->fetch();
echo 'Total attendance records: ' . $result['total'] . "\n";

$stmt = db()->query('SELECT COUNT(*) as total FROM students');
$result = $stmt->fetch();
echo 'Total students: ' . $result['total'] . "\n";

$stmt = db()->query('SELECT COUNT(*) as total FROM sections');
$result = $stmt->fetch();
echo 'Total sections: ' . $result['total'] . "\n";