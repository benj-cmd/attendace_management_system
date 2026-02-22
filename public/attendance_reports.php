<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../controllers/AttendanceReportController.php';

require_auth();

$pageTitle = 'Attendance Reports';
$data = AttendanceReportController::index();
$sections = $data['sections'] ?? [];
$reports = $data['reports'] ?? [];
$sectionId = $data['sectionId'] ?? 0;

$tab = $data['tab'] ?? 'submitted';
$breakdownType = $data['breakdownType'] ?? 'daily';
$date = $data['date'] ?? date('Y-m-d');
$weekStart = $data['weekStart'] ?? '';
$weekEnd = $data['weekEnd'] ?? '';
$dailyRows = $data['dailyRows'] ?? [];
$weeklyRows = $data['weeklyRows'] ?? [];
$weeklyStudentRows = $data['weeklyStudentRows'] ?? [];

$from = $weekStart;
$to = $weekEnd;

require_once __DIR__ . '/../views/attendance_reports/index.php';
