<?php
require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../config/auth.php';

$current = basename(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '');
$isActive = function (string $file) use ($current): string {
    return $current === $file ? ' active' : '';
};
?>
<nav class="sidebar sidebar-modern">
    <div class="sidebar-brand">
        <div class="d-flex align-items-center gap-3">
            <div class="brand-mark">S</div>
            <div>
                <div class="brand-title">SAMS</div>
            </div>
        </div>
    </div>

    <div class="nav nav-pills flex-column gap-1 sidebar-nav">
        <a class="nav-link<?= $isActive('dashboard.php') ?>" href="dashboard.php">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>
        
        <?php if (is_super_admin()): ?>
            <a class="nav-link<?= $isActive('students.php') ?><?= $isActive('student_edit.php') ?><?= $isActive('student_delete.php') ?>" href="students.php">
                <i class="bi bi-people-fill"></i>
                <span>Student Management</span>
            </a>
            <a class="nav-link<?= $isActive('users.php') ?>" href="users.php">
                <i class="bi bi-person-gear"></i>
                <span>User Management</span>
            </a>
            <a class="nav-link<?= $isActive('user_assignments.php') ?>" href="user_assignments.php">
                <i class="bi bi-person-plus"></i>
                <span>Instructor Assignments</span>
            </a>
            <div class="sidebar-divider"></div>
        <?php endif; ?>
        
        <a class="nav-link<?= $isActive('attendance.php') ?>" href="attendance.php">
            <i class="bi bi-check-circle-fill"></i>
            <span>Attendance</span>
        </a>
        <a class="nav-link<?= $isActive('attendance_report.php') ?>" href="attendance_report.php">
            <i class="bi bi-file-earmark-bar-graph"></i>
            <span>Attendance Report</span>
        </a>
        <div class="sidebar-divider"></div>
        <a class="nav-link" href="logout.php">
            <i class="bi bi-door-open"></i>
            <span>Logout</span>
        </a>
    </div>
    
    <div class="sidebar-footer mt-auto pt-3 pb-3 px-3 text-center">
        <small class="text-muted">
            &copy; <?= date('Y') ?> Attendance System
        </small>
    </div>
</nav>
<div class="content flex-grow-1">
    <header class="border-bottom text-black shadow-sm">
        <div class="container-fluid py-3 d-flex justify-content-center">
            <h5 class="mb-0 text-center"><?= e($pageTitle ?? 'Dashboard') ?></h5>
        </div>
    </header>
    <main class="container-fluid py-4">
