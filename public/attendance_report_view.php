<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../controllers/AttendanceReportController.php';

require_auth();

$id = (int)get('id', '0');
if ($id <= 0) {
    redirect('attendance_reports.php');
}

$pageTitle = 'Attendance Report';
$data = AttendanceReportController::view($id);
$report = $data['report'] ?? null;

require_once __DIR__ . '/../views/attendance_reports/view.php';
