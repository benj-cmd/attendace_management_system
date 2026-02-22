<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../controllers/SectionController.php';

require_auth();

$pageTitle = 'Attendance';
$data = SectionController::index();
$sections = $data['sections'] ?? [];
$error = '';
$success = '';

require_once __DIR__ . '/../views/attendance/sections.php';
