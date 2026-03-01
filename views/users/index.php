<?php 
$pageTitle = 'User Management';
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
                            <i class="bi bi-people me-2"></i>All Users
                            <span class="badge bg-primary ms-2"><?= count($users) ?></span>
                        </h6>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                            <i class="bi bi-person-plus me-2"></i>Create User
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Created</th>
                                        <th width="120">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="brand-mark me-3" style="width: 40px; height: 40px; font-size: 18px;">
                                                        <?= strtoupper(substr($user['fullname'], 0, 1)) ?>
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium"><?= e($user['fullname']) ?></div>
                                                        <?php if ($user['id'] === current_admin_id()): ?>
                                                            <small class="text-muted">(You)</small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= e($user['email']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $user['role'] === 'super_admin' ? 'danger' : 'primary' ?> bg-opacity-10 text-<?= $user['role'] === 'super_admin' ? 'danger' : 'primary' ?>">
                                                    <?= ucfirst(str_replace('_', ' ', $user['role'])) ?>
                                                </span>
                                            </td>
                                            <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editUserModal<?= $user['id'] ?>">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <?php if ($user['id'] !== current_admin_id()): ?>
                                                        <form method="post" action="users.php?action=delete&id=<?= $user['id'] ?>" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                            <button type="submit" class="btn btn-outline-danger">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
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
    </main>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="users.php?action=create">
                <div class="modal-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-circle me-2"></i><?= e($error) ?>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Full Name</label>
                        <input type="text" name="fullname" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Email Address</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Role</label>
                        <select name="role" class="form-select" <?= ($superAdminExists ?? false) ? 'disabled' : '' ?>>
                            <option value="instructor">Instructor</option>
                            <?php if (!($superAdminExists ?? false)): ?>
                                <option value="super_admin">Super Admin</option>
                            <?php endif; ?>
                        </select>
                        <?php if ($superAdminExists ?? false): ?>
                            <small class="text-muted">Super Admin role is not available (one already exists)</small>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-person-plus me-2"></i>Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modals -->
<?php foreach ($users as $user): ?>
    <?php if ($user['id'] !== current_admin_id()): ?>
        <div class="modal fade" id="editUserModal<?= $user['id'] ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="post" action="users.php?action=edit&id=<?= $user['id'] ?>">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-medium">Full Name</label>
                                <input type="text" name="fullname" class="form-control" value="<?= e($user['fullname']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-medium">Email Address</label>
                                <input type="email" name="email" class="form-control" value="<?= e($user['email']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-medium">Role</label>
                                <select name="role" class="form-select" <?= (($superAdminExists ?? false) && $user['role'] !== 'super_admin') ? 'disabled' : '' ?>>
                                    <option value="instructor" <?= $user['role'] === 'instructor' ? 'selected' : '' ?>>Instructor</option>
                                    <?php if ($user['role'] === 'super_admin' || !($superAdminExists ?? false)): ?>
                                        <option value="super_admin" <?= $user['role'] === 'super_admin' ? 'selected' : '' ?>>Super Admin</option>
                                    <?php endif; ?>
                                </select>
                                <?php if (($superAdminExists ?? false) && $user['role'] !== 'super_admin'): ?>
                                    <small class="text-muted">Super Admin role is not available (one already exists)</small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
