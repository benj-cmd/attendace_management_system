<?php
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/sidebar.php';
?>

<div class="row g-4 mb-4">
    <div class="col-12 col-md-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 p-3 rounded-circle bg-success bg-opacity-10">
                        <i class="bi bi-check-circle text-success fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small text-uppercase fw-bold">Present</div>
                        <h3 class="mb-0 fw-bold"><?= (int)$summary['present'] ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 p-3 rounded-circle bg-danger bg-opacity-10">
                        <i class="bi bi-x-circle text-danger fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small text-uppercase fw-bold">Absent</div>
                        <h3 class="mb-0 fw-bold"><?= (int)$summary['absent'] ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 p-3 rounded-circle bg-warning bg-opacity-10">
                        <i class="bi bi-clock text-warning fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small text-uppercase fw-bold">Late</div>
                        <h3 class="mb-0 fw-bold"><?= (int)$summary['late'] ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="mb-0 d-flex align-items-center">
                <i class="bi bi-calendar-day me-2 text-primary"></i>
                Daily Attendance Report
            </h5>
            <div class="d-flex flex-wrap gap-2">
                <form method="get" class="d-flex gap-2">
                    <div>
                        <label class="form-label fw-medium mb-2">Select Date</label>
                        <input type="date" name="date" class="form-control" value="<?= e($date) ?>">
                    </div>
                    <div class="align-self-end">
                        <button class="btn btn-outline-primary" type="submit">Load Report</button>
                    </div>
                </form>
                <div class="align-self-end">
                    <a class="btn btn-primary" href="export_daily_csv.php?date=<?= e($date) ?>">
                        <i class="bi bi-download me-1"></i> Export CSV
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Student</th>
                        <th>Student #</th>
                        <th>Status</th>
                        <th>Time Marked</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($students as $s): ?>
                    <?php
                        $sid = (int)$s['id'];
                        $a = $attendanceByStudentId[$sid] ?? null;
                        $status = $a['status'] ?? 'Not Marked';
                        $time = $a['time_marked'] ?? '';
                    ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-2">
                                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                        <i class="bi bi-person text-primary"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="fw-medium"><?= e((string)$s['fullname']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark fw-normal">
                                <?= e((string)$s['student_number']) ?>
                            </span>
                        </td>
                        <td>
                            <?php
                                $statusClass = '';
                                $statusIcon = '';
                                switch ($status) {
                                    case 'Present':
                                        $statusClass = 'success';
                                        $statusIcon = 'check-circle';
                                        break;
                                    case 'Absent':
                                        $statusClass = 'danger';
                                        $statusIcon = 'x-circle';
                                        break;
                                    case 'Late':
                                        $statusClass = 'warning';
                                        $statusIcon = 'clock';
                                        break;
                                    default:
                                        $statusClass = 'secondary';
                                        $statusIcon = 'dash-circle';
                                }
                            ?>
                            <span class="badge bg-<?= $statusClass ?> bg-opacity-10 text-<?= $statusClass ?> fw-medium">
                                <i class="bi bi-<?= $statusIcon ?> me-1"></i>
                                <?= e((string)$status) ?>
                            </span>
                        </td>
                        <td class="text-muted small fw-medium"><?= e((string)$time) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
