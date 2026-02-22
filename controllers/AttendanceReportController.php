<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/AttendanceReport.php';
require_once __DIR__ . '/../models/Section.php';

final class AttendanceReportController
{
    public static function index(): array
    {
        $sectionId = (int)get('section_id', '0');
        $sections = Section::all();
        $reports = AttendanceReport::list($sectionId > 0 ? $sectionId : null);

        $tab = get('tab', 'submitted');
        if (!in_array($tab, ['submitted', 'breakdown'], true)) {
            $tab = 'submitted';
        }

        $breakdownType = get('breakdown', 'daily');
        if (!in_array($breakdownType, ['daily', 'weekly', 'weekly_students'], true)) {
            $breakdownType = 'daily';
        }

        $today = (new DateTimeImmutable('today'))->format('Y-m-d');
        $date = get('date', $today);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = $today;
        }

        if ($date > $today) {
            $date = $today;
        }

        $dailyRows = [];
        $weeklyRows = [];
        $weeklyStudentRows = [];
        $weekStart = '';
        $weekEnd = '';

        if ($breakdownType === 'daily') {
            $dailyRows = AttendanceReport::dailyBreakdownBySection($date, $sectionId > 0 ? $sectionId : null);
        } else {
            $weekStart = get('from', '');
            $weekEnd = get('to', '');

            $fromOk = preg_match('/^\d{4}-\d{2}-\d{2}$/', $weekStart) === 1;
            $toOk = preg_match('/^\d{4}-\d{2}-\d{2}$/', $weekEnd) === 1;

            if (!$fromOk || !$toOk) {
                try {
                    $base = new DateTimeImmutable($date);
                } catch (Throwable $e) {
                    $base = new DateTimeImmutable($today);
                }

                $weekStartObj = $base->modify('monday this week');
                $weekEndObj = $weekStartObj->modify('+6 days');
                $weekStart = $weekStartObj->format('Y-m-d');
                $weekEnd = $weekEndObj->format('Y-m-d');
            }

            if ($weekStart > $today) {
                $weekStart = $today;
            }
            if ($weekEnd > $today) {
                $weekEnd = $today;
            }
            if ($weekEnd < $weekStart) {
                $weekEnd = $weekStart;
            }

            if ($breakdownType === 'weekly_students') {
                $weeklyStudentRows = AttendanceReport::weeklySummaryByStudent($weekStart, $weekEnd, $sectionId > 0 ? $sectionId : null);
            } else {
                $weeklyRows = AttendanceReport::weeklyBreakdownBySection($weekStart, $weekEnd, $sectionId > 0 ? $sectionId : null);
            }
        }

        return compact(
            'sections',
            'reports',
            'sectionId',
            'tab',
            'breakdownType',
            'date',
            'weekStart',
            'weekEnd',
            'dailyRows',
            'weeklyRows',
            'weeklyStudentRows'
        );
    }

    public static function view(int $id): array
    {
        $report = AttendanceReport::findWithItems($id);
        if (!$report) {
            redirect('attendance_reports.php');
        }

        return compact('report');
    }
}
