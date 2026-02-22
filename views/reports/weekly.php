<?php
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/sidebar.php';
?>

<div class="card">
    <div class="card-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="mb-0 d-flex align-items-center">
                <i class="bi bi-calendar-week me-2 text-primary"></i>
                Weekly Attendance Report
            </h5>
            <div class="d-flex flex-wrap gap-2">
                <form method="get" class="d-flex gap-2">
                    <div>
                        <label class="form-label fw-medium mb-2">Select Week</label>
                        <input type="date" name="date" class="form-control" value="<?= e($week_date) ?>">
                    </div>
                    <div class="align-self-end">
                        <button class="btn btn-outline-primary" type="submit">Load Report</button>
                    </div>
                </form>
                <div class="align-self-end">
                    <a class="btn btn-primary" href="export_weekly_csv.php?date=<?= e($week_date) ?>">
                        <i class="bi bi-download me-1"></i> Export CSV
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="alert alert-info mb-4">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Report Period:</strong> <?= e($monday) ?> to <?= e($sunday) ?>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Student</th>
                        <th>Student #</th>
                        <th>Present</th>
                        <th>Absent</th>
                        <th>Late</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-2">
                                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                        <i class="bi bi-person text-primary"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="fw-medium"><?= e((string)$r['fullname']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark fw-normal">
                                <?= e((string)$r['student_number']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-success bg-opacity-10 text-success fw-medium">
                                <i class="bi bi-check-circle me-1"></i>
                                <?= (int)$r['present_total'] ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-danger bg-opacity-10 text-danger fw-medium">
                                <i class="bi bi-x-circle me-1"></i>
                                <?= (int)$r['absent_total'] ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-warning bg-opacity-10 text-warning fw-medium">
                                <i class="bi bi-clock me-1"></i>
                                <?= (int)$r['late_total'] ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
