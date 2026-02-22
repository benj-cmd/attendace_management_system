<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../controllers/ReportController.php';

require_auth();

$date = get('date', date('Y-m-d'));
$data = ReportController::daily($date);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="daily_report_' . $date . '.csv"');

$out = fopen('php://output', 'w');

fputcsv($out, ['Date', $date]);
fputcsv($out, []);
fputcsv($out, ['Student', 'Student Number', 'Status', 'Time Marked']);

foreach (($data['students'] ?? []) as $s) {
    $sid = (int)$s['id'];
    $a = ($data['attendanceByStudentId'] ?? [])[$sid] ?? null;
    fputcsv($out, [
        (string)$s['fullname'],
        (string)$s['student_number'],
        (string)($a['status'] ?? 'Not Marked'),
        (string)($a['time_marked'] ?? ''),
    ]);
}

fclose($out);
exit;
