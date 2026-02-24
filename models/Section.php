<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

final class Section
{
    public static function all(): array
    {
        $adminId = current_admin_id();
        $stmt = db()->prepare('SELECT * FROM sections WHERE admin_id = :admin_id ORDER BY name ASC');
        $stmt->execute(['admin_id' => $adminId]);
        return $stmt->fetchAll();
    }

    public static function countAll(): int
    {
        $adminId = current_admin_id();
        $stmt = db()->prepare('SELECT COUNT(*) AS c FROM sections WHERE admin_id = :admin_id');
        $stmt->execute(['admin_id' => $adminId]);
        $row = $stmt->fetch();
        return (int)($row['c'] ?? 0);
    }

    public static function create(string $name): int
    {
        $adminId = current_admin_id();
        $stmt = db()->prepare('INSERT INTO sections (name, admin_id) VALUES (:name, :admin_id)');
        $stmt->execute(['name' => $name, 'admin_id' => $adminId]);
        return (int)db()->lastInsertId();
    }

    public static function findById(int $id): ?array
    {
        $adminId = current_admin_id();
        $stmt = db()->prepare('SELECT * FROM sections WHERE id = :id AND admin_id = :admin_id LIMIT 1');
        $stmt->execute(['id' => $id, 'admin_id' => $adminId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function sectionsWithStudents(): array
    {
        $adminId = current_admin_id();
        $sql = "SELECT
                    sec.id AS section_id,
                    sec.name AS section_name,
                    sec.created_at AS section_created_at,
                    s.id AS student_id,
                    s.student_number,
                    s.email,
                    s.contact_number,
                    TRIM(CONCAT_WS(' ', s.first_name, s.middle_name, s.last_name)) AS fullname
                FROM sections sec
                LEFT JOIN section_students ss ON ss.section_id = sec.id
                LEFT JOIN students s ON s.id = ss.student_id
                WHERE sec.admin_id = :admin_id
                ORDER BY sec.name ASC, s.last_name ASC, s.first_name ASC";

        $stmt = db()->prepare($sql);
        $stmt->execute(['admin_id' => $adminId]);
        $rows = $stmt->fetchAll();

        $map = [];
        foreach ($rows as $r) {
            $sid = (int)$r['section_id'];
            if (!isset($map[$sid])) {
                $map[$sid] = [
                    'id' => $sid,
                    'name' => (string)$r['section_name'],
                    'created_at' => (string)$r['section_created_at'],
                    'students' => [],
                ];
            }

            if (!empty($r['student_id'])) {
                $map[$sid]['students'][] = [
                    'id' => (int)$r['student_id'],
                    'fullname' => (string)$r['fullname'],
                    'student_number' => (string)$r['student_number'],
                    'email' => (string)$r['email'],
                    'contact_number' => (string)$r['contact_number'],
                ];
            }
        }

        return array_values($map);
    }

    public static function addStudent(int $sectionId, int $studentId): void
    {
        // Verify the section belongs to the current admin
        $adminId = current_admin_id();
        $stmt = db()->prepare('SELECT id FROM sections WHERE id = :section_id AND admin_id = :admin_id LIMIT 1');
        $stmt->execute(['section_id' => $sectionId, 'admin_id' => $adminId]);
        if (!$stmt->fetch()) {
            throw new InvalidArgumentException('Section not found or access denied');
        }

        $stmt = db()->prepare('INSERT IGNORE INTO section_students (section_id, student_id) VALUES (:section_id, :student_id)');
        $stmt->execute([
            'section_id' => $sectionId,
            'student_id' => $studentId,
        ]);
    }

    public static function studentsInSection(int $sectionId): array
    {
        // Verify the section belongs to the current admin
        $adminId = current_admin_id();
        $stmt = db()->prepare('SELECT id FROM sections WHERE id = :section_id AND admin_id = :admin_id LIMIT 1');
        $stmt->execute(['section_id' => $sectionId, 'admin_id' => $adminId]);
        if (!$stmt->fetch()) {
            throw new InvalidArgumentException('Section not found or access denied');
        }

        $stmt = db()->prepare(
            "SELECT
                s.*,
                TRIM(CONCAT_WS(' ', s.first_name, s.middle_name, s.last_name)) AS fullname
             FROM section_students ss
             JOIN students s ON s.id = ss.student_id
             WHERE ss.section_id = :section_id
             ORDER BY s.last_name ASC, s.first_name ASC"
        );
        $stmt->execute(['section_id' => $sectionId]);
        return $stmt->fetchAll();
    }
}
