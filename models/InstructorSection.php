<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

final class InstructorSection
{
    public static function assignInstructorToSection(int $instructorId, int $sectionId, ?int $assignedBy = null): void
    {
        $stmt = db()->prepare('INSERT IGNORE INTO instructor_sections (instructor_id, section_id, assigned_by) VALUES (:instructor_id, :section_id, :assigned_by)');
        $stmt->execute([
            'instructor_id' => $instructorId,
            'section_id' => $sectionId,
            'assigned_by' => $assignedBy,
        ]);
    }

    public static function removeInstructorFromSection(int $instructorId, int $sectionId): void
    {
        $stmt = db()->prepare('DELETE FROM instructor_sections WHERE instructor_id = :instructor_id AND section_id = :section_id');
        $stmt->execute([
            'instructor_id' => $instructorId,
            'section_id' => $sectionId,
        ]);
    }

    public static function getInstructorSections(int $instructorId): array
    {
        $stmt = db()->prepare('
            SELECT s.id, s.name, s.created_at, isa.assigned_at, isa.assigned_by
            FROM instructor_sections isa
            JOIN sections s ON isa.section_id = s.id
            WHERE isa.instructor_id = :instructor_id
            ORDER BY s.name ASC
        ');
        $stmt->execute(['instructor_id' => $instructorId]);
        return $stmt->fetchAll();
    }

    public static function getSectionInstructors(int $sectionId): array
    {
        $stmt = db()->prepare('
            SELECT a.id, a.fullname, a.email, a.role, isa.assigned_at, isa.assigned_by
            FROM instructor_sections isa
            JOIN admins a ON isa.instructor_id = a.id
            WHERE isa.section_id = :section_id
            ORDER BY a.fullname ASC
        ');
        $stmt->execute(['section_id' => $sectionId]);
        return $stmt->fetchAll();
    }

    public static function getAllAssignments(): array
    {
        $stmt = db()->query('
            SELECT isa.id, isa.instructor_id, isa.section_id, isa.assigned_at, isa.assigned_by,
                   a.fullname as instructor_name, a.email as instructor_email,
                   s.name as section_name
            FROM instructor_sections isa
            JOIN admins a ON isa.instructor_id = a.id
            JOIN sections s ON isa.section_id = s.id
            ORDER BY s.name ASC, a.fullname ASC
        ');
        return $stmt->fetchAll();
    }

    public static function isInstructorAssignedToSection(int $instructorId, int $sectionId): bool
    {
        $stmt = db()->prepare('SELECT COUNT(*) as count FROM instructor_sections WHERE instructor_id = :instructor_id AND section_id = :section_id');
        $stmt->execute(['instructor_id' => $instructorId, 'section_id' => $sectionId]);
        $row = $stmt->fetch();
        return (int)($row['count'] ?? 0) > 0;
    }
}
