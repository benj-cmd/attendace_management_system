<?php
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/sidebar.php';
?>

<div class="card">
    <div class="card-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="bi bi-file-earmark-text me-2 text-primary"></i>
                    Attendance Reports
                </h5>
                
            </div>
            <form method="get" class="d-flex gap-2 align-items-center flex-wrap">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-stack"></i>
                    </span>
                    <select class="form-select" name="section_id" style="min-width: 200px;" onchange="this.form.submit()">
                        <option value="0">All Sections</option>
                        <?php foreach ($sections as $s): ?>
                            <option value="<?= (int)$s['id'] ?>" <?= ((int)$sectionId === (int)$s['id']) ? 'selected' : '' ?>><?= e((string)$s['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <noscript><button class="btn btn-outline-primary" type="submit">Filter</button></noscript>
            </form>
        </div>
    </div>
    <div class="card-body">
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link<?= ($tab ?? 'submitted') === 'submitted' ? ' active' : '' ?>" href="attendance_reports.php?tab=submitted&amp;section_id=<?= (int)$sectionId ?>">
                    <i class="bi bi-list-check me-1"></i> Submitted
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?= ($tab ?? 'submitted') === 'breakdown' ? ' active' : '' ?>" href="attendance_reports.php?tab=breakdown&amp;breakdown=<?= e((string)($breakdownType ?? 'daily')) ?>&amp;date=<?= e((string)($date ?? date('Y-m-d'))) ?>&amp;from=<?= e((string)($from ?? '')) ?>&amp;to=<?= e((string)($to ?? '')) ?>&amp;section_id=<?= (int)$sectionId ?>">
                    <i class="bi bi-bar-chart me-1"></i> Breakdown
                </a>
            </li>
        </ul>

        <?php if (($tab ?? 'submitted') === 'breakdown'): ?>
            <div class="card mb-4">
                <div class="card-body p-4">
                    <form method="get" class="row g-3 align-items-end">
                        <input type="hidden" name="tab" value="breakdown">
                        <input type="hidden" name="section_id" value="<?= (int)$sectionId ?>">

                        <?php $maxDate = date('Y-m-d'); ?>

                        <div class="col-12 col-md-3">
                            <label class="form-label fw-medium mb-2">Report Type</label>
                            <select class="form-select" name="breakdown" onchange="this.form.submit()">
                                <option value="daily" <?= (($breakdownType ?? 'daily') === 'daily') ? 'selected' : '' ?>>Daily</option>
                                <option value="weekly" <?= (($breakdownType ?? 'daily') === 'weekly') ? 'selected' : '' ?>>Weekly</option>
                                <option value="weekly_students" <?= (($breakdownType ?? 'daily') === 'weekly_students') ? 'selected' : '' ?>>Weekly (Per Student)</option>
                            </select>
                        </div>

                        <?php if (in_array(($breakdownType ?? 'daily'), ['weekly', 'weekly_students'], true)): ?>
                            <div class="col-12 col-md-3">
                                <label class="form-label fw-medium mb-2">From Date</label>
                                <input type="date" class="form-control" name="from" value="<?= e((string)($from ?? '')) ?>" max="<?= e((string)$maxDate) ?>" onchange="this.form.submit()">
                            </div>

                            <div class="col-12 col-md-3">
                                <label class="form-label fw-medium mb-2">To Date</label>
                                <input type="date" class="form-control" name="to" value="<?= e((string)($to ?? '')) ?>" max="<?= e((string)$maxDate) ?>" onchange="this.form.submit()">
                            </div>
                        <?php else: ?>
                            <div class="col-12 col-md-3">
                                <label class="form-label fw-medium mb-2">Date</label>
                                <input type="date" class="form-control" name="date" value="<?= e((string)($date ?? date('Y-m-d'))) ?>" max="<?= e((string)$maxDate) ?>" onchange="this.form.submit()">
                            </div>
                        <?php endif; ?>

                        <div class="col-12 col-md-3">
                            <label class="form-label fw-medium mb-2">Search</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input class="form-control" type="search" placeholder="Search..." data-search-input="breakdown">
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <?php if (($breakdownType ?? 'daily') === 'weekly_students'): ?>
                                        <th>Section</th>
                                        <th>Student #</th>
                                        <th>Student</th>
                                        <th class="text-end">Total</th>
                                        <th class="text-end">Present</th>
                                        <th class="text-end">Absent</th>
                                        <th class="text-end">Late</th>
                                    <?php else: ?>
                                        <th>Section</th>
                                        <?php if (($breakdownType ?? 'daily') === 'weekly'): ?>
                                            <th>Date</th>
                                        <?php endif; ?>
                                        <th class="text-end">Total</th>
                                        <th class="text-end">Present</th>
                                        <th class="text-end">Absent</th>
                                        <th class="text-end">Late</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody data-search-table="breakdown">
                                <?php
                                if (($breakdownType ?? 'daily') === 'weekly_students') {
                                    $rows = $weeklyStudentRows ?? [];
                                } elseif (($breakdownType ?? 'daily') === 'weekly') {
                                    $rows = $weeklyRows ?? [];
                                } else {
                                    $rows = $dailyRows ?? [];
                                }
                                ?>
                                <?php if (empty($rows)): ?>
                                    <tr>
                                        <td colspan="<?= (($breakdownType ?? 'daily') === 'weekly_students') ? 7 : ((($breakdownType ?? 'daily') === 'weekly') ? 6 : 5) ?>" class="text-center text-muted py-4">
                                            <i class="bi bi-info-circle d-block fs-1 mb-2"></i>
                                            No submitted attendance reports found for the selected period.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($rows as $r): ?>
                                        <?php if (($breakdownType ?? 'daily') === 'weekly_students'): ?>
                                            <tr>
                                                <td>
                                                    <span class="badge bg-primary bg-opacity-10 text-primary">
                                                        <?= e((string)$r['section_name']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark fw-normal">
                                                        <?= e((string)$r['student_number']) ?>
                                                    </span>
                                                </td>
                                                <td><?= e((string)$r['fullname']) ?></td>
                                                <td class="text-end fw-medium"><?= (int)$r['total'] ?></td>
                                                <td class="text-end text-success fw-medium"><?= (int)$r['present'] ?></td>
                                                <td class="text-end text-danger fw-medium"><?= (int)$r['absent'] ?></td>
                                                <td class="text-end text-warning fw-medium"><?= (int)$r['late'] ?></td>
                                            </tr>
                                        <?php else: ?>
                                            <tr>
                                                <td>
                                                    <span class="badge bg-primary bg-opacity-10 text-primary">
                                                        <?= e((string)$r['section_name']) ?>
                                                    </span>
                                                </td>
                                                <?php if (($breakdownType ?? 'daily') === 'weekly'): ?>
                                                    <td class="text-muted small fw-medium"><?= e((string)$r['report_date']) ?></td>
                                                <?php endif; ?>
                                                <td class="text-end fw-medium"><?= (int)$r['total'] ?></td>
                                                <td class="text-end text-success fw-medium"><?= (int)$r['present'] ?></td>
                                                <td class="text-end text-danger fw-medium"><?= (int)$r['absent'] ?></td>
                                                <td class="text-end text-warning fw-medium"><?= (int)$r['late'] ?></td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php else: ?>

        <div class="mb-3">
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-search"></i>
                </span>
                <input class="form-control" type="search" placeholder="Search reports..." data-search-input="submitted">
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Report Name</th>
                                <th>Section</th>
                                <th>Submitted</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody data-search-table="submitted">
                        <?php foreach ($reports as $r): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-2">
                                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                                <i class="bi bi-file-earmark-text text-primary"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="fw-medium"><?= e((string)$r['report_name']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary">
                                        <?= e((string)$r['section_name']) ?>
                                    </span>
                                </td>
                                <td class="text-muted small fw-medium"><?= e((string)$r['submitted_at']) ?></td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-primary" href="attendance_report_view.php?id=<?= (int)$r['id'] ?>">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
