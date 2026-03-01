<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../controllers/UserController.php';

require_auth();

$action = $_GET['action'] ?? 'assignments';

switch ($action) {
    case 'assignments':
        $data = UserController::assignments();
        break;
    case 'assign':
        UserController::assignInstructor();
        break;
    case 'remove':
        UserController::removeAssignment();
        break;
    default:
        redirect('user_assignments.php');
}

extract($data);
require_once __DIR__ . '/../views/users/assignments.php';
