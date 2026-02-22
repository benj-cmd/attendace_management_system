<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Section.php';
require_once __DIR__ . '/../models/Attendance.php';
require_once __DIR__ . '/../models/AttendanceReport.php';
require_once __DIR__ . '/../config/database.php';

final class UnifiedReportController
{
    public static function getReportData(
        ?int $sectionId = null,
        ?int $studentId = null,
        ?string $dateFrom = null,
        ?string $dateTo = null
    ): array {
        // Set default date range if not provided
        $dateFrom = $dateFrom ?: date('Y-m-d'); // Today instead of first day of month
        $dateTo = $dateTo ?: date('Y-m-d'); // Today
        
        // Validate dates
        if (!self::isValidDate($dateFrom) || !self::isValidDate($dateTo)) {
            $dateFrom = date('Y-m-d');
            $dateTo = date('Y-m-d');
        }
        
        // Ensure dateFrom is not after dateTo
        if ($dateFrom > $dateTo) {
            [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
        }
        
        // Validate section-student relationship if both are provided
        if ($sectionId && $studentId) {
            if (!self::isStudentInSection($studentId, $sectionId)) {
                // Student is not in the selected section, clear student filter
                $studentId = null;
            }
        }
        
        // Get sections
        $sections = Section::all();
        
        // Get students based on section filter
        if ($sectionId) {
            $students = Student::findBySection($sectionId);
        } else {
            $students = Student::all();
        }
        
        // Build student dropdown options
        $studentOptions = [];
        foreach ($students as $student) {
            $studentOptions[] = [
                'id' => (int)$student['id'],
                'name' => trim($student['first_name'] . ' ' . $student['last_name']),
                'student_number' => $student['student_number']
            ];
        }
        
        // Get attendance data with all filters
        $attendanceData = self::getFilteredAttendance($sectionId, $studentId, $dateFrom, $dateTo);
        
        // Calculate summary statistics
        $summary = self::calculateSummary($attendanceData);
        
        // Get student breakdown
        $studentBreakdown = self::getStudentBreakdown($attendanceData);
        
        // Calculate date range summary (daily/weekly/monthly)
        $dateRangeSummary = self::getDateRangeSummary($dateFrom, $dateTo, $attendanceData);
        
        return [
            'sections' => $sections,
            'studentOptions' => $studentOptions,
            'attendanceData' => $attendanceData,
            'summary' => $summary,
            'studentBreakdown' => $studentBreakdown,
            'dateRangeSummary' => $dateRangeSummary,
            'filters' => [
                'sectionId' => $sectionId,
                'studentId' => $studentId,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo
            ]
        ];
    }
    
    private static function isStudentInSection(int $studentId, int $sectionId): bool {
        $sql = "SELECT COUNT(*) as count FROM section_students WHERE student_id = :student_id AND section_id = :section_id";
        $stmt = db()->prepare($sql);
        $stmt->execute(['student_id' => $studentId, 'section_id' => $sectionId]);
        $result = $stmt->fetch();
        return $result && $result['count'] > 0;
    }
    
    private static function getFilteredAttendance(
        ?int $sectionId,
        ?int $studentId,
        string $dateFrom,
        string $dateTo
    ): array {
        // Get latest report for each section in the date range
        $sql = "
            SELECT 
                ari.student_id,
                s.student_number,
                TRIM(CONCAT(s.first_name, ' ', s.last_name)) as student_name,
                sec.name as section_name,
                DATE(ar.submitted_at) as date,
                ari.status,
                ar.submitted_at as time_marked
            FROM attendance_reports ar
            JOIN attendance_report_items ari ON ar.id = ari.report_id
            JOIN students s ON ari.student_id = s.id
            JOIN section_students ss ON s.id = ss.student_id
            JOIN sections sec ON ss.section_id = sec.id
            WHERE DATE(ar.submitted_at) BETWEEN :date_from AND :date_to
        ";
        
        $params = [
            'date_from' => $dateFrom,
            'date_to' => $dateTo
        ];
        
        if ($sectionId) {
            $sql .= " AND ss.section_id = :section_id";
            $params['section_id'] = $sectionId;
        }
        
        if ($studentId) {
            $sql .= " AND ari.student_id = :student_id";
            $params['student_id'] = $studentId;
        }
        
        $sql .= " ORDER BY ar.submitted_at DESC, s.last_name ASC, s.first_name ASC";
        
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    private static function calculateSummary(array $attendanceData): array
    {
        $present = 0;
        $absent = 0;
        $late = 0;
        $total = count($attendanceData);
        
        foreach ($attendanceData as $record) {
            switch ($record['status']) {
                case 'Present':
                    $present++;
                    break;
                case 'Absent':
                    $absent++;
                    break;
                case 'Late':
                    $late++;
                    break;
            }
        }
        
        return [
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'total' => $total,
            'attendance_rate' => $total > 0 ? round(($present / $total) * 100, 1) : 0
        ];
    }
    
    private static function getStudentBreakdown(array $attendanceData): array
    {
        return $attendanceData; // Already in the right format
    }
    
    private static function getDateRangeSummary(string $dateFrom, string $dateTo, array $attendanceData): array
    {
        $interval = self::getDateInterval($dateFrom, $dateTo);
        
        $dailyStats = [];
        if ($interval <= 31) { // Show daily breakdown for up to 1 month
            $dailyStats = self::getDailyBreakdown($dateFrom, $dateTo, $attendanceData);
        }
        
        return [
            'interval' => $interval,
            'interval_type' => $interval == 1 ? 'daily' : 
                             ($interval <= 7 ? 'weekly' : 
                             ($interval <= 31 ? 'monthly' : 'extended')),
            'daily_breakdown' => $dailyStats,
            'total_days' => $interval
        ];
    }
    
    private static function getDailyBreakdown(string $dateFrom, string $dateTo, array $attendanceData): array
    {
        $dailyStats = [];
        $current = strtotime($dateFrom);
        $end = strtotime($dateTo);
        
        // Initialize all dates
        while ($current <= $end) {
            $date = date('Y-m-d', $current);
            $dailyStats[$date] = [
                'date' => $date,
                'present' => 0,
                'absent' => 0,
                'late' => 0,
                'total' => 0
            ];
            $current = strtotime('+1 day', $current);
        }
        
        // Populate with actual data
        foreach ($attendanceData as $record) {
            $date = $record['date'];
            if (isset($dailyStats[$date])) {
                $dailyStats[$date]['total']++;
                switch ($record['status']) {
                    case 'Present':
                        $dailyStats[$date]['present']++;
                        break;
                    case 'Absent':
                        $dailyStats[$date]['absent']++;
                        break;
                    case 'Late':
                        $dailyStats[$date]['late']++;
                        break;
                }
            }
        }
        
        return array_values($dailyStats);
    }
    
    private static function getDateInterval(string $dateFrom, string $dateTo): int
    {
        $from = new DateTime($dateFrom);
        $to = new DateTime($dateTo);
        return (int)$to->diff($from)->days + 1;
    }
    
    private static function isValidDate(string $date): bool
    {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}