<?php

declare(strict_types=1);

require_once __DIR__ . '/../controllers/AuthController.php';

$pageTitle = 'Register';
$data = AuthController::register();
$error = $data['error'] ?? '';
$success = $data['success'] ?? '';

require_once __DIR__ . '/../views/auth/register.php';
