<?php
/**
 * Evolvcode CMS - Admin Pages Management
 */

$pageTitle = 'Pages';
require_once __DIR__ . '/includes/header.php';
Auth::requirePermission('pages');

$db = Database::getInstance();
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$message = '';
$error = '';

// Handle form submission
if (Security::isPost()) {
    $token = $_POST[CSRF_TOKEN_NAME] ?? '';
    if (!Security::validateCSRFToken($token)) {
        $error = 'Invalid form submission.';
    } else {
        $data = [
            'title' => Security::clean($_POST['title'] ?? ''),
            'slug' => Security::clean($_POST['slug'] ?? ''),
            'content' => $_POST['content'] ?? '',
            'status' => Security::clean($_POST['status'] ?? 'published'),
            'meta_title' => Security::clean($_POST['meta_title'] ?? ''),
            'meta_description' => Security::clean($_POST['meta_description'] ?? ''),
            'meta_keywords' => Security::clean($_POST['meta_keywords'] ?? ''),
            'custom_script' => $_POST['custom_script'] ?? '',
        ];
        
        if (empty($data['slug'])) {
            $data['slug'] = createSlug($data['title']);
        }
        
        try {
            if ($id > 0) {
                $db->update('pages', $data, 'id = ?', [$id]);
                $message = 'Page updated successfully.';
            } else {
                $db->insert('pages', $data);
                $message = 'Page created successfully.';
                $action = 'list';
            }
        } catch (Exception $e) {
            $error = 'An error occurred. Please try again.';
        }
    }
}

// Handle delete
if ($action === 'delete' && $id > 0) {
    $token = $_GET['token'] ?? '';
    if (Security::validateCSRFToken($token)) {
        $db->delete('pages', 'id = ?', [$id]);
        $message = 'Page deleted successfully.';
    }
    $action = 'list';
}

// Get pages
$pages = $db->fetchAll("SELECT * FROM pages ORDER BY title ASC");

// Get single page for edit
$page = null;
if ($action === 'edit' && $id > 0) {
    $page = $db->fetchOne("SELECT * FROM pages WHERE id = ?", [$id]);
    if (!$page) {
        $action = 'list';
        $error = 'Page not found.';
    }
}
?>

<?php if ($action === 'list'): ?>
<div class="page-header">
    <div>
        <h1 class="page-title">Pages</h1>
        <p class="page-subtitle">Manage your website pages</p>
    </div>
    <a href="?action=new" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Page
    </a>
</div>

<?php if ($message): ?>
<div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= e($message) ?></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= e($error) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <?php if (!empty($pages)): ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Modified</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pages as $item): ?>
                    <tr>
                        <td>
                            <strong><?= e($item['title']) ?></strong>
                            <div style="font-size: 12px; color: var(--color-gray-500);">
                                /<?= e($item['slug']) ?>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge status-<?= $item['status'] ?>">
                                <?= ucfirst($item['status']) ?>
                            </span>
                        </td>
                        <td style="font-size: 13px; color: var(--color-gray-600);">
                            <?= formatDate($item['updated_at'], 'M j, Y') ?>
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="<?= e(SITE_URL) ?>/<?= e($item['slug']) ?>" 
                                   target="_blank" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="?action=edit&id=<?= $item['id'] ?>" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="?action=delete&id=<?= $item['id'] ?>&token=<?= e(Security::generateCSRFToken()) ?>" 
                                   class="delete" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-file-alt"></i>
            <h3>No Pages Yet</h3>
            <p>Create custom pages for your website.</p>
            <a href="?action=new" class="btn btn-primary" style="margin-top: 16px;">
                <i class="fas fa-plus"></i> Add Page
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php else: ?>
<div class="page-header">
    <div>
        <h1 class="page-title"><?= $action === 'edit' ? 'Edit Page' : 'New Page' ?></h1>
        <p class="page-subtitle">
            <a href="?action=list"><i class="fas fa-arrow-left"></i> Back to Pages</a>
        </p>
    </div>
    <?php if ($action === 'edit' && !empty($page['slug'])): ?>
    <a href="<?= e(SITE_URL . '/' . $page['slug']) ?>" target="_blank" class="btn btn-primary" style="background: transparent; color: var(--color-primary); border: 1px solid var(--color-primary);">
        <i class="fas fa-external-link-alt"></i> View Page
    </a>
    <?php endif; ?>
</div>

<?php if ($error): ?>
<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= e($error) ?></div>
<?php endif; ?>

<?php if ($message): ?>
<div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= e($message) ?></div>
<?php endif; ?>

<form method="POST" action="" data-validate>
    <?= Security::csrfField() ?>
    
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Page Details</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="title" class="form-label">Title <span class="required">*</span></label>
                        <input type="text" name="title" id="title" class="form-control" 
                               value="<?= e($page['title'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" name="slug" id="slug" class="form-control" 
                               value="<?= e($page['slug'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="content" class="form-label">Content</label>
                        <textarea name="content" id="content" class="form-control tinymce-editor" 
                                  rows="15"><?= e($page['content'] ?? '') ?></textarea>
                        <p class="form-text">HTML is allowed.</p>
                    </div>
                </div>
            </div>
            
            <div class="card" style="margin-top: 24px;">
                <div class="card-header">
                    <h3 class="card-title">SEO Settings</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="meta_title" class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" id="meta_title" class="form-control" 
                               value="<?= e($page['meta_title'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="meta_description" class="form-label">Meta Description</label>
                        <textarea name="meta_description" id="meta_description" class="form-control" 
                                  rows="3"><?= e($page['meta_description'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="meta_keywords" class="form-label">Meta Keywords</label>
                        <input type="text" name="meta_keywords" id="meta_keywords" class="form-control" 
                               value="<?= e($page['meta_keywords'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>
        
        <div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Publish</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="published" <?= ($page['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                            <option value="draft" <?= ($page['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-save"></i> <?= $action === 'edit' ? 'Update' : 'Save' ?> Page
                    </button>
                </div>
            </div>

            <div class="card" style="margin-top: 24px;">
                <div class="card-header">
                    <h3 class="card-title">Custom Scripts</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="custom_script" class="form-label">Body Script (Before &lt;/body&gt;)</label>
                        <textarea name="custom_script" id="custom_script" class="form-control" 
                                  rows="6" style="font-family: monospace; font-size: 12px;"><?= e($page['custom_script'] ?? '') ?></textarea>
                        <p class="form-text">Add tracking codes or custom JS here.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
@media (max-width: 1024px) {
    div[style*="grid-template-columns: 2fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}
</style>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
