<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

final class AttendanceReport
{
    public static function create(int $sectionId, ?int $adminId, string $reportName, array $items): int
    {
        $pdo = db();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare('INSERT INTO attendance_reports (section_id, report_name, admin_id) VALUES (:section_id, :report_name, :admin_id)');
            $stmt->execute([
                'section_id' => $sectionId,
                'report_name' => $reportName,
                'admin_id' => $adminId,
            ]);
            $reportId = (int)$pdo->lastInsertId();

            $itemStmt = $pdo->prepare(
                "INSERT INTO attendance_report_items (report_id, student_id, status)
                 VALUES (:report_id, :student_id, :status)"
            );

            foreach ($items as $studentId => $status) {
                $itemStmt->execute([
                    'report_id' => $reportId,
                    'student_id' => (int)$studentId,
                    'status' => (string)$status,
                ]);
            }

            $pdo->commit();
            return $reportId;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function list(?int $sectionId = null): array
    {
        $sql = "SELECT
                    r.id,
                    r.report_name,
                    r.submitted_at,
                    s.id AS section_id,
                    s.name AS section_name
                FROM attendance_reports r
                JOIN sections s ON s.id = r.section_id";

        $params = [];
        if ($sectionId !== null && $sectionId > 0) {
            $sql .= ' WHERE r.section_id = :section_id';
            $params['section_id'] = $sectionId;
        }

        $sql .= ' ORDER BY r.submitted_at DESC';

        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function countAll(): int
    {
        $stmt = db()->query('SELECT COUNT(*) AS c FROM attendance_reports');
        $row = $stmt->fetch();
        return (int)($row['c'] ?? 0);
    }

    public static function findWithItems(int $reportId): ?array
    {
        $stmt = db()->prepare(
            "SELECT
                r.id,
                r.report_name,
                r.submitted_at,
                s.id AS section_id,
                s.name AS section_name
             FROM attendance_reports r
             JOIN sections s ON s.id = r.section_id
             WHERE r.id = :id
             LIMIT 1"
        );
        $stmt->execute(['id' => $reportId]);
        $report = $stmt->fetch();
        if (!$report) {
            return null;
        }

        $itemsStmt = db()->prepare(
            "SELECT
                i.student_id,
                i.status,
                st.student_number,
                TRIM(CONCAT_WS(' ', st.first_name, st.middle_name, st.last_name)) AS fullname
             FROM attendance_report_items i
             JOIN students st ON st.id = i.student_id
             WHERE i.report_id = :rid
             ORDER BY st.last_name ASC, st.first_name ASC"
        );
        $itemsStmt->execute(['rid' => $reportId]);
        $items = $itemsStmt->fetchAll();

        $report['items'] = $items;
        return $report;
    }

    public static function dailyBreakdownBySection(string $date, ?int $sectionId = null): array
    {
        $sql = "SELECT
                    s.id AS section_id,
                    s.name AS section_name,
                    COUNT(i.student_id) AS total,
                    SUM(CASE WHEN i.status = 'Present' THEN 1 ELSE 0 END) AS present,
                    SUM(CASE WHEN i.status = 'Absent' THEN 1 ELSE 0 END) AS absent,
                    SUM(CASE WHEN i.status = 'Late' THEN 1 ELSE 0 END) AS late
                FROM attendance_reports r
                JOIN sections s ON s.id = r.section_id
                JOIN attendance_report_items i ON i.report_id = r.id
                WHERE DATE(r.submitted_at) = :report_date";

        $params = ['report_date' => $date];
        if ($sectionId !== null && $sectionId > 0) {
            $sql .= ' AND r.section_id = :section_id';
            $params['section_id'] = $sectionId;
        }

        $sql .= ' GROUP BY s.id, s.name ORDER BY s.name ASC';

        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function weeklyBreakdownBySection(string $weekStartDate, string $weekEndDate, ?int $sectionId = null): array
    {
        $sql = "SELECT
                    s.id AS section_id,
                    s.name AS section_name,
                    DATE(r.submitted_at) AS report_date,
                    COUNT(i.student_id) AS total,
                    SUM(CASE WHEN i.status = 'Present' THEN 1 ELSE 0 END) AS present,
                    SUM(CASE WHEN i.status = 'Absent' THEN 1 ELSE 0 END) AS absent,
                    SUM(CASE WHEN i.status = 'Late' THEN 1 ELSE 0 END) AS late
                FROM attendance_reports r
                JOIN sections s ON s.id = r.section_id
                JOIN attendance_report_items i ON i.report_id = r.id
                WHERE DATE(r.submitted_at) BETWEEN :week_start AND :week_end";

        $params = [
            'week_start' => $weekStartDate,
            'week_end' => $weekEndDate,
        ];
        if ($sectionId !== null && $sectionId > 0) {
            $sql .= ' AND r.section_id = :section_id';
            $params['section_id'] = $sectionId;
        }

        $sql .= ' GROUP BY s.id, s.name, DATE(r.submitted_at) ORDER BY s.name ASC, report_date ASC';

        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function dailyTotals(string $date): array
    {
        $stmt = db()->prepare(
            "SELECT
                SUM(CASE WHEN i.status = 'Present' THEN 1 ELSE 0 END) AS present_total,
                SUM(CASE WHEN i.status = 'Absent' THEN 1 ELSE 0 END) AS absent_total,
                SUM(CASE WHEN i.status = 'Late' THEN 1 ELSE 0 END) AS late_total
             FROM (
                SELECT MAX(r.id) AS id
                FROM attendance_reports r
                WHERE (r.report_name = :report_date_name OR DATE(r.submitted_at) = :report_date_submitted)
                GROUP BY r.section_id
             ) latest
             JOIN attendance_report_items i ON i.report_id = latest.id"
        );
        $stmt->execute([
            'report_date_name' => $date,
            'report_date_submitted' => $date,
        ]);
        $row = $stmt->fetch();

        return [
            'present' => (int)($row['present_total'] ?? 0),
            'absent' => (int)($row['absent_total'] ?? 0),
            'late' => (int)($row['late_total'] ?? 0),
        ];
    }

    public static function weeklySummaryByStudent(string $weekStartDate, string $weekEndDate, ?int $sectionId = null): array
    {
        $sql = "SELECT
                    sec.id AS section_id,
                    sec.name AS section_name,
                    st.id AS student_id,
                    st.student_number,
                    TRIM(CONCAT_WS(' ', st.first_name, st.middle_name, st.last_name)) AS fullname,
                    COUNT(i.student_id) AS total,
                    SUM(CASE WHEN i.status = 'Present' THEN 1 ELSE 0 END) AS present,
                    SUM(CASE WHEN i.status = 'Absent' THEN 1 ELSE 0 END) AS absent,
                    SUM(CASE WHEN i.status = 'Late' THEN 1 ELSE 0 END) AS late
                FROM attendance_reports r
                JOIN sections sec ON sec.id = r.section_id
                JOIN attendance_report_items i ON i.report_id = r.id
                JOIN students st ON st.id = i.student_id
                WHERE DATE(r.submitted_at) BETWEEN :week_start AND :week_end";

        $params = [
            'week_start' => $weekStartDate,
            'week_end' => $weekEndDate,
        ];
        if ($sectionId !== null && $sectionId > 0) {
            $sql .= ' AND r.section_id = :section_id';
            $params['section_id'] = $sectionId;
        }

        $sql .= ' GROUP BY sec.id, sec.name, st.id, st.student_number, st.first_name, st.middle_name, st.last_name';
        $sql .= ' ORDER BY sec.name ASC, st.last_name ASC, st.first_name ASC';

        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
