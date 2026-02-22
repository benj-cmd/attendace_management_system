<?php
// Simple test to check if AJAX endpoint works
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../controllers/UnifiedReportController.php';

require_auth();

// Test the controller directly
$data = UnifiedReportController::getReportData(null, null, '2026-02-01', '2026-02-22', null);

echo json_encode([
    'success' => true,
    'message' => 'Controller working correctly',
    'data_sample' => [
        'summary' => $data['summary'],
        'section_count' => count($data['sectionSummary']),
        'student_count' => count($data['studentBreakdown'])
    ]
]);