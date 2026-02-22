<?php

declare(strict_types=1);

require_once __DIR__ . '/../controllers/AuthController.php';

$pageTitle = 'Login';
$data = AuthController::login();
$error = $data['error'] ?? '';

require_once __DIR__ . '/../views/auth/login.php';
