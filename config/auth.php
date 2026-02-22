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
