<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../controllers/StudentController.php';

require_auth();

$id = (int)get('id', '0');
if ($id > 0) {
    StudentController::delete($id);
}

redirect('students.php');
