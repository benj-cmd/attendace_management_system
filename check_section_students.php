<?php
require_once __DIR__ . '/config/database.php';

echo "Section_students table columns:\n";
$stmt = db()->query('DESCRIBE section_students');
$columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
foreach ($columns as $col) {
    echo "  - " . $col . "\n";
}

echo "\nSample data from section_students:\n";
$stmt = db()->query('SELECT * FROM section_students LIMIT 5');
$rows = $stmt->fetchAll();
foreach ($rows as $row) {
    print_r($row);
}