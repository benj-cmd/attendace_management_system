<?php
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/sidebar.php';
?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="get" class="row g-2 align-items-end mb-3">
            <div class="col-12 col-md-4">
                <label class="form-label">Date</label>
                <input type="date" name="date" class="form-control" value="<?= e($date) ?>">
            </div>
            <div class="col-12 col-md-2">
                <button class="btn btn-outline-primary w-100" type="submit">Load</button>
            </div>
        </form>

        <div class="row g-3">
            <div class="col-12 col-lg-7">
                <div class="card border-0 bg-light">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="fw-semibold">Pending Approvals</div>
                            <div class="d-flex gap-2">
                                <form method="post" class="m-0">
                                    <input type="hidden" name="date" value="<?= e($date) ?>">
                                    <input type="hidden" name="action" value="approve_all">
                                    <button class="btn btn-sm btn-outline-success" type="submit">Approve All</button>
                                </form>
                            </div>
                        </div>

                        <form method="post">
                            <input type="hidden" name="date" value="<?= e($date) ?>">
                            <input type="hidden" name="action" value="approve_selected">

                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 36px;"></th>
                                            <th>Student</th>
                                            <th>Student #</th>
                                            <th>Status (Pending)</th>
                                            <th>Submitted</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($students as $s): ?>
                                        <?php
                                            $sid = (int)$s['id'];
                                            $pending = $pendingByStudentId[$sid] ?? null;
                                            if (!$pending) {
                                                continue;
                                            }
                                            $selected = $pending['status'] ?? 'Absent';
                                        ?>
                                        <tr>
                                            <td>
                                                <input class="form-check-input" type="checkbox" name="approve[<?= $sid ?>]" value="1">
                                            </td>
                                            <td><?= e((string)$s['fullname']) ?></td>
                                            <td><?= e((string)$s['student_number']) ?></td>
                                            <td><?= e((string)$selected) ?></td>
                                            <td class="text-muted small"><?= e((string)($pending['time_marked'] ?? '')) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                <button class="btn btn-success" type="submit">Approve Selected</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-5">
                <div class="card border-0 bg-white">
                    <div class="card-body">
                        <div class="fw-semibold mb-2">Submit Attendance as Pending (Admin)</div>
                        <div class="text-muted small mb-3">
                            This simulates student submissions. Records saved here will appear under Pending Approvals.
                        </div>

                        <form method="post">
                            <input type="hidden" name="date" value="<?= e($date) ?>">
                            <input type="hidden" name="action" value="save_pending">

                            <div class="table-responsive" style="max-height: 420px; overflow: auto;">
                                <table class="table table-sm align-middle">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th style="width: 170px;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($students as $s): ?>
                                        <?php
                                            $sid = (int)$s['id'];
                                            $existing = $pendingByStudentId[$sid] ?? ($approvedByStudentId[$sid] ?? null);
                                            $selected = $existing['status'] ?? 'Absent';
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold"><?= e((string)$s['fullname']) ?></div>
                                                <div class="text-muted small"><?= e((string)$s['student_number']) ?></div>
                                            </td>
                                            <td>
                                                <select name="status[<?= $sid ?>]" class="form-select form-select-sm">
                                                    <option value="Present" <?= $selected === 'Present' ? 'selected' : '' ?>>Present</option>
                                                    <option value="Absent" <?= $selected === 'Absent' ? 'selected' : '' ?>>Absent</option>
                                                    <option value="Late" <?= $selected === 'Late' ? 'selected' : '' ?>>Late</option>
                                                </select>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <button class="btn btn-primary" type="submit">Submit Pending</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card border-0 bg-white">
                    <div class="card-body">
                        <div class="fw-semibold mb-2">Approved</div>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Student #</th>
                                        <th>Status</th>
                                        <th>Approved At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($students as $s): ?>
                                    <?php
                                        $sid = (int)$s['id'];
                                        $approved = $approvedByStudentId[$sid] ?? null;
                                        if (!$approved) {
                                            continue;
                                        }
                                    ?>
                                    <tr>
                                        <td><?= e((string)$s['fullname']) ?></td>
                                        <td><?= e((string)$s['student_number']) ?></td>
                                        <td><?= e((string)$approved['status']) ?></td>
                                        <td class="text-muted small"><?= e((string)($approved['approved_at'] ?? '')) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
