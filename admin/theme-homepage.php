<?php
/**
 * Evolvcode CMS - Homepage Theme Settings
 */

$pageTitle = 'Homepage Settings';
require_once __DIR__ . '/includes/header.php';
Auth::requirePermission('homepage');

$db = Database::getInstance();
$message = '';
$error = '';

// Handle form submission
if (Security::isPost()) {
    $token = $_POST[CSRF_TOKEN_NAME] ?? '';
    if (!Security::validateCSRFToken($token)) {
        $error = 'Invalid form submission.';
    } else {
        // Collect all settings
        $settings = [
            // Hero Section
            'home_hero_badge' => Security::clean($_POST['home_hero_badge'] ?? ''),
            'home_hero_title_1' => Security::clean($_POST['home_hero_title_1'] ?? ''),
            'home_hero_title_2' => Security::clean($_POST['home_hero_title_2'] ?? ''),
            'home_hero_description' => Security::clean($_POST['home_hero_description'] ?? ''),
            'home_hero_btn_primary_text' => Security::clean($_POST['home_hero_btn_primary_text'] ?? ''),
            'home_hero_btn_primary_link' => Security::clean($_POST['home_hero_btn_primary_link'] ?? ''),
            'home_hero_btn_secondary_text' => Security::clean($_POST['home_hero_btn_secondary_text'] ?? ''),
            'home_hero_btn_secondary_link' => Security::clean($_POST['home_hero_btn_secondary_link'] ?? ''),
            
            // Services Section
            'home_show_services' => isset($_POST['home_show_services']) ? '1' : '0',
            'home_services_label' => Security::clean($_POST['home_services_label'] ?? ''),
            'home_services_title' => Security::clean($_POST['home_services_title'] ?? ''),
            'home_services_desc' => Security::clean($_POST['home_services_desc'] ?? ''),
            
            // Why Choose Us Section
            'home_show_wcu' => isset($_POST['home_show_wcu']) ? '1' : '0',
            'home_wcu_label' => Security::clean($_POST['home_wcu_label'] ?? ''),
            'home_wcu_title' => Security::clean($_POST['home_wcu_title'] ?? ''),
            'home_wcu_desc' => Security::clean($_POST['home_wcu_desc'] ?? ''),
            
            // Featured Projects
            'home_show_projects' => isset($_POST['home_show_projects']) ? '1' : '0',
            'home_projects_label' => Security::clean($_POST['home_projects_label'] ?? ''),
            'home_projects_title' => Security::clean($_POST['home_projects_title'] ?? ''),
            'home_projects_desc' => Security::clean($_POST['home_projects_desc'] ?? ''),
            
            // Process Section
            'home_show_process' => isset($_POST['home_show_process']) ? '1' : '0',
            'home_process_label' => Security::clean($_POST['home_process_label'] ?? ''),
            'home_process_title' => Security::clean($_POST['home_process_title'] ?? ''),
            'home_process_desc' => Security::clean($_POST['home_process_desc'] ?? ''),
            
            // Trust/Transparency Section
            'home_show_trust' => isset($_POST['home_show_trust']) ? '1' : '0',
            
            // CTA Section
            'home_show_cta' => isset($_POST['home_show_cta']) ? '1' : '0',
            'home_cta_title' => Security::clean($_POST['home_cta_title'] ?? ''),
            'home_cta_desc' => Security::clean($_POST['home_cta_desc'] ?? ''),
            'home_cta_btn_text' => Security::clean($_POST['home_cta_btn_text'] ?? ''),
            'home_cta_btn_link' => Security::clean($_POST['home_cta_btn_link'] ?? ''),
        ];
        
        // Save Settings
        foreach ($settings as $key => $value) {
            updateSetting($key, $value);
        }
        
        $message = 'Homepage settings saved successfully.';
    }
}

// Helper to get setting with default fallback
function getVal($key, $default = '') {
    return e(getSetting($key, $default));
}

// Check checkbox state
function isChecked($key, $default = '1') {
    return getSetting($key, $default) === '1' ? 'checked' : '';
}
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Homepage Settings</h1>
        <p class="page-subtitle">Manage content and visibility of homepage sections.</p>
    </div>
</div>

<?php if ($message): ?>
<div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= e($message) ?></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= e($error) ?></div>
<?php endif; ?>

<form method="POST" action="">
    <?= Security::csrfField() ?>
    
    <div class="grid grid-3" style="grid-template-columns: 3fr 1fr; gap: 24px;">
        <div style="display: flex; flex-direction: column; gap: 24px;">
            
            <!-- Hero Section -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Hero Section</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Badge Text</label>
                        <input type="text" name="home_hero_badge" class="form-control" value="<?= getVal('home_hero_badge', 'Digital Marketing & Web Development Agency') ?>">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Title Line 1</label>
                            <input type="text" name="home_hero_title_1" class="form-control" value="<?= getVal('home_hero_title_1', 'Transform Your') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Title Line 2</label>
                            <input type="text" name="home_hero_title_2" class="form-control" value="<?= getVal('home_hero_title_2', 'Digital Presence') ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="home_hero_description" class="form-control" rows="3"><?= getVal('home_hero_description', 'Design visuals that speak louder than words — turning ideas into creative designs that connect, inspire, and engage your audience.') ?></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Primary Button Text</label>
                            <input type="text" name="home_hero_btn_primary_text" class="form-control" value="<?= getVal('home_hero_btn_primary_text', 'Get Started') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Primary Button Link</label>
                            <input type="text" name="home_hero_btn_primary_link" class="form-control" value="<?= getVal('home_hero_btn_primary_link', '/contact') ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Secondary Button Text</label>
                            <input type="text" name="home_hero_btn_secondary_text" class="form-control" value="<?= getVal('home_hero_btn_secondary_text', 'View Our Work') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Secondary Button Link</label>
                            <input type="text" name="home_hero_btn_secondary_link" class="form-control" value="<?= getVal('home_hero_btn_secondary_link', '/portfolio') ?>">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Services Section -->
            <div class="card">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 class="card-title">Services Section</h3>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="home_show_services" name="home_show_services" <?= isChecked('home_show_services') ?>>
                        <label class="form-check-label" for="home_show_services">Show Section</label>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Section Label (Small Top Text)</label>
                        <input type="text" name="home_services_label" class="form-control" value="<?= getVal('home_services_label', 'What We Do') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Section Title</label>
                        <input type="text" name="home_services_title" class="form-control" value="<?= getVal('home_services_title', 'Our Services') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="home_services_desc" class="form-control" rows="2"><?= getVal('home_services_desc', 'We offer a comprehensive suite of digital solutions tailored to help your business grow and succeed online.') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Why Choose Us Section -->
            <div class="card">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 class="card-title">Why Choose Us Section</h3>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="home_show_wcu" name="home_show_wcu" <?= isChecked('home_show_wcu') ?>>
                        <label class="form-check-label" for="home_show_wcu">Show Section</label>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Section Label</label>
                        <input type="text" name="home_wcu_label" class="form-control" value="<?= getVal('home_wcu_label', 'Why Choose Evolvcode') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Section Title</label>
                        <input type="text" name="home_wcu_title" class="form-control" value="<?= getVal('home_wcu_title', 'Your Growth, Our Mission') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="home_wcu_desc" class="form-control" rows="2"><?= getVal('home_wcu_desc', 'We combine creativity with technical expertise to deliver digital solutions that drive real business results.') ?></textarea>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">Note: The 3 checklist items are currently static in the code. Manage them in source code if needed.</p>
                </div>
            </div>

            <!-- Portfolio Section -->
            <div class="card">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 class="card-title">Portfolio Section</h3>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="home_show_projects" name="home_show_projects" <?= isChecked('home_show_projects') ?>>
                        <label class="form-check-label" for="home_show_projects">Show Section</label>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Section Label</label>
                        <input type="text" name="home_projects_label" class="form-control" value="<?= getVal('home_projects_label', 'Our Work') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Section Title</label>
                        <input type="text" name="home_projects_title" class="form-control" value="<?= getVal('home_projects_title', 'Featured Projects') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="home_projects_desc" class="form-control" rows="2"><?= getVal('home_projects_desc', 'Take a look at some of our recent work and see how we help businesses succeed online.') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Process Section -->
            <div class="card">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 class="card-title">Process Section</h3>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="home_show_process" name="home_show_process" <?= isChecked('home_show_process') ?>>
                        <label class="form-check-label" for="home_show_process">Show Section</label>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Section Label</label>
                        <input type="text" name="home_process_label" class="form-control" value="<?= getVal('home_process_label', 'Our Process') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Section Title</label>
                        <input type="text" name="home_process_title" class="form-control" value="<?= getVal('home_process_title', 'How We Do Digital Marketing at Evolvcode') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="home_process_desc" class="form-control" rows="2"><?= getVal('home_process_desc', 'We keep things simple, clear, and results-focused—taking your brand from "just online" to "growing online."') ?></textarea>
                    </div>
                </div>
            </div>
            
            <!-- Trust Section -->
             <div class="card">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 class="card-title">Trust/Transparency Section</h3>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="home_show_trust" name="home_show_trust" <?= isChecked('home_show_trust') ?>>
                        <label class="form-check-label" for="home_show_trust">Show Section</label>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-sm">This section contains the 3 icons (Transparency, Experienced Team, Result Guarantee).</p>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="card">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 class="card-title">CTA Section (Bottom)</h3>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="home_show_cta" name="home_show_cta" <?= isChecked('home_show_cta') ?>>
                        <label class="form-check-label" for="home_show_cta">Show Section</label>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Title</label>
                        <input type="text" name="home_cta_title" class="form-control" value="<?= getVal('home_cta_title', 'Ready to Transform Your Business?') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="home_cta_desc" class="form-control" rows="2"><?= getVal('home_cta_desc', "Let's discuss how we can help you achieve your digital goals. Get a free consultation today!") ?></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Button Text</label>
                            <input type="text" name="home_cta_btn_text" class="form-control" value="<?= getVal('home_cta_btn_text', 'Contact Us') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Button Link</label>
                            <input type="text" name="home_cta_btn_link" class="form-control" value="<?= getVal('home_cta_btn_link', '/contact') ?>">
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        
        <!-- Sidebar Actions -->
        <div>
            <div class="card sticky-top" style="top: 24px;">
                <div class="card-header">
                    <h3 class="card-title">Actions</h3>
                </div>
                <div class="card-body">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="<?= e(SITE_URL) ?>" target="_blank" class="btn btn-secondary mt-3" style="width: 100%;">
                        <i class="fas fa-external-link-alt"></i> View Homepage
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
.form-check.form-switch {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
}
.form-check-input {
    margin: 0;
    width: 40px;
    height: 20px;
    appearance: none;
    background: #e5e7eb;
    border-radius: 20px;
    position: relative;
    cursor: pointer;
    transition: background .2s;
}
.form-check-input::after {
    content: '';
    position: absolute;
    top: 2px;
    left: 2px;
    width: 16px;
    height: 16px;
    background: white;
    border-radius: 50%;
    transition: transform .2s;
    box-shadow: 0 1px 2px rgba(0,0,0,0.2);
}
.form-check-input:checked {
    background: var(--color-primary);
}
.form-check-input:checked::after {
    transform: translateX(20px);
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
