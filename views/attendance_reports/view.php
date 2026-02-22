<?php
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <div class="fw-semibold"><?= e((string)$report['report_name']) ?></div>
        <div class="text-muted small">
            Section: <?= e((string)$report['section_name']) ?>
            <span class="mx-2">|</span>
            Submitted: <?= e((string)$report['submitted_at']) ?>
        </div>
    </div>
    <a class="btn btn-outline-secondary" href="attendance_reports.php">Back</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead>
                    <tr>
                        <th>Student #</th>
                        <th>Student</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach (($report['items'] ?? []) as $it): ?>
                    <tr>
                        <td><?= e((string)$it['student_number']) ?></td>
                        <td><?= e((string)$it['fullname']) ?></td>
                        <td><?= e((string)$it['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
