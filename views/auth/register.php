<?php
require_once __DIR__ . '/../layout/plain_header.php';
?>
<div class="container py-5" style="max-width: 460px;">
    <div class="card border-0 shadow-lg">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <div class="mx-auto mb-3">
                    <div class="brand-mark mx-auto d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                        <i class="bi bi-person-plus fs-2"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-2">Create Account</h3>
                <p class="text-muted mb-0">Join our attendance system</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger mb-4">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success mb-4">
                    <i class="bi bi-check-circle me-2"></i>
                    <?= e($success) ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label class="form-label fw-medium mb-2">Full Name</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-person"></i>
                        </span>
                        <input type="text" name="fullname" class="form-control form-control-lg ps-1" placeholder="Enter your full name" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium mb-2">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input type="email" name="email" class="form-control form-control-lg ps-1" placeholder="Enter your email" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium mb-2">Password</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input type="password" name="password" class="form-control form-control-lg ps-1" placeholder="Enter your password" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-medium mb-2">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input type="password" name="confirm_password" class="form-control form-control-lg ps-1" placeholder="Confirm your password" required>
                    </div>
                </div>

                <div class="d-grid mb-4">
                    <button class="btn btn-success btn-lg py-2" type="submit">
                        <i class="bi bi-person-plus me-2"></i>Create Account
                    </button>
                </div>
            </form>

            <div class="text-center">
                <p class="mb-0 text-muted small">Already have an account? <a href="login.php" class="text-success fw-medium">Sign in</a></p>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../layout/plain_footer.php'; ?>
