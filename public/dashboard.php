<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../controllers/DashboardController.php';

require_auth();

$pageTitle = 'Dashboard';

$data = DashboardController::index();
$today = $data['today'] ?? date('Y-m-d');
$summary = $data['summary'] ?? ['present' => 0, 'absent' => 0, 'late' => 0];
$studentCount = (int)($data['studentCount'] ?? 0);
$sectionCount = (int)($data['sectionCount'] ?? 0);
$submittedReportCount = (int)($data['submittedReportCount'] ?? 0);

require_once __DIR__ . '/../views/dashboard.php';
