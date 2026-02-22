<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/Attendance.php';
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Section.php';
require_once __DIR__ . '/../models/AttendanceReport.php';

final class DashboardController
{
    public static function index(): array
    {
        $today = date('Y-m-d');

        $summary = AttendanceReport::dailyTotals($today);

        $studentCount = Student::countAll();
        $sectionCount = Section::countAll();
        $submittedReportCount = AttendanceReport::countAll();

        return compact(
            'today',
            'summary',
            'studentCount',
            'sectionCount',
            'submittedReportCount'
        );
    }
}
