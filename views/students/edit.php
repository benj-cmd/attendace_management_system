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
                <i class="bi bi-person-fill-gear me-2 text-primary"></i>
                Edit Student
            </h5>
            <a class="btn btn-outline-secondary" href="students.php">
                <i class="bi bi-arrow-left me-1"></i> Back to Students
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="post">
            <div class="row g-4">
                <div class="col-12 col-md-4">
                    <label class="form-label fw-medium mb-2">First Name</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-person"></i>
                        </span>
                        <input class="form-control form-control-lg ps-1" name="first_name" value="<?= e((string)$student['first_name']) ?>" required>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-medium mb-2">Middle Name</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-person"></i>
                        </span>
                        <input class="form-control form-control-lg ps-1" name="middle_name" value="<?= e((string)($student['middle_name'] ?? '')) ?>">
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-medium mb-2">Last Name</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-person"></i>
                        </span>
                        <input class="form-control form-control-lg ps-1" name="last_name" value="<?= e((string)$student['last_name']) ?>" required>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-medium mb-2">Address</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-house"></i>
                        </span>
                        <input class="form-control form-control-lg ps-1" name="address" value="<?= e((string)$student['address']) ?>" required>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-medium mb-2">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input type="email" class="form-control form-control-lg ps-1" name="email" value="<?= e((string)$student['email']) ?>" required>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-medium mb-2">Contact Number</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-phone"></i>
                        </span>
                        <input class="form-control form-control-lg ps-1" name="contact_number" value="<?= e((string)$student['contact_number']) ?>" required>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-medium mb-2">Student Number</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-card-text"></i>
                        </span>
                        <input class="form-control form-control-lg ps-1" value="<?= e((string)$student['student_number']) ?>" readonly>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2 mt-4">
                <button class="btn btn-primary btn-lg" type="submit">
                    <i class="bi bi-save me-1"></i> Save Changes
                </button>
                <a class="btn btn-outline-secondary btn-lg" href="students.php">
                    <i class="bi bi-x me-1"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
