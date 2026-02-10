<?php
/**
 * Evolvcode CMS - Users & Roles Management (Super Admin Only)
 */

$pageTitle = 'Users';
require_once __DIR__ . '/includes/header.php';

// Only Super Admin can access this page
Auth::requirePermission('users');

$db = Database::getInstance();
$message = '';
$error = '';
$editUser = null;
$editRole = null;

// Current tab
$tab = $_GET['tab'] ?? 'users';

// ── Available permission modules ──
$allPermissions = [
    'Content' => [
        'pages'    => 'Pages',
        'services' => 'Services',
        'projects' => 'Portfolio',
        'posts'    => 'Blog Posts',
    ],
    'Theme' => [
        'homepage' => 'Homepage',
        'menus'    => 'Menus',
    ],
    'Communication' => [
        'forms'       => 'Forms',
        'submissions' => 'Submissions',
        'contacts'    => 'Contacts',
    ],
    'System' => [
        'media'    => 'Media Library',
        'settings' => 'Settings',
        'security' => 'Security',
        'users'    => 'Users',
    ],
];

// ══════════════════════════════════════
//  HANDLE FORM SUBMISSIONS
// ══════════════════════════════════════
if (Security::isPost()) {
    $token = $_POST[CSRF_TOKEN_NAME] ?? '';

    // ── ADD USER ──
    if (isset($_POST['add_user'])) {
        if (!Security::validateCSRFToken($token)) {
            $error = 'Invalid CSRF token.';
        } else {
            $username = Security::clean($_POST['username'] ?? '');
            $email = Security::clean($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $roleId = (int)($_POST['role_id'] ?? 1);
            $status = Security::clean($_POST['status'] ?? 'active');

            if (!$username || !$email || !$password) {
                $error = 'Username, email, and password are required.';
            } elseif (strlen($password) < 8) {
                $error = 'Password must be at least 8 characters.';
            } elseif ($db->exists('admins', 'username = ?', [$username])) {
                $error = 'Username already exists.';
            } elseif ($db->exists('admins', 'email = ?', [$email])) {
                $error = 'Email already exists.';
            } else {
                try {
                    Auth::createUser($username, $email, $password, $roleId, $status);
                    $message = "User <strong>{$username}</strong> created successfully.";
                } catch (Exception $e) {
                    $error = 'Failed to create user: ' . $e->getMessage();
                }
            }
        }
    }

    // ── EDIT USER ──
    elseif (isset($_POST['edit_user'])) {
        if (!Security::validateCSRFToken($token)) {
            $error = 'Invalid CSRF token.';
        } else {
            $userId = (int)($_POST['user_id'] ?? 0);
            $username = Security::clean($_POST['username'] ?? '');
            $email = Security::clean($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $roleId = (int)($_POST['role_id'] ?? 1);
            $status = Security::clean($_POST['status'] ?? 'active');

            if (!$username || !$email) {
                $error = 'Username and email are required.';
            } else {
                $dup = $db->fetchOne("SELECT id FROM admins WHERE username = ? AND id != ?", [$username, $userId]);
                if ($dup) {
                    $error = 'Username already taken by another user.';
                } else {
                    $dupEmail = $db->fetchOne("SELECT id FROM admins WHERE email = ? AND id != ?", [$email, $userId]);
                    if ($dupEmail) {
                        $error = 'Email already taken by another user.';
                    } else {
                        $data = [
                            'username' => $username,
                            'email' => $email,
                            'role_id' => $roleId,
                            'status' => $status,
                        ];

                        if (!empty($password)) {
                            if (strlen($password) < 8) {
                                $error = 'Password must be at least 8 characters.';
                            } else {
                                $data['password_hash'] = Auth::hashPassword($password);
                            }
                        }

                        if (!$error) {
                            $db->update('admins', $data, 'id = ?', [$userId]);
                            $message = "User <strong>{$username}</strong> updated successfully.";
                        }
                    }
                }
            }
        }
    }

    // ── DELETE USER ──
    elseif (isset($_POST['delete_user'])) {
        if (!Security::validateCSRFToken($token)) {
            $error = 'Invalid CSRF token.';
        } else {
            $userId = (int)($_POST['user_id'] ?? 0);
            if ($userId === Auth::userId()) {
                $error = 'You cannot delete your own account.';
            } else {
                $db->delete('admins', 'id = ?', [$userId]);
                $message = 'User deleted successfully.';
            }
        }
    }

    // ── ADD ROLE ──
    elseif (isset($_POST['add_role'])) {
        $tab = 'roles';
        if (!Security::validateCSRFToken($token)) {
            $error = 'Invalid CSRF token.';
        } else {
            Auth::ensureRolesTable();
            $name = Security::clean($_POST['role_name'] ?? '');
            $slug = Security::clean($_POST['role_slug'] ?? '');
            $description = Security::clean($_POST['role_description'] ?? '');
            $permissions = $_POST['permissions'] ?? [];

            if (empty($slug)) {
                $slug = strtolower(preg_replace('/[^a-z0-9]+/', '_', strtolower($name)));
            }

            if (!$name) {
                $error = 'Role name is required.';
            } else {
                try {
                    $existing = $db->fetchOne("SELECT id FROM roles WHERE slug = ?", [$slug]);
                    if ($existing) {
                        $error = 'A role with this slug already exists.';
                    } else {
                        $db->insert('roles', [
                            'name' => $name,
                            'slug' => $slug,
                            'description' => $description,
                            'permissions' => json_encode(array_values($permissions)),
                        ]);
                        $message = "Role <strong>{$name}</strong> created successfully.";
                    }
                } catch (Exception $e) {
                    $error = 'Failed to create role: ' . $e->getMessage();
                }
            }
        }
    }

    // ── EDIT ROLE ──
    elseif (isset($_POST['edit_role'])) {
        $tab = 'roles';
        if (!Security::validateCSRFToken($token)) {
            $error = 'Invalid CSRF token.';
        } else {
            $roleId = (int)($_POST['role_id'] ?? 0);
            $name = Security::clean($_POST['role_name'] ?? '');
            $slug = Security::clean($_POST['role_slug'] ?? '');
            $description = Security::clean($_POST['role_description'] ?? '');
            $permissions = $_POST['permissions'] ?? [];

            if (!$name) {
                $error = 'Role name is required.';
            } else {
                try {
                    // Prevent editing super_admin slug
                    $existing = $db->fetchOne("SELECT slug FROM roles WHERE id = ?", [$roleId]);
                    
                    $data = [
                        'name' => $name,
                        'description' => $description,
                        'permissions' => json_encode(array_values($permissions)),
                    ];

                    // Only allow slug change if not super_admin
                    if ($existing && $existing['slug'] !== 'super_admin') {
                        $data['slug'] = $slug;
                    }

                    $db->update('roles', $data, 'id = ?', [$roleId]);
                    $message = "Role <strong>{$name}</strong> updated successfully.";
                } catch (Exception $e) {
                    $error = 'Failed to update role: ' . $e->getMessage();
                }
            }
        }
    }

    // ── DELETE ROLE ──
    elseif (isset($_POST['delete_role'])) {
        $tab = 'roles';
        if (!Security::validateCSRFToken($token)) {
            $error = 'Invalid CSRF token.';
        } else {
            $roleId = (int)($_POST['role_id'] ?? 0);
            $role = $db->fetchOne("SELECT slug FROM roles WHERE id = ?", [$roleId]);

            if ($role && $role['slug'] === 'super_admin') {
                $error = 'Cannot delete the Super Admin role.';
            } else {
                // Check if any users use this role
                try {
                    $usersWithRole = $db->fetchOne("SELECT COUNT(*) as cnt FROM admins WHERE role_id = ?", [$roleId]);
                    if ($usersWithRole && $usersWithRole['cnt'] > 0) {
                        $error = 'Cannot delete this role — ' . $usersWithRole['cnt'] . ' user(s) are assigned to it.';
                    } else {
                        $db->delete('roles', 'id = ?', [$roleId]);
                        $message = 'Role deleted successfully.';
                    }
                } catch (Exception $e) {
                    $db->delete('roles', 'id = ?', [$roleId]);
                    $message = 'Role deleted successfully.';
                }
            }
        }
    }
}

// ══════════════════════════════════════
//  FETCH DATA
// ══════════════════════════════════════

// Check if editing a user
if (isset($_GET['edit']) && $tab === 'users') {
    try {
        $editUser = $db->fetchOne(
            "SELECT a.*, r.name as role_name FROM admins a LEFT JOIN roles r ON a.role_id = r.id WHERE a.id = ?",
            [(int)$_GET['edit']]
        );
    } catch (Exception $e) {
        $editUser = $db->fetchOne("SELECT * FROM admins WHERE id = ?", [(int)$_GET['edit']]);
    }
}

// Check if editing a role
if (isset($_GET['edit_role'])) {
    $tab = 'roles';
    try {
        $editRole = $db->fetchOne("SELECT * FROM roles WHERE id = ?", [(int)$_GET['edit_role']]);
    } catch (Exception $e) {
        $editRole = null;
    }
}

// Fetch all users
try {
    $users = $db->fetchAll(
        "SELECT a.*, r.name as role_name, r.slug as role_slug 
         FROM admins a 
         LEFT JOIN roles r ON a.role_id = r.id 
         ORDER BY a.id ASC"
    );
} catch (Exception $e) {
    $users = $db->fetchAll("SELECT * FROM admins ORDER BY id ASC");
}

// Fetch all roles
$roles = Auth::getRoles();
?>

<!-- Tab Navigation -->
<div class="page-header" style="margin-bottom: 0;">
    <div>
        <h1 class="page-title">Users & Roles</h1>
        <p class="page-subtitle">Manage admin users and access control.</p>
    </div>
</div>

<div class="tabs-nav" style="margin-bottom: 24px;">
    <a href="?tab=users" class="tab-link <?= $tab === 'users' ? 'active' : '' ?>">
        <i class="fas fa-users"></i> Users
    </a>
    <a href="?tab=roles" class="tab-link <?= $tab === 'roles' ? 'active' : '' ?>">
        <i class="fas fa-shield-alt"></i> Roles
    </a>
</div>

<?php if ($message): ?>
<div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $message ?></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= e($error) ?></div>
<?php endif; ?>

<?php if ($tab === 'users'): ?>
<!-- ═══════════════════════════════════ -->
<!--          USERS TAB                 -->
<!-- ═══════════════════════════════════ -->
<div class="grid grid-3" style="grid-template-columns: 2fr 1fr; gap: 24px;">
    <!-- Users List -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Users (<?= count($users) ?>)</h3>
        </div>
        <div class="card-body p-0">
            <table class="table data-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div class="user-avatar">
                                    <?= strtoupper(substr($u['username'], 0, 1)) ?>
                                </div>
                                <div>
                                    <strong style="display: block;"><?= e($u['username']) ?></strong>
                                    <small style="color: var(--color-gray-500);"><?= e($u['email']) ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php
                            $badgeColor = match($u['role_slug'] ?? '') {
                                'super_admin' => 'background: #fee2e2; color: #dc2626;',
                                'editor' => 'background: #dbeafe; color: #2563eb;',
                                'viewer' => 'background: #f3f4f6; color: #6b7280;',
                                default => 'background: #fef3c7; color: #d97706;',
                            };
                            ?>
                            <span class="role-badge" style="<?= $badgeColor ?>">
                                <?= e($u['role_name'] ?? 'No Role') ?>
                            </span>
                        </td>
                        <td>
                            <?php if (($u['status'] ?? 'active') === 'active'): ?>
                                <span class="status-dot active"><i class="fas fa-circle"></i> Active</span>
                            <?php else: ?>
                                <span class="status-dot inactive"><i class="fas fa-circle"></i> Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($u['last_login'] ?? null): ?>
                                <small><?= date('M d, Y H:i', strtotime($u['last_login'])) ?></small>
                            <?php else: ?>
                                <small style="color: var(--color-gray-400);">Never</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <a href="?edit=<?= $u['id'] ?>" class="btn-icon" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if ($u['id'] !== Auth::userId()): ?>
                                <form method="POST" action="" onsubmit="return confirm('Are you sure you want to delete this user?');" style="display: inline;">
                                    <?= Security::csrfField() ?>
                                    <input type="hidden" name="delete_user" value="1">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <button type="submit" class="btn-icon text-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($users)): ?>
                    <tr><td colspan="5" style="text-align: center; padding: 30px; color: var(--color-gray-400);">No users found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add/Edit User Form -->
    <div class="card h-fit">
        <div class="card-header">
            <h3 class="card-title"><?= $editUser ? 'Edit User' : 'Add New User' ?></h3>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <?= Security::csrfField() ?>
                <?php if ($editUser): ?>
                    <input type="hidden" name="edit_user" value="1">
                    <input type="hidden" name="user_id" value="<?= $editUser['id'] ?>">
                <?php else: ?>
                    <input type="hidden" name="add_user" value="1">
                <?php endif; ?>

                <div class="form-group">
                    <label class="form-label">Username <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="username" class="form-control" 
                           value="<?= e($editUser['username'] ?? '') ?>" required
                           placeholder="e.g. john_doe">
                </div>

                <div class="form-group">
                    <label class="form-label">Email <span style="color: #ef4444;">*</span></label>
                    <input type="email" name="email" class="form-control" 
                           value="<?= e($editUser['email'] ?? '') ?>" required
                           placeholder="e.g. john@example.com">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Password <?= $editUser ? '' : '<span style="color: #ef4444;">*</span>' ?>
                    </label>
                    <input type="password" name="password" class="form-control" 
                           <?= $editUser ? '' : 'required' ?> minlength="8"
                           placeholder="<?= $editUser ? 'Leave blank to keep current' : 'Min 8 characters' ?>">
                    <?php if ($editUser): ?>
                    <small class="form-text">Leave blank to keep the current password.</small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label">Role <span style="color: #ef4444;">*</span></label>
                    <select name="role_id" class="form-control" required>
                        <?php if (empty($roles)): ?>
                        <option value="1">Super Admin (default)</option>
                        <?php else: ?>
                        <?php foreach ($roles as $role): ?>
                        <option value="<?= $role['id'] ?>" 
                                <?= ($editUser && ($editUser['role_id'] ?? 0) == $role['id']) ? 'selected' : '' ?>>
                            <?= e($role['name']) ?>
                        </option>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="active" <?= ($editUser && ($editUser['status'] ?? 'active') === 'active') || !$editUser ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= ($editUser && ($editUser['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>

                <div class="form-group mt-4">
                    <?php if ($editUser): ?>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                        <a href="users.php" class="btn btn-white w-100 mt-2">Cancel</a>
                    <?php else: ?>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-user-plus"></i> Create User
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<?php else: ?>
<!-- ═══════════════════════════════════ -->
<!--          ROLES TAB                 -->
<!-- ═══════════════════════════════════ -->
<div class="grid grid-3" style="grid-template-columns: 2fr 1fr; gap: 24px;">
    <!-- Roles List -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Roles (<?= count($roles) ?>)</h3>
        </div>
        <div class="card-body p-0">
            <table class="table data-table">
                <thead>
                    <tr>
                        <th>Role</th>
                        <th>Permissions</th>
                        <th>Users</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($roles as $role): ?>
                    <?php 
                    $perms = json_decode($role['permissions'] ?? '[]', true) ?: [];
                    $userCount = 0;
                    try {
                        $cnt = $db->fetchOne("SELECT COUNT(*) as cnt FROM admins WHERE role_id = ?", [$role['id']]);
                        $userCount = $cnt['cnt'] ?? 0;
                    } catch (Exception $e) {}
                    ?>
                    <tr>
                        <td>
                            <strong style="display: block;"><?= e($role['name']) ?></strong>
                            <small style="color: var(--color-gray-500);"><?= e($role['slug']) ?></small>
                            <?php if ($role['description'] ?? ''): ?>
                            <div style="font-size: 0.8rem; color: var(--color-gray-400); margin-top: 2px;">
                                <?= e($role['description']) ?>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (in_array('*', $perms)): ?>
                                <span class="perm-tag perm-full">Full Access</span>
                            <?php elseif (empty($perms)): ?>
                                <span style="color: var(--color-gray-400); font-size: 0.85rem;">None</span>
                            <?php else: ?>
                                <div class="perm-tags">
                                    <?php foreach ($perms as $p): ?>
                                    <span class="perm-tag"><?= e($p) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span style="font-weight: 500;"><?= $userCount ?></span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <a href="?tab=roles&edit_role=<?= $role['id'] ?>" class="btn-icon" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if (($role['slug'] ?? '') !== 'super_admin'): ?>
                                <form method="POST" action="" onsubmit="return confirm('Delete this role? Users assigned to it must be reassigned first.');" style="display: inline;">
                                    <?= Security::csrfField() ?>
                                    <input type="hidden" name="delete_role" value="1">
                                    <input type="hidden" name="role_id" value="<?= $role['id'] ?>">
                                    <button type="submit" class="btn-icon text-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($roles)): ?>
                    <tr><td colspan="4" style="text-align: center; padding: 30px; color: var(--color-gray-400);">No roles found. Run the migration SQL first.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add/Edit Role Form -->
    <div class="card h-fit">
        <div class="card-header">
            <h3 class="card-title"><?= $editRole ? 'Edit Role' : 'Create New Role' ?></h3>
        </div>
        <div class="card-body">
            <?php 
            $editPerms = [];
            if ($editRole) {
                $editPerms = json_decode($editRole['permissions'] ?? '[]', true) ?: [];
            }
            $isSuperAdmin = $editRole && ($editRole['slug'] ?? '') === 'super_admin';
            ?>
            <form method="POST" action="">
                <?= Security::csrfField() ?>
                <?php if ($editRole): ?>
                    <input type="hidden" name="edit_role" value="1">
                    <input type="hidden" name="role_id" value="<?= $editRole['id'] ?>">
                <?php else: ?>
                    <input type="hidden" name="add_role" value="1">
                <?php endif; ?>

                <div class="form-group">
                    <label class="form-label">Role Name <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="role_name" class="form-control" 
                           value="<?= e($editRole['name'] ?? '') ?>" required
                           placeholder="e.g. Content Manager">
                </div>

                <div class="form-group">
                    <label class="form-label">Slug</label>
                    <input type="text" name="role_slug" class="form-control" 
                           value="<?= e($editRole['slug'] ?? '') ?>"
                           placeholder="Auto-generated from name"
                           <?= $isSuperAdmin ? 'disabled' : '' ?>>
                    <small class="form-text">Unique identifier. Leave blank to auto-generate.</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <input type="text" name="role_description" class="form-control" 
                           value="<?= e($editRole['description'] ?? '') ?>"
                           placeholder="Brief description of this role">
                </div>

                <div class="form-group">
                    <label class="form-label" style="margin-bottom: 12px;">Permissions</label>
                    
                    <?php if ($isSuperAdmin): ?>
                    <div class="perm-notice">
                        <i class="fas fa-infinity"></i> Super Admin always has full access — permissions cannot be modified.
                        <input type="hidden" name="permissions[]" value="*">
                    </div>
                    <?php else: ?>
                    
                    <label class="perm-checkbox perm-select-all" style="margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid var(--color-gray-200);">
                        <input type="checkbox" id="selectAll" onchange="toggleAll(this)">
                        <span><strong>Select All</strong></span>
                    </label>

                    <?php foreach ($allPermissions as $group => $modules): ?>
                    <div class="perm-group">
                        <div class="perm-group-title"><?= $group ?></div>
                        <?php foreach ($modules as $key => $label): ?>
                        <label class="perm-checkbox">
                            <input type="checkbox" name="permissions[]" value="<?= $key ?>" class="perm-cb"
                                   <?= in_array($key, $editPerms) ? 'checked' : '' ?>>
                            <span><?= e($label) ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="form-group mt-4">
                    <?php if ($editRole): ?>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-save"></i> Update Role
                        </button>
                        <a href="?tab=roles" class="btn btn-white w-100 mt-2">Cancel</a>
                    <?php else: ?>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-plus"></i> Create Role
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
/* ── Tabs ── */
.tabs-nav {
    display: flex;
    gap: 0;
    border-bottom: 2px solid var(--color-gray-200);
    margin-top: 16px;
}
.tab-link {
    padding: 12px 24px;
    text-decoration: none;
    color: var(--color-gray-500);
    font-weight: 500;
    font-size: 0.95rem;
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 8px;
}
.tab-link:hover {
    color: var(--color-gray-800);
}
.tab-link.active {
    color: var(--color-primary, #000);
    border-bottom-color: var(--color-primary, #000);
}

/* ── User Avatar ── */
.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--color-gray-200);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: var(--color-gray-600);
    font-size: 0.9rem;
    flex-shrink: 0;
}

/* ── Role Badge ── */
.role-badge {
    padding: 4px 12px;
    border-radius: 9999px;
    font-size: 0.8rem;
    font-weight: 600;
    white-space: nowrap;
}

/* ── Status Dot ── */
.status-dot {
    font-weight: 500;
    font-size: 0.85rem;
    white-space: nowrap;
}
.status-dot i {
    font-size: 0.45rem;
    vertical-align: middle;
    margin-right: 4px;
}
.status-dot.active { color: #16a34a; }
.status-dot.inactive { color: #9ca3af; }

/* ── Permission Tags ── */
.perm-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
}
.perm-tag {
    padding: 2px 10px;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    background: var(--color-gray-100);
    color: var(--color-gray-600);
}
.perm-tag.perm-full {
    background: #fee2e2;
    color: #dc2626;
    font-weight: 600;
}

/* ── Permission Checkboxes ── */
.perm-group {
    margin-bottom: 16px;
}
.perm-group-title {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    color: var(--color-gray-400);
    letter-spacing: 0.5px;
    margin-bottom: 6px;
}
.perm-checkbox {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 6px 0;
    cursor: pointer;
    font-size: 0.9rem;
}
.perm-checkbox input[type="checkbox"] {
    width: 17px;
    height: 17px;
    accent-color: var(--color-primary, #000);
    cursor: pointer;
}
.perm-notice {
    background: #fef3c7;
    color: #92400e;
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* ── Utilities ── */
.btn-icon {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    color: var(--color-gray-500);
    border: 1px solid var(--color-gray-200);
    background: white;
    transition: all 0.2s;
    cursor: pointer;
    text-decoration: none;
}
.btn-icon:hover {
    background: var(--color-gray-50);
    color: var(--color-primary);
    border-color: var(--color-primary);
}
.btn-icon.text-danger:hover {
    background: #fee2e2;
    color: #ef4444;
    border-color: #fca5a5;
}
.h-fit {
    height: fit-content;
    position: sticky;
    top: 24px;
}

@media (max-width: 1024px) {
    .grid-3[style*="grid-template-columns: 2fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}
</style>

<script>
function toggleAll(el) {
    document.querySelectorAll('.perm-cb').forEach(cb => cb.checked = el.checked);
}

// Sync select-all checkbox state
document.querySelectorAll('.perm-cb').forEach(cb => {
    cb.addEventListener('change', () => {
        const all = document.querySelectorAll('.perm-cb');
        const checked = document.querySelectorAll('.perm-cb:checked');
        const selectAll = document.getElementById('selectAll');
        if (selectAll) selectAll.checked = all.length === checked.length;
    });
});

// Initialize select-all state
(function() {
    const all = document.querySelectorAll('.perm-cb');
    const checked = document.querySelectorAll('.perm-cb:checked');
    const selectAll = document.getElementById('selectAll');
    if (selectAll && all.length > 0) selectAll.checked = all.length === checked.length;
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
