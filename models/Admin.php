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
        $stmt = db()->prepare('SELECT id, fullname, email, created_at FROM admins WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public static function create(string $fullname, string $email, string $passwordPlain): int
    {
        $passwordHash = password_hash($passwordPlain, PASSWORD_DEFAULT);

        $stmt = db()->prepare('INSERT INTO admins (fullname, email, password) VALUES (:fullname, :email, :password)');
        $stmt->execute([
            'fullname' => $fullname,
            'email' => $email,
            'password' => $passwordHash,
        ]);

        return (int)db()->lastInsertId();
    }
}
