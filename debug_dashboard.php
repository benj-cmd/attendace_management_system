<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/controllers/DashboardController.php';

// Temporarily bypass auth for testing
// require_auth();

echo "Testing Dashboard Controller...\n";

$data = DashboardController::index();
$today = $data['today'] ?? date('Y-m-d');
$summary = $data['summary'] ?? ['present' => 0, 'absent' => 0, 'late' => 0];
$studentCount = (int)($data['studentCount'] ?? 0);
$sectionCount = (int)($data['sectionCount'] ?? 0);
$submittedReportCount = (int)($data['submittedReportCount'] ?? 0);

echo "Today: " . $today . "\n";
echo "Summary: \n";
var_dump($summary);
echo "Student Count: " . $studentCount . "\n";
echo "Section Count: " . $sectionCount . "\n";
echo "Submitted Report Count: " . $submittedReportCount . "\n";

echo "\nExpected values in dashboard view:\n";
echo "Present: " . (int)$summary['present'] . "\n";
echo "Absent: " . (int)$summary['absent'] . "\n";
echo "Late: " . (int)$summary['late'] . "\n";