<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

final class Admin
{
    public static function findByEmail(string $email): ?array
    {
        $stmt = db()->prepare('SELECT * FROM admins WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public static function findById(int $id): ?array
    {
        $stmt = db()->prepare('SELECT id, fullname, email, role, created_at FROM admins WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public static function all(): array
    {
        $stmt = db()->query('SELECT id, fullname, email, role, created_at FROM admins ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }

    public static function create(string $fullname, string $email, string $passwordPlain, string $role = 'instructor'): int
    {
        $passwordHash = password_hash($passwordPlain, PASSWORD_DEFAULT);

        $stmt = db()->prepare('INSERT INTO admins (fullname, email, password, role) VALUES (:fullname, :email, :password, :role)');
        $stmt->execute([
            'fullname' => $fullname,
            'email' => $email,
            'password' => $passwordHash,
            'role' => $role,
        ]);

        return (int)db()->lastInsertId();
    }

    public static function update(int $id, string $fullname, string $email, ?string $role = null): void
    {
        $sql = 'UPDATE admins SET fullname = :fullname, email = :email';
        $params = [
            'id' => $id,
            'fullname' => $fullname,
            'email' => $email,
        ];

        if ($role !== null) {
            $sql .= ', role = :role';
            $params['role'] = $role;
        }

        $sql .= ' WHERE id = :id';

        $stmt = db()->prepare($sql);
        $stmt->execute($params);
    }

    public static function delete(int $id): void
    {
        $stmt = db()->prepare('DELETE FROM admins WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function count(): int
    {
        $stmt = db()->query('SELECT COUNT(*) as count FROM admins');
        $row = $stmt->fetch();
        return (int)($row['count'] ?? 0);
    }
}
