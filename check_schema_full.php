<?php
require_once __DIR__ . '/config/database.php';

echo "Students table columns:\n";
$stmt = db()->query('DESCRIBE students');
$columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
foreach ($columns as $col) {
    echo "  - " . $col . "\n";
}

echo "\nSections table columns:\n";
$stmt = db()->query('DESCRIBE sections');
$columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
foreach ($columns as $col) {
    echo "  - " . $col . "\n";
}

echo "\nChecking relationships:\n";
// Check if there's a junction table
try {
    $stmt = db()->query('SHOW TABLES LIKE "%section%"');
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Section-related tables: " . implode(', ', $tables) . "\n";
} catch (Exception $e) {
    echo "Error checking tables: " . $e->getMessage() . "\n";
}