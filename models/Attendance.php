<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

final class Attendance
{
    private static function hasApprovalColumns(): bool
    {
        static $cached = null;
        if ($cached !== null) {
            return (bool)$cached;
        }

        try {
            $stmt = db()->prepare(
                "SELECT COUNT(*) AS c
                 FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = :schema
                   AND TABLE_NAME = 'attendance'
                   AND COLUMN_NAME = 'approval_status'"
            );
            $stmt->execute(['schema' => DB_NAME]);
            $row = $stmt->fetch();
            $cached = ((int)($row['c'] ?? 0)) > 0;
        } catch (Throwable $e) {
            $cached = false;
        }

        return (bool)$cached;
    }

    public static function upsertPendingForDate(int $studentId, string $status, string $date): void
    {
        if (!self::hasApprovalColumns()) {
            // Backward-compatible fallback if DB migration not applied yet
            $stmt = db()->prepare(
                'INSERT INTO attendance (student_id, status, date) VALUES (:student_id, :status, :date)
                 ON DUPLICATE KEY UPDATE status = VALUES(status), time_marked = CURRENT_TIMESTAMP'
            );
        } else {
            $stmt = db()->prepare(
                "INSERT INTO attendance (student_id, status, approval_status, date)
                 VALUES (:student_id, :status, 'Pending', :date)
                 ON DUPLICATE KEY UPDATE
                    status = VALUES(status),
                    approval_status = 'Pending',
                    approved_at = NULL,
                    time_marked = CURRENT_TIMESTAMP"
            );
        }

        $stmt->execute([
            'student_id' => $studentId,
            'status' => $status,
            'date' => $date,
        ]);
    }

    public static function approveByStudentDate(int $studentId, string $date): void
    {
        if (!self::hasApprovalColumns()) {
            return;
        }
        $stmt = db()->prepare(
            "UPDATE attendance
             SET approval_status = 'Approved', approved_at = CURRENT_TIMESTAMP
             WHERE student_id = :student_id AND date = :date"
        );
        $stmt->execute([
            'student_id' => $studentId,
            'date' => $date,
        ]);
    }

    public static function approveAllPendingByDate(string $date): void
    {
        if (!self::hasApprovalColumns()) {
            return;
        }
        $stmt = db()->prepare(
            "UPDATE attendance
             SET approval_status = 'Approved', approved_at = CURRENT_TIMESTAMP
             WHERE date = :date AND approval_status = 'Pending'"
        );
        $stmt->execute(['date' => $date]);
    }

    public static function findByDate(string $date, ?string $approvalStatus = null): array
    {
        $adminId = current_admin_id();
        $sql =
            "SELECT
                a.*,
                s.student_number,
                TRIM(CONCAT_WS(' ', s.first_name, s.middle_name, s.last_name)) AS fullname
             FROM attendance a
             JOIN students s ON s.id = a.student_id
             WHERE a.date = :date AND s.admin_id = :admin_id";

        $params = ['date' => $date, 'admin_id' => $adminId];
        if ($approvalStatus !== null && self::hasApprovalColumns()) {
            $sql .= ' AND a.approval_status = :approval_status';
            $params['approval_status'] = $approvalStatus;
        }

        $sql .= ' ORDER BY s.last_name ASC, s.first_name ASC';

        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function findPendingByDate(string $date): array
    {
        if (!self::hasApprovalColumns()) {
            return [];
        }

        return self::findByDate($date, 'Pending');
    }

    public static function countPendingByDate(string $date): int
    {
        if (!self::hasApprovalColumns()) {
            return 0;
        }

        $adminId = current_admin_id();
        $stmt = db()->prepare(
            "SELECT COUNT(*) AS c
             FROM attendance a
             JOIN students s ON s.id = a.student_id
             WHERE a.date = :date AND a.approval_status = 'Pending' AND s.admin_id = :admin_id"
        );
        $stmt->execute(['date' => $date, 'admin_id' => $adminId]);
        $row = $stmt->fetch();
        return (int)($row['c'] ?? 0);
    }

    public static function findApprovedByDate(string $date): array
    {
        if (!self::hasApprovalColumns()) {
            return self::findByDate($date, null);
        }

        return self::findByDate($date, 'Approved');
    }

    public static function findForStudentByDate(int $studentId, string $date): ?array
    {
        $stmt = db()->prepare('SELECT * FROM attendance WHERE student_id = :student_id AND date = :date LIMIT 1');
        $stmt->execute(['student_id' => $studentId, 'date' => $date]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function dailySummaryApproved(string $date): array
    {
        if (!self::hasApprovalColumns()) {
            $stmt = db()->prepare(
                "SELECT
                    SUM(status = 'Present') AS present_total,
                    SUM(status = 'Absent') AS absent_total,
                    SUM(status = 'Late') AS late_total
                 FROM attendance
                 WHERE date = :date"
            );
        } else {
            $stmt = db()->prepare(
                "SELECT
                    SUM(status = 'Present') AS present_total,
                    SUM(status = 'Absent') AS absent_total,
                    SUM(status = 'Late') AS late_total
                 FROM attendance
                 WHERE date = :date AND approval_status = 'Approved'"
            );
        }
        $stmt->execute(['date' => $date]);
        $row = $stmt->fetch();

        return [
            'present' => (int)($row['present_total'] ?? 0),
            'absent' => (int)($row['absent_total'] ?? 0),
            'late' => (int)($row['late_total'] ?? 0),
        ];
    }

    public static function weeklyCounts(string $startDate, string $endDate): array
    {
        $sql =
            "SELECT
                 s.id AS student_id,
                 TRIM(CONCAT_WS(' ', s.first_name, s.middle_name, s.last_name)) AS fullname,
                 s.student_number,";

        if (self::hasApprovalColumns()) {
            $sql .=
                " SUM(a.approval_status = 'Approved' AND a.status = 'Present') AS present_total,
                  SUM(a.approval_status = 'Approved' AND a.status = 'Absent') AS absent_total,
                  SUM(a.approval_status = 'Approved' AND a.status = 'Late') AS late_total";
        } else {
            $sql .=
                " SUM(a.status = 'Present') AS present_total,
                  SUM(a.status = 'Absent') AS absent_total,
                  SUM(a.status = 'Late') AS late_total";
        }

        $sql .=
            " FROM students s
              LEFT JOIN attendance a
                ON a.student_id = s.id AND a.date BETWEEN :start_date AND :end_date
              GROUP BY s.id, s.first_name, s.middle_name, s.last_name, s.student_number
              ORDER BY s.last_name ASC, s.first_name ASC";

        $stmt = db()->prepare($sql);

        $stmt->execute([
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        $rows = $stmt->fetchAll();
        foreach ($rows as &$r) {
            $r['present_total'] = (int)$r['present_total'];
            $r['absent_total'] = (int)$r['absent_total'];
            $r['late_total'] = (int)$r['late_total'];
        }
        unset($r);

        return $rows;
    }
}
