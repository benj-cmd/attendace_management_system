<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../controllers/UnifiedReportController.php';

require_auth();

// Enable detailed error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', '1');

header('Content-Type: application/json');

// Log the request
error_log('AJAX Request started at ' . date('Y-m-d H:i:s'));
error_log('POST data: ' . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log('Invalid method: ' . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    // Get POST parameters
    $sectionId = post('section_id', '');
    $studentId = post('student_id', '');
    $dateFrom = post('date_from', '');
    $dateTo = post('date_to', '');

    // Convert empty strings to null for proper filtering
    $sectionId = $sectionId ? (int)$sectionId : null;
    $studentId = $studentId ? (int)$studentId : null;
    $dateFrom = $dateFrom ? $dateFrom : null;
    $dateTo = $dateTo ? $dateTo : null;

    error_log("Raw POST data - section_id: '$sectionId', student_id: '$studentId', date_from: '$dateFrom', date_to: '$dateTo'");
    
    error_log("Processed parameters - section_id: " . var_export($sectionId, true) . ", student_id: " . var_export($studentId, true));
    
    // Get filtered data
    error_log("Calling UnifiedReportController::getReportData()");
    $data = UnifiedReportController::getReportData($sectionId, $studentId, $dateFrom, $dateTo);
    error_log("Controller returned data successfully");
    
    // Log the data structure
    error_log("Data keys: " . implode(', ', array_keys($data)));
    error_log("Student breakdown count: " . count($data['studentBreakdown']));
    
    // Return only the data needed for the AJAX response
    $response = [
        'success' => true,
        'summary' => $data['summary'],
        'studentBreakdown' => $data['studentBreakdown'],
        'dateRangeSummary' => $data['dateRangeSummary'],
        'filters' => $data['filters']
    ];
    
    error_log("Response prepared, sending JSON");
    echo json_encode($response);
    error_log("JSON sent successfully");
    
} catch (Exception $e) {
    error_log('AJAX Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
    error_log('Stack trace: ' . $e->getTraceAsString());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'An error occurred while processing your request: ' . $e->getMessage()
    ]);
    
    error_log("Error response sent");
}