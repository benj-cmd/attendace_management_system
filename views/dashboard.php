<?php
require_once __DIR__ . '/layout/header.php';
require_once __DIR__ . '/layout/sidebar.php';
?>

<div class="row g-4">
    <!-- Stats Overview -->
    <div class="col-12">
        <div class="row g-4">
            <div class="col-12 col-md-4">
                <div class="card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 p-3 rounded-circle bg-primary bg-opacity-10">
                                <i class="bi bi-people text-primary fs-3"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="text-muted small text-uppercase fw-bold">Students</div>
                                <h3 class="mb-0 fw-bold"><?= (int)($studentCount ?? 0) ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 p-3 rounded-circle bg-info bg-opacity-10">
                                <i class="bi bi-stack text-info fs-3"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="text-muted small text-uppercase fw-bold">Sections</div>
                                <h3 class="mb-0 fw-bold"><?= (int)($sectionCount ?? 0) ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 p-3 rounded-circle bg-success bg-opacity-10">
                                <i class="bi bi-file-earmark-text text-success fs-3"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="text-muted small text-uppercase fw-bold">Reports</div>
                                <h3 class="mb-0 fw-bold"><?= (int)($submittedReportCount ?? 0) ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions and Date Info -->
    <div class="col-12">
        <div class="row g-4">
            <div class="col-12 col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-title mb-3 fw-bold">Today's Date</h6>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar-event fs-4 text-primary me-3"></i>
                            <div>
                                <h5 class="mb-1 fw-bold"><?= e((string)($today ?? date('F j, Y'))) ?></h5>
                                <div class="text-muted small">Current attendance date</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-title mb-3 fw-bold">Quick Actions</h6>
                        <div class="d-grid gap-2">
                            <a class="btn btn-lg btn-primary" href="attendance.php">
                                <i class="bi bi-check-circle me-2"></i>Mark Attendance
                            </a>
                            <div class="d-flex gap-2">
                                <a class="btn btn-outline-primary flex-fill" href="students.php">
                                    <i class="bi bi-people me-1"></i> Manage
                                </a>
                                <a class="btn btn-outline-secondary flex-fill" href="attendance_report.php">
                                    <i class="bi bi-file-earmark-text me-1"></i> Reports
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
