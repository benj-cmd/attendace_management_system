<?php require_once __DIR__ . '/../layout/plain_header.php'; ?>

<style>
    .auth-wrapper {
        max-width: 430px;
        margin: 0 auto;
        padding: 60px 0;
    }

    .auth-card {
        border: 0;
        border-radius: 18px;
        padding: 40px;
        background: #ffffff;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
    }

    .brand-mark {
        width: 70px;
        height: 70px;
        border-radius: 14px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 32px;
        font-weight: bold;
        color: #fff;
        margin: 0 auto 20px;
        box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.3), 0 2px 4px -1px rgba(79, 70, 229, 0.15);
    }

    .form-control-modern {
        border: 1px solid #e4e7eb;
        border-radius: 12px;
        padding: 12px 14px;
        font-size: 15px;
        transition: all 0.2s;
    }

    .form-control-modern:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15);
    }

    .btn-modern {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border: none;
        border-radius: 12px;
        padding: 12px;
        font-size: 16px;
        font-weight: 600;
        transition: 0.2s;
        letter-spacing: 0.5px;
    }

    .btn-modern:hover {
        opacity: 0.92;
        transform: translateY(-1px);
    }

    .auth-footer a {
        text-decoration: none;
        font-weight: 600;
        color: var(--primary) !important;
    }

    .auth-footer a:hover {
        text-decoration: underline;
    }

    .password-strength {
        height: 4px;
        border-radius: 2px;
        margin-top: 8px;
        transition: all 0.3s;
    }

    .strength-weak { background: #ef4444; width: 33%; }
    .strength-medium { background: #f59e0b; width: 66%; }
    .strength-strong { background: var(--primary); width: 100%; }
</style>

<div class="auth-wrapper">
    <div class="auth-card">

        <div class="text-center mb-4">
            <div class="brand-mark">S</div>
            <h3 class="fw-bold mb-1">Create Account</h3>
            <p class="text-muted">Join our attendance system</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger mb-4">
                <i class="bi bi-exclamation-circle me-2"></i><?= e($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success mb-4">
                <i class="bi bi-check-circle me-2"></i><?= e($success) ?>
            </div>
        <?php endif; ?>

        <form method="post" id="signupForm">
            <div class="mb-3">
                <label class="form-label fw-semibold">Full Name</label>
                <input type="text" name="fullname" class="form-control form-control-modern" placeholder="Enter your full name" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Email Address</label>
                <input type="email" name="email" class="form-control form-control-modern" placeholder="Enter your email" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Password</label>
                <input type="password" name="password" class="form-control form-control-modern" placeholder="Enter your password" required id="password">
                <div class="password-strength" id="passwordStrength"></div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control form-control-modern" placeholder="Confirm your password" required id="confirmPassword">
            </div>

            <button class="btn btn-modern w-100 mb-4 btn-primary" type="submit">
                <i class="bi bi-person-plus me-2"></i>Create Account
            </button>
        </form>

        <div class="text-center auth-footer">
            <p class="text-muted mb-0">
                Already have an account? 
                <a href="login.php">Sign in</a>
            </p>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirmPassword');
    const passwordStrength = document.getElementById('passwordStrength');
    const form = document.getElementById('signupForm');

    function checkPasswordStrength(password) {
        let strength = 0;
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
        if (password.match(/[0-9]/)) strength++;
        if (password.match(/[^a-zA-Z0-9]/)) strength++;

        passwordStrength.className = 'password-strength';
        if (password.length > 0) {
            if (strength <= 1) {
                passwordStrength.classList.add('strength-weak');
            } else if (strength === 2) {
                passwordStrength.classList.add('strength-medium');
            } else {
                passwordStrength.classList.add('strength-strong');
            }
        }
    }

    password.addEventListener('input', function() {
        checkPasswordStrength(this.value);
    });

    form.addEventListener('submit', function(e) {
        if (password.value !== confirmPassword.value) {
            e.preventDefault();
            if (!document.querySelector('.alert-danger')) {
                const alert = document.createElement('div');
                alert.className = 'alert alert-danger mb-4';
                alert.innerHTML = '<i class="bi bi-exclamation-circle me-2"></i>Passwords do not match';
                form.insertBefore(alert, form.firstChild);
            }
        }
    });
});
</script>

<?php require_once __DIR__ . '/../layout/plain_footer.php'; ?>
