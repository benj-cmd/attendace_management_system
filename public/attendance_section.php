<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../controllers/SectionController.php';

require_auth();

$sectionId = (int)get('id', '0');
if ($sectionId <= 0) {
    redirect('attendance.php');
}

$pageTitle = 'Attendance';
$data = SectionController::section($sectionId);
$section = $data['section'] ?? null;
$students = $data['students'] ?? [];
$error = $data['error'] ?? '';
$success = $data['success'] ?? '';

require_once __DIR__ . '/../views/attendance/section_detail.php';
