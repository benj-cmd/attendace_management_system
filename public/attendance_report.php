<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../controllers/UnifiedReportController.php';

require_auth();

$pageTitle = 'Attendance Report';

// Get filter parameters
$sectionId = get('section_id', '');
$studentId = get('student_id', '');
$dateFrom = get('date_from', '');
$dateTo = get('date_to', '');

// Convert empty strings to null for proper filtering
$sectionId = $sectionId ? (int)$sectionId : null;
$studentId = $studentId ? (int)$studentId : null;
$dateFrom = $dateFrom ? $dateFrom : null;
$dateTo = $dateTo ? $dateTo : null;

$data = UnifiedReportController::getReportData($sectionId, $studentId, $dateFrom, $dateTo);

// Extract data for the view
$sections = $data['sections'];
$studentOptions = $data['studentOptions'];
$attendanceData = $data['attendanceData'];
$summary = $data['summary'];
$studentBreakdown = $data['studentBreakdown'];
$dateRangeSummary = $data['dateRangeSummary'];
$filters = $data['filters'];

require_once __DIR__ . '/../views/reports/unified.php';