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
            <div>
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="bi bi-stack me-2 text-primary"></i>
                    Class Sections
                </h5>
                <p class="mb-0 text-muted small">Manage sections and assigned students</p>
            </div>
            <div class="input-group" style="max-width: 300px;">
                <span class="input-group-text">
                    <i class="bi bi-search"></i>
                </span>
                <input class="form-control" type="search" placeholder="Search sections..." data-search-input="sections">
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-4" data-search-cards="sections">
        <?php foreach ($sections as $sec): ?>
            <div class="col-12 col-lg-6">
                <div class="card h-100 border-0 shadow-sm" data-search-item>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h6 class="card-title fw-bold mb-1 d-flex align-items-center">
                                    <i class="bi bi-person-lines-fill me-2 text-primary"></i>
                                    <?= e((string)$sec['name']) ?>
                                </h6>
                                <div class="d-flex align-items-center text-muted">
                                    <i class="bi bi-people me-1"></i>
                                    <span class="small"><?= count($sec['students']) ?> students</span>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <a class="btn btn-primary btn-sm" href="attendance_section.php?id=<?= (int)$sec['id'] ?>">
                                    <i class="bi bi-chevron-right"></i> Open
                                </a>
                            </div>
                        </div>

                        <div class="border-top pt-3">
                            <div class="d-flex align-items-center text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                <span class="small">Manage students within this section</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
