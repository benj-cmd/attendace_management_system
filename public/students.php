<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../controllers/StudentController.php';

require_auth();

$pageTitle = 'Students';
$data = StudentController::index();
$students = $data['students'] ?? [];
$sections = $data['sections'] ?? [];
$sectionId = (int)($data['sectionId'] ?? 0);
$error = $data['error'] ?? '';
$success = $data['success'] ?? '';

require_once __DIR__ . '/../views/students/index.php';
