<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/controllers/UnifiedReportController.php';

// Test the controller directly to see what SQL is being generated
echo "Testing UnifiedReportController...\n";

// Test with section filter
try {
    $reflection = new ReflectionClass('UnifiedReportController');
    $method = $reflection->getMethod('getFilteredAttendance');
    $method->setAccessible(true);
    
    // This will show us the actual SQL being generated
    echo "Controller method exists and is accessible\n";
    
    // Let's also check the file contents directly
    $fileContents = file_get_contents(__DIR__ . '/controllers/UnifiedReportController.php');
    if (strpos($fileContents, 'AND s.section_id = :section_id') !== false) {
        echo "ERROR: Found old s.section_id reference in file!\n";
        echo "Line containing the issue:\n";
        $lines = explode("\n", $fileContents);
        foreach ($lines as $lineNum => $line) {
            if (strpos($line, 's.section_id') !== false) {
                echo "Line " . ($lineNum + 1) . ": " . $line . "\n";
            }
        }
    } else {
        echo "File appears to be correct - no s.section_id references found\n";
    }
    
    if (strpos($fileContents, 'AND ss.section_id = :section_id') !== false) {
        echo "SUCCESS: Found correct ss.section_id reference\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}