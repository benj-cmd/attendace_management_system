<?php
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/sidebar.php';
?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle me-2"></i>
        <?= e($error) ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="bi bi-person-lines-fill me-2 text-primary"></i>
                    Section: <?= e((string)$section['name']) ?>
                </h5>
                <p class="mb-0 text-muted small"><?= count($students) ?> student(s) enrolled</p>
            </div>
            <a class="btn btn-outline-secondary" href="attendance.php">
                <i class="bi bi-arrow-left me-1"></i> Back to Sections
            </a>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($students)): ?>
            <div class="alert alert-warning">
                <i class="bi bi-info-circle me-2"></i>
                This section has no students yet. Add students from the Students Management page.
            </div>
        <?php else: ?>
            <form id="attendanceForm" method="post">
                <input type="hidden" name="action" value="submit_report">
                <input type="hidden" name="report_name" id="reportNameInput" value="<?= e(date('Y-m-d')) ?>">

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Student #</th>
                                <th>Student</th>
                                <th class="text-center">Present</th>
                                <th class="text-center">Absent</th>
                                <th class="text-center">Late</th>
                            </tr>
                        </thead>
                        <tbody id="attendanceTableBody">
                        <?php foreach ($students as $s): ?>
                            <?php $sid = (int)$s['id']; ?>
                            <tr class="attendance-row" tabindex="0" data-student-id="<?= $sid ?>">
                                <td>
                                    <span class="badge bg-light text-dark fw-normal">
                                        <?= e((string)$s['student_number']) ?>
                                    </span>
                                </td>
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
                                <td class="text-center">
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input" type="radio" name="status[<?= $sid ?>]" value="Present" required data-status="Present">
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input" type="radio" name="status[<?= $sid ?>]" value="Absent" required data-status="Absent">
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input" type="radio" name="status[<?= $sid ?>]" value="Late" required data-status="Late">
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex flex-wrap gap-2 mt-4">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#submitReportModal">
                        <i class="bi bi-send me-1"></i> Submit Report
                    </button>

                    <button type="button" class="btn btn-outline-primary" id="markAllPresentBtn">
                        <i class="bi bi-check-all me-1"></i> Mark All Present
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($students)): ?>
<div class="modal fade" id="submitReportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-send me-2"></i>
                    Submit Attendance Report
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-medium">Report Name</label>
                    <input class="form-control form-control-lg" id="reportNameField" value="<?= e(date('Y-m-d')) ?>" required>
                    <div class="text-muted small mt-2">Default is today's date.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('reportNameInput').value = document.getElementById('reportNameField').value; document.getElementById('attendanceForm').submit();">
                    <i class="bi bi-send me-1"></i> Submit Report
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
(() => {
    const tbody = document.getElementById('attendanceTableBody');
    if (!tbody) return;

    const rows = Array.from(tbody.querySelectorAll('.attendance-row'));
    if (!rows.length) return;

    const setActiveRow = (row) => {
        rows.forEach((r) => r.classList.remove('table-primary'));
        row.classList.add('table-primary');
        row.focus({ preventScroll: true });
        row.scrollIntoView({ block: 'nearest' });
    };

    const getActiveRow = () => rows.find((r) => r.classList.contains('table-primary')) || rows[0];

    rows.forEach((row) => {
        row.addEventListener('click', (e) => {
            setActiveRow(row);
        });
        row.addEventListener('focus', () => {
            setActiveRow(row);
        });
    });

    setActiveRow(rows[0]);

    const markRow = (row, status) => {
        const input = row.querySelector(`input[type="radio"][data-status="${status}"]`);
        if (!input) return;
        input.checked = true;
        input.dispatchEvent(new Event('change', { bubbles: true }));
    };

    const move = (delta) => {
        const active = getActiveRow();
        const idx = rows.indexOf(active);
        const next = rows[Math.min(rows.length - 1, Math.max(0, idx + delta))];
        if (next) setActiveRow(next);
    };

    document.addEventListener('keydown', (e) => {
        const tag = (e.target && e.target.tagName) ? e.target.tagName.toLowerCase() : '';
        if (tag === 'input' || tag === 'textarea' || tag === 'select') {
            return;
        }

        const key = (e.key || '').toLowerCase();
        const active = getActiveRow();

        if (key === 'p') {
            e.preventDefault();
            markRow(active, 'Present');
            move(1);
        } else if (key === 'a') {
            e.preventDefault();
            markRow(active, 'Absent');
            move(1);
        } else if (key === 'l') {
            e.preventDefault();
            markRow(active, 'Late');
            move(1);
        } else if (key === 'arrowdown') {
            e.preventDefault();
            move(1);
        } else if (key === 'arrowup') {
            e.preventDefault();
            move(-1);
        }
    });

    const markAllBtn = document.getElementById('markAllPresentBtn');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', () => {
            if (!confirm('Mark all students as Present?')) {
                return;
            }
            rows.forEach((row) => markRow(row, 'Present'));
        });
    }
})();
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
