<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../controllers/ReportController.php';

require_auth();

$pageTitle = 'Weekly Attendance Report';
$date = get('date', date('Y-m-d'));
$data = ReportController::weekly($date);

$week_date = $data['week_date'] ?? date('Y-m-d');
$monday = $data['monday'] ?? '';
$sunday = $data['sunday'] ?? '';
$rows = $data['rows'] ?? [];

require_once __DIR__ . '/../views/reports/weekly.php';
