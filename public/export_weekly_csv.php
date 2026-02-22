<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../controllers/ReportController.php';

require_auth();

$date = get('date', date('Y-m-d'));
$data = ReportController::weekly($date);

$weekDate = (string)($data['week_date'] ?? date('Y-m-d'));
$monday = (string)($data['monday'] ?? '');
$sunday = (string)($data['sunday'] ?? '');
$rows = $data['rows'] ?? [];

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="weekly_report_' . $weekDate . '.csv"');

$out = fopen('php://output', 'w');

fputcsv($out, ['Week Range', $monday . ' to ' . $sunday]);
fputcsv($out, []);
fputcsv($out, ['Student', 'Student Number', 'Present', 'Absent', 'Late']);

foreach ($rows as $r) {
    fputcsv($out, [
        (string)$r['fullname'],
        (string)$r['student_number'],
        (string)((int)$r['present_total']),
        (string)((int)$r['absent_total']),
        (string)((int)$r['late_total']),
    ]);
}

fclose($out);
exit;
