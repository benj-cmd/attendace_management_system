<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../controllers/UserController.php';

require_auth();

$action = $_GET['action'] ?? 'index';
$id = (int)($_GET['id'] ?? 0);

switch ($action) {
    case 'index':
        $data = UserController::index();
        break;
    case 'create':
        $data = UserController::create();
        break;
    case 'edit':
        if ($id <= 0) redirect('users.php');
        $data = UserController::edit($id);
        break;
    case 'delete':
        if ($id <= 0) redirect('users.php');
        UserController::delete($id);
        break;
    default:
        redirect('users.php');
}

extract($data);
require_once __DIR__ . '/../views/users/index.php';
