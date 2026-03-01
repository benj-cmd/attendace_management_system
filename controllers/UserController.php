<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../models/Section.php';
require_once __DIR__ . '/../models/InstructorSection.php';

final class UserController
{
    public static function index(): array
    {
        require_super_admin();
        
        $users = Admin::all();
        $superAdminExists = self::superAdminExists();
        return compact('users', 'superAdminExists');
    }

    public static function create(): array
    {
        require_super_admin();
        
        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullname = post('fullname');
            $email = strtolower(post('email'));
            $password = post('password');
            $confirm = post('confirm_password');
            $role = post('role', 'instructor');

            if ($fullname === '' || $email === '' || $password === '') {
                $error = 'All fields are required.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Invalid email address.';
            } elseif ($password !== $confirm) {
                $error = 'Passwords do not match.';
            } elseif (Admin::findByEmail($email)) {
                $error = 'Email already registered.';
            } elseif (!in_array($role, ['super_admin', 'instructor'])) {
                $error = 'Invalid role selected.';
            } elseif ($role === 'super_admin' && self::superAdminExists()) {
                $error = 'A super admin account already exists. Only one super admin is allowed.';
            } else {
                Admin::create($fullname, $email, $password, $role);
                $_SESSION['success'] = 'User created successfully.';
                redirect('users.php');
            }
        }

        // Get users for the modal display
        $users = Admin::all();
        $superAdminExists = self::superAdminExists();
        return ['error' => $error, 'success' => $success, 'users' => $users, 'superAdminExists' => $superAdminExists];
    }
    
    private static function superAdminExists(): bool
    {
        $stmt = db()->prepare('SELECT COUNT(*) as count FROM admins WHERE role = :role');
        $stmt->execute(['role' => 'super_admin']);
        $result = $stmt->fetch();
        return $result && $result['count'] > 0;
    }

    public static function edit(int $id): array
    {
        require_super_admin();
        
        $user = Admin::findById($id);
        if (!$user) {
            redirect('users.php');
        }

        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullname = post('fullname');
            $email = strtolower(post('email'));
            $role = post('role');

            if ($fullname === '' || $email === '') {
                $error = 'Full name and email are required.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Invalid email address.';
            } else {
                // Check if email is used by another user
                $existingUser = Admin::findByEmail($email);
                if ($existingUser && $existingUser['id'] != $id) {
                    $error = 'Email already registered to another user.';
                } elseif (!in_array($role, ['super_admin', 'instructor'])) {
                    $error = 'Invalid role selected.';
                } elseif ($role === 'super_admin' && self::superAdminExists() && $user['role'] !== 'super_admin') {
                    $error = 'A super admin account already exists. Only one super admin is allowed.';
                } else {
                    Admin::update($id, $fullname, $email, $role);
                    $_SESSION['success'] = 'User updated successfully.';
                    redirect('users.php');
                }
            }
        }

        return ['user' => $user, 'error' => $error, 'success' => $success, 'users' => Admin::all(), 'superAdminExists' => self::superAdminExists()];
    }

    public static function delete(int $id): void
    {
        require_super_admin();
        
        // Prevent deletion of self
        if ($id === current_admin_id()) {
            $_SESSION['error'] = 'You cannot delete your own account.';
            redirect('users.php');
        }

        $user = Admin::findById($id);
        if (!$user) {
            $_SESSION['error'] = 'User not found.';
            redirect('users.php');
        }

        Admin::delete($id);
        $_SESSION['success'] = 'User deleted successfully.';
        redirect('users.php');
    }

    public static function assignments(): array
    {
        require_super_admin();
        
        $assignments = InstructorSection::getAllAssignments();
        $instructors = array_filter(Admin::all(), fn($admin) => $admin['role'] === 'instructor');
        $sections = Section::all();
        
        return compact('assignments', 'instructors', 'sections');
    }

    public static function assignInstructor(): void
    {
        require_super_admin();
        
        $instructorId = (int)post('instructor_id');
        $sectionId = (int)post('section_id');
        $assignedBy = current_admin_id();

        if ($instructorId && $sectionId) {
            InstructorSection::assignInstructorToSection($instructorId, $sectionId, $assignedBy);
            $_SESSION['success'] = 'Instructor assigned to section successfully.';
        } else {
            $_SESSION['error'] = 'Please select both instructor and section.';
        }

        redirect('user_assignments.php');
    }

    public static function removeAssignment(): void
    {
        require_super_admin();
        
        $instructorId = (int)post('instructor_id');
        $sectionId = (int)post('section_id');

        if ($instructorId && $sectionId) {
            InstructorSection::removeInstructorFromSection($instructorId, $sectionId);
            $_SESSION['success'] = 'Assignment removed successfully.';
        }

        redirect('user_assignments.php');
    }
}
