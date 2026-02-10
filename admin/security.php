<?php
/**
 * Evolvcode CMS - Security Settings
 */

require_once __DIR__ . '/includes/header.php';
Auth::requirePermission('security');

// Handle Actions
$message = '';
$error = '';

if (Security::isPost()) {
    // CSRF Check
    if (!Security::validateCSRFToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
        $error = 'Invalid security token. Please refresh and try again.';
    } else {
        // Toggle 2FA
        if (isset($_POST['toggle_2fa'])) {
            $newValue = isset($_POST['security_2fa_enabled']) ? '1' : '0';
            
            // Should verify email settings before enabling?
            // For now, we trust the user knows what they're doing or has read warnings.
            
            $db->update('settings', ['setting_value' => $newValue], 'setting_key = ?', ['security_2fa_enabled']);
            $message = 'Security settings updated successfully.';
        }
        
        // Download Backup
        if (isset($_POST['download_backup'])) {
            try {
                $sqlContent = Backup::generate();
                $filename = 'evolvcode_backup_' . date('Y-m-d_H-i-s') . '.sql';
                
                // Clear output buffer
                if (ob_get_level()) ob_end_clean();
                
                // Headers for download
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Content-Length: ' . strlen($sqlContent));
                header('Pragma: no-cache');
                
                echo $sqlContent;
                exit;
            } catch (Exception $e) {
                $error = 'Backup failed: ' . $e->getMessage();
            }
        }
    }
}

// Get current settings
$is2faEnabled = getSetting('security_2fa_enabled', '0') === '1';

?>

<div class="admin-header">
    <div class="header-left">
        <h1 class="page-title">Security & Backup</h1>
    </div>
</div>

<?php if ($message): ?>
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i> <?= e($message) ?>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-error">
    <i class="fas fa-exclamation-circle"></i> <?= e($error) ?>
</div>
<?php endif; ?>

<div class="grid grid-2" style="gap: 24px;">
    
    <!-- Two-Factor Authentication -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-shield-alt"></i> Two-Factor Authentication</h3>
        </div>
        <div class="card-body">
            <p style="margin-bottom: 20px; color: var(--color-gray-600); line-height: 1.6;">
                Enhance your account security by requiring a One-Time Password (OTP) sent to your email address every time you log in.
            </p>
            
            <?php if (!$is2faEnabled): ?>
            <div class="alert alert-warning" style="background: #FFF3E0; color: #E65100; border: 1px solid #FFCC80;">
                <i class="fas fa-exclamation-triangle"></i> 
                <strong>Important:</strong> Ensure your SMTP email settings are correctly configured and working before enabling 2FA. If email fails, you may be locked out.
            </div>
            <?php else: ?>
            <div class="alert alert-success" style="background: #E8F5E9; color: #2E7D32; border: 1px solid #A5D6A7;">
                <i class="fas fa-check-circle"></i> 2FA is currently <strong>ENABLED</strong>. Your account is secure.
            </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <?= Security::csrfField() ?>
                <input type="hidden" name="toggle_2fa" value="1">
                
                <div class="form-group">
                    <label class="switch-label" style="display: flex; align-items: center; gap: 12px; cursor: pointer;">
                        <div class="switch">
                            <input type="checkbox" name="security_2fa_enabled" value="1" <?= $is2faEnabled ? 'checked' : '' ?>>
                            <span class="slider round"></span>
                        </div>
                        <span style="font-weight: 500;">Enable Two-Factor Authentication via Email</span>
                    </label>
                </div>
                
                <div class="form-footer" style="margin-top: 20px;">
                    <button type="submit" class="btn btn-primary">
                        Save Security Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Database Backup -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-database"></i> Database Backup</h3>
        </div>
        <div class="card-body">
            <p style="margin-bottom: 20px; color: var(--color-gray-600); line-height: 1.6;">
                Download a complete SQL dump of your database. This includes all tables, settings, content, and user data. recommended to be done regularly.
            </p>
            
            <div class="backup-info" style="background: #f8fafc; padding: 16px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #e2e8f0;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <strong>Database Type:</strong>
                    <span>MySQL / MariaDB</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <strong>Database Name:</strong>
                    <span><?= e(DB_NAME) ?></span>
                </div>
            </div>

            <form method="POST" action="">
                <?= Security::csrfField() ?>
                <input type="hidden" name="download_backup" value="1">
                
                <button type="submit" class="btn btn-outline" style="width: 100%; justify-content: center;">
                    <i class="fas fa-download"></i> Download Database Backup
                </button>
            </form>
        </div>
    </div>
    
</div>

<style>
/* Switch Toggle Styles */
.switch {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 26px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 20px;
  width: 20px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: var(--color-primary);
}

input:focus + .slider {
  box-shadow: 0 0 1px var(--color-primary);
}

input:checked + .slider:before {
  -webkit-transform: translateX(24px);
  -ms-transform: translateX(24px);
  transform: translateX(24px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
