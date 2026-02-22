<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../controllers/ReportController.php';

require_auth();

$pageTitle = 'Daily Attendance Report';
$date = get('date', date('Y-m-d'));
$data = ReportController::daily($date);

$students = $data['students'] ?? [];
$attendanceByStudentId = $data['attendanceByStudentId'] ?? [];
$summary = $data['summary'] ?? ['present' => 0, 'absent' => 0, 'late' => 0];
$date = $data['date'] ?? $date;

require_once __DIR__ . '/../views/reports/daily.php';
