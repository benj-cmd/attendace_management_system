<?php
// Comprehensive debug script for the unified report AJAX system
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/controllers/UnifiedReportController.php';

echo "=== Unified Report AJAX Debug Test ===\n\n";

// Test 1: Check if all required files exist
echo "1. File existence check:\n";
$files = [
    'controllers/UnifiedReportController.php',
    'models/Student.php', 
    'models/Section.php',
    'models/Attendance.php',
    'config/database.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "  ✓ $file exists\n";
    } else {
        echo "  ✗ $file NOT FOUND\n";
    }
}

echo "\n2. Database connection test:\n";
try {
    $pdo = db();
    echo "  ✓ Database connection successful\n";
    
    // Test basic queries
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM students');
    $result = $stmt->fetch();
    echo "  ✓ Students table: " . $result['count'] . " records\n";
    
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM sections');
    $result = $stmt->fetch();
    echo "  ✓ Sections table: " . $result['count'] . " records\n";
    
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM attendance');
    $result = $stmt->fetch();
    echo "  ✓ Attendance table: " . $result['count'] . " records\n";
    
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM section_students');
    $result = $stmt->fetch();
    echo "  ✓ Section_students table: " . $result['count'] . " records\n";
    
} catch (Exception $e) {
    echo "  ✗ Database connection failed: " . $e->getMessage() . "\n";
    exit;
}

echo "\n3. Controller method test:\n";
try {
    // Test the getReportData method
    $data = UnifiedReportController::getReportData(null, null, '2026-02-01', '2026-02-22', null);
    echo "  ✓ getReportData() executed successfully\n";
    echo "  ✓ Summary data: Present=" . $data['summary']['present'] . ", Absent=" . $data['summary']['absent'] . ", Late=" . $data['summary']['late'] . "\n";
    echo "  ✓ Section summary count: " . count($data['sectionSummary']) . "\n";
    echo "  ✓ Student breakdown count: " . count($data['studentBreakdown']) . "\n";
    
} catch (Exception $e) {
    echo "  ✗ Controller method failed: " . $e->getMessage() . "\n";
    echo "  Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n4. Student model findBySection test:\n";
try {
    require_once __DIR__ . '/models/Student.php';
    $students = Student::findBySection(1); // Test with section 1
    echo "  ✓ findBySection(1) returned " . count($students) . " students\n";
} catch (Exception $e) {
    echo "  ✗ Student::findBySection failed: " . $e->getMessage() . "\n";
}

echo "\n5. Simulating AJAX request:\n";
try {
    // Simulate what the AJAX endpoint does
    $_POST = [
        'section_id' => '',
        'student_id' => '',
        'date_from' => '2026-02-01',
        'date_to' => '2026-02-22',
        'status' => ''
    ];
    
    $sectionId = $_POST['section_id'] ? (int)$_POST['section_id'] : null;
    $studentId = $_POST['student_id'] ? (int)$_POST['student_id'] : null;
    $dateFrom = $_POST['date_from'];
    $dateTo = $_POST['date_to'];
    $status = $_POST['status'];
    
    echo "  Input parameters: section_id=$sectionId, student_id=$studentId, date_from=$dateFrom, date_to=$dateTo, status=$status\n";
    
    $data = UnifiedReportController::getReportData($sectionId, $studentId, $dateFrom, $dateTo, $status);
    
    $response = [
        'success' => true,
        'summary' => $data['summary'],
        'sectionSummary' => $data['sectionSummary'],
        'studentBreakdown' => $data['studentBreakdown'],
        'dateRangeSummary' => $data['dateRangeSummary'],
        'filters' => $data['filters']
    ];
    
    echo "  ✓ AJAX simulation successful\n";
    echo "  ✓ Response structure is valid\n";
    echo "  ✓ JSON encoding test: " . (json_encode($response) ? "PASS" : "FAIL") . "\n";
    
} catch (Exception $e) {
    echo "  ✗ AJAX simulation failed: " . $e->getMessage() . "\n";
    echo "  Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Debug Complete ===\n";