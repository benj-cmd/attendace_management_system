<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

final class Student
{
    public static function all(?int $sectionId = null): array
    {
        $adminId = current_admin_id();
        $sql =
            "SELECT
                s.*,
                TRIM(CONCAT_WS(' ', s.first_name, s.middle_name, s.last_name)) AS fullname,
                GROUP_CONCAT(DISTINCT sec.name ORDER BY sec.name SEPARATOR ', ') AS section_names
             FROM students s
             LEFT JOIN section_students ss ON ss.student_id = s.id
             LEFT JOIN sections sec ON sec.id = ss.section_id
             WHERE s.admin_id = :admin_id";

        $params = ['admin_id' => $adminId];
        if ($sectionId !== null && $sectionId > 0) {
            $sql .= ' AND ss.section_id = :section_id';
            $params['section_id'] = $sectionId;
        }

        $sql .= ' GROUP BY s.id ORDER BY s.last_name ASC, s.first_name ASC';

        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function countAll(): int
    {
        $adminId = current_admin_id();
        $stmt = db()->prepare('SELECT COUNT(*) AS c FROM students WHERE admin_id = :admin_id');
        $stmt->execute(['admin_id' => $adminId]);
        $row = $stmt->fetch();
        return (int)($row['c'] ?? 0);
    }

    public static function findBySection(int $sectionId): array
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
             FROM students s
             JOIN section_students ss ON s.id = ss.student_id
             WHERE ss.section_id = :section_id
             ORDER BY s.last_name ASC, s.first_name ASC"
        );
        $stmt->execute(['section_id' => $sectionId]);
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array
    {
        $adminId = current_admin_id();
        $stmt = db()->prepare(
            "SELECT
                s.*,
                TRIM(CONCAT_WS(' ', s.first_name, s.middle_name, s.last_name)) AS fullname
             FROM students s
             WHERE s.id = :id AND s.admin_id = :admin_id
             LIMIT 1"
        );
        $stmt->execute(['id' => $id, 'admin_id' => $adminId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    private static function nextStudentNumber(PDO $pdo): string
    {
        $prefix = '26-';

        $stmt = $pdo->prepare(
            "SELECT student_number
             FROM students
             WHERE student_number LIKE :like
             ORDER BY CAST(SUBSTRING(student_number, 4) AS UNSIGNED) DESC
             LIMIT 1
             FOR UPDATE"
        );
        $stmt->execute(['like' => $prefix . '%']);
        $row = $stmt->fetch();

        if (!$row || !isset($row['student_number'])) {
            return $prefix . '00000';
        }

        $last = 0;
        if (isset($row['student_number'])) {
            $num = (string)$row['student_number'];
            $suffix = substr($num, 3);
            if ($suffix !== false && ctype_digit($suffix)) {
                $last = (int)$suffix;
            }
        }

        $next = $last + 1;
        $suffix = str_pad((string)$next, 5, '0', STR_PAD_LEFT);
        return $prefix . $suffix;
    }

    public static function create(
        string $firstName,
        ?string $middleName,
        string $lastName,
        string $address,
        string $email,
        string $contactNumber
    ): int {
        $pdo = db();
        $pdo->beginTransaction();

        try {
            $studentNumber = self::nextStudentNumber($pdo);
            $adminId = current_admin_id();

            $stmt = $pdo->prepare(
                'INSERT INTO students (first_name, middle_name, last_name, address, email, contact_number, student_number, admin_id)
                 VALUES (:first_name, :middle_name, :last_name, :address, :email, :contact_number, :student_number, :admin_id)'
            );
            $stmt->execute([
                'first_name' => $firstName,
                'middle_name' => $middleName !== '' ? $middleName : null,
                'last_name' => $lastName,
                'address' => $address,
                'email' => $email,
                'contact_number' => $contactNumber,
                'student_number' => $studentNumber,
                'admin_id' => $adminId,
            ]);

            $id = (int)$pdo->lastInsertId();
            $pdo->commit();
            return $id;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function update(
        int $id,
        string $firstName,
        ?string $middleName,
        string $lastName,
        string $address,
        string $email,
        string $contactNumber
    ): void {
        $adminId = current_admin_id();
        $stmt = db()->prepare(
            'UPDATE students
             SET first_name = :first_name,
                 middle_name = :middle_name,
                 last_name = :last_name,
                 address = :address,
                 email = :email,
                 contact_number = :contact_number
             WHERE id = :id AND admin_id = :admin_id'
        );
        $stmt->execute([
            'id' => $id,
            'first_name' => $firstName,
            'middle_name' => $middleName !== '' ? $middleName : null,
            'last_name' => $lastName,
            'address' => $address,
            'email' => $email,
            'contact_number' => $contactNumber,
            'admin_id' => $adminId,
        ]);
    }

    public static function delete(int $id): void
    {
        $adminId = current_admin_id();
        $stmt = db()->prepare('DELETE FROM students WHERE id = :id AND admin_id = :admin_id');
        $stmt->execute(['id' => $id, 'admin_id' => $adminId]);
    }
}
