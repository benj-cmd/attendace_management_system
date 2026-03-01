<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Section.php';
require_once __DIR__ . '/../models/AttendanceReport.php';

final class SectionController
{
    public static function index(): array
    {
        $sections = Section::sectionsWithStudents();

        return compact('sections');
    }

    public static function section(int $sectionId): array
    {
        $error = '';
        $success = '';

        $section = Section::findById($sectionId);
        if (!$section) {
            redirect('attendance.php');
        }

        $students = Section::studentsInSection($sectionId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = post('action');

            if ($action === 'submit_report') {
                $reportName = post('report_name', date('Y-m-d'));
                $statuses = $_POST['status'] ?? [];

                $items = [];
                foreach ($students as $s) {
                    $sid = (int)$s['id'];
                    $status = $statuses[$sid] ?? 'Absent';
                    if (!in_array($status, ['Present', 'Absent', 'Late'], true)) {
                        $status = 'Absent';
                    }
                    $items[$sid] = $status;
                }

                try {
                    $adminId = current_admin_id();
                    $reportId = AttendanceReport::create($sectionId, $adminId, $reportName, $items);
                    redirect('attendance_report.php');
                } catch (PDOException $e) {
                    $error = 'Failed to submit attendance report.';
                }
            }
        }

        return compact('section', 'students', 'error', 'success');
    }
}
