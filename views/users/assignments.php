<?php 
$pageTitle = 'Instructor Assignments';
require_once __DIR__ . '/../layout/header.php'; 
require_once __DIR__ . '/../layout/sidebar.php';
?>

    <main class="container-fluid py-4">
        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i><?= e($_SESSION['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i><?= e($_SESSION['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="bi bi-diagram-3 me-2"></i>Current Assignments
                            <span class="badge bg-primary ms-2"><?= count($assignments) ?></span>
                        </h6>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignModal">
                            <i class="bi bi-person-plus me-2"></i>Assign Instructor
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Instructor</th>
                                        <th>Section</th>
                                        <th>Assigned By</th>
                                        <th>Assigned Date</th>
                                        <th width="100">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($assignments as $assignment): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="brand-mark me-3" style="width: 40px; height: 40px; font-size: 18px;">
                                                        <?= strtoupper(substr($assignment['instructor_name'], 0, 1)) ?>
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium"><?= e($assignment['instructor_name']) ?></div>
                                                        <small class="text-muted"><?= e($assignment['instructor_email']) ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info bg-opacity-10 text-info">
                                                    <?= e($assignment['section_name']) ?>
                                                </span>
                                            </td>
                                            <td><?= e($assignment['assigned_by'] ? 'Admin' : 'System') ?></td>
                                            <td><?= date('M j, Y', strtotime($assignment['assigned_at'])) ?></td>
                                            <td>
                                                <form method="post" action="user_assignments.php?action=remove" class="d-inline" onsubmit="return confirm('Are you sure you want to remove this assignment?')">
                                                    <input type="hidden" name="instructor_id" value="<?= $assignment['instructor_id'] ?>">
                                                    <input type="hidden" name="section_id" value="<?= $assignment['section_id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-x-circle"></i> Remove
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-2">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">
                            <i class="bi bi-people me-2"></i>Instructors
                            <span class="badge bg-primary ms-2"><?= count($instructors) ?></span>
                        </h6>
                    </div>
                    <div class="card-body">
                        <?php foreach ($instructors as $instructor): ?>
                            <div class="d-flex align-items-center mb-3">
                                <div class="brand-mark me-3" style="width: 35px; height: 35px; font-size: 16px;">
                                    <?= strtoupper(substr($instructor['fullname'], 0, 1)) ?>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-medium"><?= e($instructor['fullname']) ?></div>
                                    <small class="text-muted"><?= e($instructor['email']) ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">
                            <i class="bi bi-diagram-3 me-2"></i>Sections
                            <span class="badge bg-primary ms-2"><?= count($sections) ?></span>
                        </h6>
                    </div>
                    <div class="card-body">
                        <?php foreach ($sections as $section): ?>
                            <div class="d-flex align-items-center mb-3">
                                <div class="brand-mark me-3" style="width: 35px; height: 35px; font-size: 16px; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);">
                                    <?= strtoupper(substr($section['name'], 0, 1)) ?>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-medium"><?= e($section['name']) ?></div>
                                    <small class="text-muted">Created <?= date('M j, Y', strtotime($section['created_at'])) ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

<!-- Assign Instructor Modal -->
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Instructor to Section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="user_assignments.php?action=assign">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Select Instructor</label>
                        <select name="instructor_id" class="form-select" required>
                            <option value="">Choose an instructor...</option>
                            <?php foreach ($instructors as $instructor): ?>
                                <option value="<?= $instructor['id'] ?>"><?= e($instructor['fullname']) ?> (<?= e($instructor['email']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Select Section</label>
                        <select name="section_id" class="form-select" required>
                            <option value="">Choose a section...</option>
                            <?php foreach ($sections as $section): ?>
                                <option value="<?= $section['id'] ?>"><?= e($section['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Multiple instructors can be assigned to the same section. Each instructor will be able to mark attendance for all students in their assigned sections.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-person-plus me-2"></i>Assign Instructor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
