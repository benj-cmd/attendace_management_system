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

<?php if (!empty($success)): ?>
    <div class="alert alert-success">
        <i class="bi bi-check-circle me-2"></i>
        <?= e($success) ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="mb-0 d-flex align-items-center">
                <i class="bi bi-people me-2 text-primary"></i>
                Student Management
            </h5>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#createSectionModal">
                    <i class="bi bi-stack me-1"></i> Create Section
                </button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                    <i class="bi bi-plus-circle me-1"></i> Add Student
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form method="get" class="row g-3 align-items-end mb-4">
            <div class="col-12 col-md-4">
                <label class="form-label fw-medium mb-2">Filter by Section</label>
                <select class="form-select" name="section_id" onchange="this.form.submit()">
                    <option value="0">All Sections</option>
                    <?php foreach (($sections ?? []) as $sec): ?>
                        <option value="<?= (int)$sec['id'] ?>" <?= ((int)($sectionId ?? 0) === (int)$sec['id']) ? 'selected' : '' ?>><?= e((string)$sec['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-8">
                <label class="form-label fw-medium mb-2">Search Students</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>
                    <input class="form-control" type="search" placeholder="Search by name, student number, or email..." data-search-input="students">
                </div>
                <noscript>
                    <button class="btn btn-outline-primary mt-2" type="submit">Apply Filter</button>
                </noscript>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Student #</th>
                        <th>Name</th>
                        <th>Section</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>Created</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody data-search-table="students">
                <?php foreach ($students as $s): ?>
                    <tr>
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
                        <td>
                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                <?= e((string)($s['section_names'] ?? '')) ?>
                            </span>
                        </td>
                        <td>
                            <span class="text-muted"><?= e((string)$s['email']) ?></span>
                        </td>
                        <td><?= e((string)$s['contact_number']) ?></td>
                        <td>
                            <span class="small text-muted"><?= e((string)$s['created_at']) ?></span>
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a class="btn btn-sm btn-outline-primary" href="student_edit.php?id=<?= (int)$s['id'] ?>">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a class="btn btn-sm btn-outline-danger" href="student_delete.php?id=<?= (int)$s['id'] ?>" onclick="return confirm('Are you sure you want to delete this student? This action cannot be undone.');">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<div class="modal fade" id="createSectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post">
                <input type="hidden" name="action" value="create_section">
                <div class="modal-body">
                    <label class="form-label">Section Name</label>
                    <input class="form-control" name="section_name" placeholder="e.g. BSIT 3-A" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="addStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Section</label>
                            <select class="form-select" name="section_id" required>
                                <option value="">Select a section</option>
                                <?php foreach (($sections ?? []) as $sec): ?>
                                    <option value="<?= (int)$sec['id'] ?>"><?= e((string)$sec['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">First Name</label>
                            <input class="form-control" name="first_name" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">Middle Name</label>
                            <input class="form-control" name="middle_name">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">Last Name</label>
                            <input class="form-control" name="last_name" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <input class="form-control" name="address" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Contact Number</label>
                            <input class="form-control" name="contact_number" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
