<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

function start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    session_name(SESSION_NAME);
    session_start();
}

function require_auth(): void
{
    start_session();

    if (empty($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }
}

function current_admin_id(): ?int
{
    start_session();

    return isset($_SESSION['admin_id']) ? (int)$_SESSION['admin_id'] : null;
}

function current_admin_role(): ?string
{
    start_session();

    return $_SESSION['admin_role'] ?? null;
}

function is_super_admin(): bool
{
    return current_admin_role() === 'super_admin';
}

function is_instructor(): bool
{
    return current_admin_role() === 'instructor';
}

function require_super_admin(): void
{
    require_auth();
    
    if (!is_super_admin()) {
        header('HTTP/1.0 403 Forbidden');
        echo 'Access denied. Super admin privileges required.';
        exit;
    }
}

function require_instructor(): void
{
    require_auth();
    
    if (!is_instructor()) {
        header('HTTP/1.0 403 Forbidden');
        echo 'Access denied. Instructor privileges required.';
        exit;
    }
}
