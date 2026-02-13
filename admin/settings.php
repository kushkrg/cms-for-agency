<?php
/**
 * Evolvcode CMS - Admin Settings
 */

$pageTitle = 'Settings';
require_once __DIR__ . '/includes/header.php';
Auth::requirePermission('settings');

$db = Database::getInstance();
$message = '';
$error = '';

// Current tab
$tab = $_GET['tab'] ?? 'general';

// Handle form submission
if (Security::isPost()) {
    $token = $_POST[CSRF_TOKEN_NAME] ?? '';
    if (!Security::validateCSRFToken($token)) {
        $error = 'Invalid form submission.';
    } else {
        $activeTab = $_POST['active_tab'] ?? 'general';
        $settings = [];

        if ($activeTab === 'general') {
            $settings = [
                'site_name' => Security::clean($_POST['site_name'] ?? ''),
                'site_tagline' => Security::clean($_POST['site_tagline'] ?? ''),
                'site_description' => Security::clean($_POST['site_description'] ?? ''),
                'contact_email' => Security::clean($_POST['contact_email'] ?? ''),
                'contact_phone' => Security::clean($_POST['contact_phone'] ?? ''),
                'whatsapp_number' => Security::clean($_POST['whatsapp_number'] ?? ''),
                'address' => Security::clean($_POST['address'] ?? ''),
                'facebook_url' => Security::clean($_POST['facebook_url'] ?? ''),
                'twitter_url' => Security::clean($_POST['twitter_url'] ?? ''),
                'linkedin_url' => Security::clean($_POST['linkedin_url'] ?? ''),
                'instagram_url' => Security::clean($_POST['instagram_url'] ?? ''),
                'youtube_url' => Security::clean($_POST['youtube_url'] ?? ''),
            ];


            // Handle logo upload
            if (!empty($_FILES['logo']['name'])) {
                $uploader = new FileUpload();
                $uploadedPath = $uploader->uploadImage($_FILES['logo'], 'logo');
                
                if ($uploadedPath) {
                    $existing = $db->fetchOne("SELECT id FROM settings WHERE setting_key = 'logo_path'");
                    if ($existing) {
                        $db->update('settings', ['setting_value' => $uploadedPath], 'id = ?', [$existing['id']]);
                    } else {
                        $db->insert('settings', ['setting_key' => 'logo_path', 'setting_value' => $uploadedPath]);
                    }
                } else {
                    $error = 'Logo upload failed: ' . implode(' ', $uploader->getErrors());
                }
            }

            // Handle white logo upload
            if (!empty($_FILES['logo_white']['name'])) {
                $uploader = new FileUpload();
                $uploadedPath = $uploader->uploadImage($_FILES['logo_white'], 'logo-white');
                
                if ($uploadedPath) {
                    $existing = $db->fetchOne("SELECT id FROM settings WHERE setting_key = 'logo_white_path'");
                    if ($existing) {
                        $db->update('settings', ['setting_value' => $uploadedPath], 'id = ?', [$existing['id']]);
                    } else {
                        $db->insert('settings', ['setting_key' => 'logo_white_path', 'setting_value' => $uploadedPath]);
                    }
                } else {
                    $error = ($error ? $error . ' ' : '') . 'White Logo upload failed: ' . implode(' ', $uploader->getErrors());
                }
            }


        } elseif ($activeTab === 'integrations') {
            $settings = [
                'google_analytics' => Security::clean($_POST['google_analytics'] ?? ''),
                'recaptcha_site_key' => Security::clean($_POST['recaptcha_site_key'] ?? ''),
                'recaptcha_secret_key' => Security::clean($_POST['recaptcha_secret_key'] ?? ''),
                'custom_css' => $_POST['custom_css'] ?? '',
                'custom_js_head' => $_POST['custom_js_head'] ?? '',
                'custom_js_body' => $_POST['custom_js_body'] ?? '',
            ];

        } elseif ($activeTab === 'advanced') {
            $settings = [
                'footer_text' => Security::clean($_POST['footer_text'] ?? ''),
            ];

        } elseif ($activeTab === 'smtp') {
            $settings = [
                'smtp_host' => Security::clean($_POST['smtp_host'] ?? ''),
                'smtp_port' => Security::clean($_POST['smtp_port'] ?? '587'),
                'smtp_user' => Security::clean($_POST['smtp_user'] ?? ''),
                'smtp_encryption' => Security::clean($_POST['smtp_encryption'] ?? 'tls'),
                'smtp_from_email' => Security::clean($_POST['smtp_from_email'] ?? ''),
                'smtp_from_name' => Security::clean($_POST['smtp_from_name'] ?? ''),
            ];
            
            // Only update password if provided
            if (!empty($_POST['smtp_pass'])) {
                $settings['smtp_pass'] = $_POST['smtp_pass'];
            }
        }
        
        foreach ($settings as $key => $value) {
            $existing = $db->fetchOne("SELECT id FROM settings WHERE setting_key = ?", [$key]);
            if ($existing) {
                $db->update('settings', ['setting_value' => $value], 'id = ?', [$existing['id']]);
            } else {
                $db->insert('settings', ['setting_key' => $key, 'setting_value' => $value]);
            }
        }
        
        if (empty($error)) {
            $message = 'Settings saved successfully.';
        }
        
        // redirect to keep tab active
        if (isset($_POST['active_tab'])) {
            $tab = $_POST['active_tab'];
        }
    }
}

// Get current settings
$settings = [];
$rows = $db->fetchAll("SELECT setting_key, setting_value FROM settings");
foreach ($rows as $row) {
    if (in_array($row['setting_key'], ['custom_css', 'custom_js_head', 'custom_js_body'])) {
        $settings[$row['setting_key']] = $row['setting_value']; // Raw for code
    } else {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}
?>

<div class="page-header" style="margin-bottom: 0;">
    <div>
        <h1 class="page-title">Site Settings</h1>
        <p class="page-subtitle">Configure your website settings</p>
    </div>
</div>

<div class="tabs-nav" style="margin-bottom: 24px;">
    <a href="?tab=general" class="tab-link <?= $tab === 'general' ? 'active' : '' ?>">
        <i class="fas fa-sliders-h"></i> General
    </a>
    <a href="?tab=integrations" class="tab-link <?= $tab === 'integrations' ? 'active' : '' ?>">
        <i class="fas fa-plug"></i> Integrations
    </a>
    <a href="?tab=advanced" class="tab-link <?= $tab === 'advanced' ? 'active' : '' ?>">
        <i class="fas fa-toolbox"></i> Advanced
    </a>
    <a href="?tab=smtp" class="tab-link <?= $tab === 'smtp' ? 'active' : '' ?>">
        <i class="fas fa-envelope"></i> SMTP
    </a>
</div>

<?php if ($message): ?>
<div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= e($message) ?></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= e($error) ?></div>
<?php endif; ?>

<form method="POST" action="?tab=<?= $tab ?>" enctype="multipart/form-data">
    <?= Security::csrfField() ?>
    <input type="hidden" name="active_tab" value="<?= $tab ?>">
    
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <!-- Left Column: Tab Content -->
        <div>
            
            <!-- GENERAL TAB -->
            <?php if ($tab === 'general'): ?>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">General Settings</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="site_name" class="form-label">Site Name</label>
                        <input type="text" name="site_name" id="site_name" class="form-control" 
                               value="<?= e($settings['site_name'] ?? 'Evolvcode Solutions') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="site_tagline" class="form-label">Tagline</label>
                        <input type="text" name="site_tagline" id="site_tagline" class="form-control" 
                               value="<?= e($settings['site_tagline'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="site_description" class="form-label">Site Description</label>
                        <textarea name="site_description" id="site_description" class="form-control" 
                                  rows="3"><?= e($settings['site_description'] ?? '') ?></textarea>
                        <p class="form-text">Used for SEO meta description.</p>
                    </div>
                </div>
            </div>
            
            <div class="card" style="margin-top: 24px;">
                <div class="card-header">
                    <h3 class="card-title">Contact Information</h3>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="contact_email" class="form-label">Email Address</label>
                            <input type="email" name="contact_email" id="contact_email" class="form-control" 
                                   value="<?= e($settings['contact_email'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="contact_phone" class="form-label">Phone Number</label>
                            <input type="text" name="contact_phone" id="contact_phone" class="form-control" 
                                   value="<?= e($settings['contact_phone'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="whatsapp_number" class="form-label">WhatsApp Number</label>
                        <input type="text" name="whatsapp_number" id="whatsapp_number" class="form-control" 
                               value="<?= e($settings['whatsapp_number'] ?? '') ?>" 
                               placeholder="e.g., 919229045881 (without + sign)">
                    </div>
                    
                    <div class="form-group">
                        <label for="address" class="form-label">Address</label>
                        <textarea name="address" id="address" class="form-control" 
                                  rows="2"><?= e($settings['address'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <div class="card" style="margin-top: 24px;">
                <div class="card-header">
                    <h3 class="card-title">Social Media Links</h3>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="facebook_url" class="form-label">
                                <i class="fab fa-facebook"></i> Facebook URL
                            </label>
                            <input type="url" name="facebook_url" id="facebook_url" class="form-control" 
                                   value="<?= e($settings['facebook_url'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="twitter_url" class="form-label">
                                <i class="fab fa-twitter"></i> Twitter URL
                            </label>
                            <input type="url" name="twitter_url" id="twitter_url" class="form-control" 
                                   value="<?= e($settings['twitter_url'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="linkedin_url" class="form-label">
                                <i class="fab fa-linkedin"></i> LinkedIn URL
                            </label>
                            <input type="url" name="linkedin_url" id="linkedin_url" class="form-control" 
                                   value="<?= e($settings['linkedin_url'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="instagram_url" class="form-label">
                                <i class="fab fa-instagram"></i> Instagram URL
                            </label>
                            <input type="url" name="instagram_url" id="instagram_url" class="form-control" 
                                   value="<?= e($settings['instagram_url'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="youtube_url" class="form-label">
                            <i class="fab fa-youtube"></i> YouTube URL
                        </label>
                        <input type="url" name="youtube_url" id="youtube_url" class="form-control" 
                               value="<?= e($settings['youtube_url'] ?? '') ?>">
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- INTEGRATIONS TAB -->
            <?php if ($tab === 'integrations'): ?>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Google Services</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="google_analytics" class="form-label">Google Analytics ID</label>
                        <input type="text" name="google_analytics" id="google_analytics" class="form-control" 
                               value="<?= e($settings['google_analytics'] ?? '') ?>" 
                               placeholder="e.g., G-XXXXXXXXXX">
                    </div>
                </div>
            </div>

            <div class="card" style="margin-top: 24px;">
                <div class="card-header">
                    <h3 class="card-title">Google reCAPTCHA v3</h3>
                </div>
                <div class="card-body">
                    <p class="form-text" style="margin-bottom: 16px;">
                        Protect your forms from spam. Get your keys from the <a href="https://www.google.com/recaptcha/admin/create" target="_blank">Google reCAPTCHA Admin Console</a>.
                    </p>
                    <div class="form-group">
                        <label for="recaptcha_site_key" class="form-label">Site Key</label>
                        <input type="text" name="recaptcha_site_key" id="recaptcha_site_key" class="form-control" 
                               value="<?= e($settings['recaptcha_site_key'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="recaptcha_secret_key" class="form-label">Secret Key</label>
                        <input type="text" name="recaptcha_secret_key" id="recaptcha_secret_key" class="form-control" 
                               value="<?= e($settings['recaptcha_secret_key'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <div class="card" style="margin-top: 24px;">
                <div class="card-header">
                    <h3 class="card-title">Custom Code</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="custom_css" class="form-label">Global Custom CSS</label>
                        <textarea name="custom_css" id="custom_css" class="form-control code-editor" 
                                  rows="6" placeholder="body { background-color: #f0f0f0; }"><?= htmlspecialchars($settings['custom_css'] ?? '') ?></textarea>
                        <p class="form-text">Applied to all pages. Wrapped in <code>&lt;style&gt;</code> automatically.</p>
                    </div>

                    <div class="form-group">
                        <label for="custom_js_head" class="form-label">Custom JavaScript (HEAD)</label>
                        <textarea name="custom_js_head" id="custom_js_head" class="form-control code-editor" 
                                  rows="6" placeholder="<script>...</script> or <meta ...>"><?= htmlspecialchars($settings['custom_js_head'] ?? '') ?></textarea>
                        <p class="form-text">Injected before <code>&lt;/head&gt;</code>. Useful for tracking codes, meta tags, etc.</p>
                    </div>

                    <div class="form-group">
                        <label for="custom_js_body" class="form-label">Custom JavaScript (BODY)</label>
                        <textarea name="custom_js_body" id="custom_js_body" class="form-control code-editor" 
                                  rows="6" placeholder="<script>...</script>"><?= htmlspecialchars($settings['custom_js_body'] ?? '') ?></textarea>
                        <p class="form-text">Injected before <code>&lt;/body&gt;</code>. Useful for chat widgets, analytics, etc.</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- ADVANCED TAB -->
            <?php if ($tab === 'advanced'): ?>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Advanced Settings</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="footer_text" class="form-label">Footer Copyright Text</label>
                        <input type="text" name="footer_text" id="footer_text" class="form-control" 
                               value="<?= e($settings['footer_text'] ?? '© ' . date('Y') . ' Evolvcode Solutions. All rights reserved.') ?>">
                    </div>
                    <!-- Moved Analytics to Integrations -->
                </div>
            </div>
            <?php endif; ?>

            <!-- SMTP TAB -->
            <?php if ($tab === 'smtp'): ?>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-envelope"></i> Email (SMTP) Settings</h3>
                </div>
                <div class="card-body">
                    <p class="form-text" style="margin-bottom: 16px;">
                        Configure SMTP to send form notification emails. Leave empty to use PHP's default mail function.
                    </p>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="smtp_host" class="form-label">SMTP Host</label>
                            <input type="text" name="smtp_host" id="smtp_host" class="form-control" 
                                   value="<?= e($settings['smtp_host'] ?? '') ?>" 
                                   placeholder="e.g., smtp.gmail.com">
                        </div>
                        
                        <div class="form-group">
                            <label for="smtp_port" class="form-label">SMTP Port</label>
                            <input type="number" name="smtp_port" id="smtp_port" class="form-control" 
                                   value="<?= e($settings['smtp_port'] ?? '587') ?>" 
                                   placeholder="587">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="smtp_user" class="form-label">SMTP Username</label>
                            <input type="text" name="smtp_user" id="smtp_user" class="form-control" 
                                   value="<?= e($settings['smtp_user'] ?? '') ?>" 
                                   placeholder="your@email.com">
                        </div>
                        
                        <div class="form-group">
                            <label for="smtp_pass" class="form-label">SMTP Password</label>
                            <input type="password" name="smtp_pass" id="smtp_pass" class="form-control" 
                                   placeholder="<?= !empty($settings['smtp_pass']) ? '••••••••' : 'Enter password' ?>">
                            <?php if (!empty($settings['smtp_pass'])): ?>
                            <small class="form-text">Leave empty to keep existing password</small>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="smtp_encryption" class="form-label">Encryption</label>
                        <select name="smtp_encryption" id="smtp_encryption" class="form-control">
                            <option value="tls" <?= ($settings['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS (Recommended)</option>
                            <option value="ssl" <?= ($settings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                            <option value="none" <?= ($settings['smtp_encryption'] ?? '') === 'none' ? 'selected' : '' ?>>None</option>
                        </select>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="smtp_from_email" class="form-label">From Email</label>
                            <input type="email" name="smtp_from_email" id="smtp_from_email" class="form-control" 
                                   value="<?= e($settings['smtp_from_email'] ?? '') ?>" 
                                   placeholder="noreply@yourdomain.com">
                        </div>
                        
                        <div class="form-group">
                            <label for="smtp_from_name" class="form-label">From Name</label>
                            <input type="text" name="smtp_from_name" id="smtp_from_name" class="form-control" 
                                   value="<?= e($settings['smtp_from_name'] ?? '') ?>" 
                                   placeholder="Your Company Name">
                        </div>
                    </div>
                    
                    <div class="form-group" style="margin-top: 16px;">
                        <button type="button" id="testSmtpBtn" class="btn btn-secondary" onclick="testSmtp()">
                            <i class="fas fa-paper-plane"></i> Test SMTP Connection
                        </button>
                        <span id="smtpTestResult" style="margin-left: 12px;"></span>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
        
        <!-- Right Column: Sidebar -->
        <div>
            <div class="card h-fit">
                <div class="card-header">
                    <h3 class="card-title">Actions</h3>
                </div>
                <div class="card-body">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-save"></i> Save Settings
                    </button>
                </div>
            </div>
            
            <?php if ($tab === 'general'): ?>
            <div class="card" style="margin-top: 24px;">
                <div class="card-header">
                    <h3 class="card-title">Site Branding</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="logo" class="form-label">Primary Logo (Color/Dark)</label>
                        <input type="file" name="logo" id="logo" class="form-control" 
                               accept="image/*" data-preview="logo-preview">
                        <?php if (!empty($settings['logo_path'])): ?>
                        <img src="<?= e(SITE_URL . $settings['logo_path']) ?>" id="logo-preview" 
                             style="margin-top: 12px; max-width: 200px; border-radius: 8px; border: 1px solid #eee; padding: 5px;">
                        <?php else: ?>
                        <img id="logo-preview" style="margin-top: 12px; max-width: 200px; border-radius: 8px; display: none;">
                        <?php endif; ?>
                    </div>

                    <div class="form-group" style="margin-top: 20px; border-top: 1px dashed #eee; padding-top: 20px;">
                        <label for="logo_white" class="form-label">White Logo (For Dark Backgrounds)</label>
                        <input type="file" name="logo_white" id="logo_white" class="form-control" 
                               accept="image/*" data-preview="logo-white-preview">
                        <?php if (!empty($settings['logo_white_path'])): ?>
                        <div style="background: #333; padding: 10px; margin-top: 12px; border-radius: 8px; display: inline-block;">
                            <img src="<?= e(SITE_URL . $settings['logo_white_path']) ?>" id="logo-white-preview" 
                                 style="max-width: 200px; display: block;">
                        </div>
                        <?php else: ?>
                        <div id="logo-white-wrapper" style="background: #333; padding: 10px; margin-top: 12px; border-radius: 8px; display: none;">
                            <img id="logo-white-preview" style="max-width: 200px; display: block;">
                        </div>
                        <?php endif; ?>
                        <p class="form-text">Upload a white version of your logo for the footer and dark sections.</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</form>

<style>
@media (max-width: 1024px) {
    div[style*="grid-template-columns: 2fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}
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
.code-editor {
    font-family: 'Courier New', Courier, monospace;
    font-size: 0.9rem;
    background: #f9fafb;
}
</style>

<script>
function testSmtp() {
    const btn = document.getElementById('testSmtpBtn');
    const resultSpan = document.getElementById('smtpTestResult');
    
    // Original button text
    const originalText = btn.innerHTML;
    
    // Show loading state
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testing...';
    resultSpan.className = '';
    resultSpan.innerHTML = '';
    
    // Call API
    fetch('<?= e(SITE_URL) ?>/api/smtp-test.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                resultSpan.className = 'text-success';
                resultSpan.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
            } else {
                resultSpan.className = 'text-danger';
                resultSpan.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + data.message;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            resultSpan.className = 'text-danger';
            resultSpan.innerHTML = '<i class="fas fa-exclamation-circle"></i> Connection error';
        })
        .finally(() => {
            // Restore button
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
