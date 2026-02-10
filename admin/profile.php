<?php
/**
 * Evolvcode CMS - Admin Profile
 */

$pageTitle = 'My Profile';
require_once __DIR__ . '/includes/header.php';

$success = '';
$error = '';

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        
        if (empty($username) || empty($email)) {
            $error = 'Username and email are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email format.';
        } else {
            // Check if username/email already exists for OTHER users
            $db = Database::getInstance();
            $existing = $db->fetchOne(
                "SELECT id FROM admins WHERE (username = ? OR email = ?) AND id != ?", 
                [$username, $email, $currentAdmin['id']]
            );
            
            if ($existing) {
                $error = 'Username or email already taken.';
            } else {
                $db->query(
                    "UPDATE admins SET username = ?, email = ? WHERE id = ?", 
                    [$username, $email, $currentAdmin['id']]
                );
                
                // Update session
                $_SESSION['admin_username'] = $username;
                $currentAdmin['username'] = $username;
                $currentAdmin['email'] = $email;
                
                $success = 'Profile updated successfully.';
            }
        }
    }
}

// Handle Password Change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $currentPass = $_POST['current_password'];
        $newPass = $_POST['new_password'];
        $confirmPass = $_POST['confirm_password'];
        
        if (empty($currentPass) || empty($newPass)) {
            $error = 'All password fields are required.';
        } elseif ($newPass !== $confirmPass) {
            $error = 'New passwords do not match.';
        } elseif (strlen($newPass) < 8) {
            $error = 'Password must be at least 8 characters long.';
        } else {
            // Verify current password
            $db = Database::getInstance();
            $admin = $db->fetchOne("SELECT password FROM admins WHERE id = ?", [$currentAdmin['id']]);
            
            if (!password_verify($currentPass, $admin['password'])) {
                $error = 'Incorrect current password.';
            } else {
                $hash = password_hash($newPass, PASSWORD_DEFAULT);
                $db->query("UPDATE admins SET password = ? WHERE id = ?", [$hash, $currentAdmin['id']]);
                $success = 'Password changed successfully.';
            }
        }
    }
}

?>

<div class="admin-header">
    <div class="header-left">
        <h1>My Profile</h1>
    </div>
</div>

<?php if ($success): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= e($success) ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= e($error) ?></div>
<?php endif; ?>

<div class="grid-layout two-columns">
    <!-- Profile Info -->
    <div class="card">
        <div class="card-header">
            <h3>Account Information</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <?= Security::csrfField() ?>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" value="<?= e($currentAdmin['username']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" value="<?= e($currentAdmin['email']) ?>" required>
                </div>
                
                <div class="form-actions">
                    <button type="submit" name="update_profile" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Change Password -->
    <div class="card">
        <div class="card-header">
            <h3>Change Password</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <?= Security::csrfField() ?>
                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" class="form-control" required>
                    <small class="text-muted">Minimum 8 characters</small>
                </div>
                
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                
                <div class="form-actions">
                    <button type="submit" name="change_password" class="btn btn-warning">
                        <i class="fas fa-key"></i> Change Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.grid-layout {
    display: grid;
    gap: 24px;
}
.two-columns {
    grid-template-columns: 1fr 1fr;
}
@media (max-width: 768px) {
    .two-columns { grid-template-columns: 1fr; }
}
.alert {
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 24px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 10px;
}
.alert-success {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}
.alert-danger {
    background: #fee2e2;
    color: #b91c1c;
    border: 1px solid #fecaca;
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
