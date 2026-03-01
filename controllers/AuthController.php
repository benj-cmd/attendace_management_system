<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Admin.php';

final class AuthController
{
    public static function login(): array
    {
        start_session();

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = strtolower(post('email'));
            $password = post('password');

            if ($email === '' || $password === '') {
                $error = 'Email and password are required.';
            } else {
                $admin = Admin::findByEmail($email);

                if (!$admin || !password_verify($password, (string)$admin['password'])) {
                    $error = 'Invalid credentials.';
                } else {
                    $_SESSION['admin_id'] = (int)$admin['id'];
                    $_SESSION['admin_role'] = $admin['role'];
                    redirect('dashboard.php');
                }
            }
        }

        return ['error' => $error];
    }

    public static function logout(): void
    {
        start_session();
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool)$params['secure'], (bool)$params['httponly']);
        }

        session_destroy();
        redirect('login.php');
    }
}
