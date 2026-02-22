<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../controllers/StudentController.php';

require_auth();

$id = (int)get('id', '0');
if ($id <= 0) {
    redirect('students.php');
}

$pageTitle = 'Edit Student';
$data = StudentController::edit($id);
$student = $data['student'] ?? null;
$error = $data['error'] ?? '';
$success = $data['success'] ?? '';

require_once __DIR__ . '/../views/students/edit.php';
