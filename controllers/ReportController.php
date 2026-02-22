<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Attendance.php';

final class ReportController
{
    public static function daily(string $date): array
    {
        $students = Student::all();

        $attendanceByStudentId = [];
        foreach (Attendance::findApprovedByDate($date) as $row) {
            $attendanceByStudentId[(int)$row['student_id']] = $row;
        }

        $summary = Attendance::dailySummaryApproved($date);

        return compact('date', 'students', 'attendanceByStudentId', 'summary');
    }

    public static function weekly(string $anyDateInWeek): array
    {
        $ts = strtotime($anyDateInWeek);
        if ($ts === false) {
            $ts = time();
        }

        $dayOfWeek = (int)date('N', $ts); // 1=Mon..7=Sun
        $monday = date('Y-m-d', strtotime('-' . ($dayOfWeek - 1) . ' days', $ts));
        $sunday = date('Y-m-d', strtotime('+' . (7 - $dayOfWeek) . ' days', $ts));

        $rows = Attendance::weeklyCounts($monday, $sunday);

        return [
            'week_date' => date('Y-m-d', $ts),
            'monday' => $monday,
            'sunday' => $sunday,
            'rows' => $rows,
        ];
    }
}
