<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Attendance.php';

final class AttendanceController
{
    public static function mark(): array
    {
        $error = '';
        $success = '';

        $date = post('date', date('Y-m-d'));
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $date = get('date', date('Y-m-d'));
        }

        $students = Student::all();

        $pendingByStudentId = [];
        foreach (Attendance::findPendingByDate($date) as $row) {
            $pendingByStudentId[(int)$row['student_id']] = $row;
        }

        $approvedByStudentId = [];
        foreach (Attendance::findApprovedByDate($date) as $row) {
            $approvedByStudentId[(int)$row['student_id']] = $row;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $date = post('date', date('Y-m-d'));
            $action = post('action', 'save_pending');
            $statuses = $_POST['status'] ?? [];

            try {
                if ($action === 'approve_all') {
                    Attendance::approveAllPendingByDate($date);
                    $success = 'All pending attendance approved for ' . $date . '.';
                } elseif ($action === 'approve_selected') {
                    $approveIds = $_POST['approve'] ?? [];
                    foreach ($approveIds as $sid => $val) {
                        if ($val === '1') {
                            Attendance::approveByStudentDate((int)$sid, $date);
                        }
                    }
                    $success = 'Selected attendance approved for ' . $date . '.';
                } else {
                    // Save as Pending (instructor approval flow)
                    foreach ($students as $s) {
                        $sid = (int)$s['id'];
                        if (!array_key_exists($sid, $statuses)) {
                            continue;
                        }

                        $status = $statuses[$sid] ?? 'Absent';
                        if (!in_array($status, ['Present', 'Absent', 'Late'], true)) {
                            $status = 'Absent';
                        }
                        Attendance::upsertPendingForDate($sid, $status, $date);
                    }

                    $success = 'Attendance submitted as Pending for ' . $date . '.';
                }

                $pendingByStudentId = [];
                foreach (Attendance::findPendingByDate($date) as $row) {
                    $pendingByStudentId[(int)$row['student_id']] = $row;
                }

                $approvedByStudentId = [];
                foreach (Attendance::findApprovedByDate($date) as $row) {
                    $approvedByStudentId[(int)$row['student_id']] = $row;
                }
            } catch (PDOException $e) {
                $error = 'Failed to save attendance.';
            }
        }

        return compact('students', 'date', 'pendingByStudentId', 'approvedByStudentId', 'error', 'success');
    }
}
