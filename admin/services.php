<?php
/**
 * Evolvcode CMS - Admin Services Management
 */

$pageTitle = 'Services';
require_once __DIR__ . '/includes/header.php';
Auth::requirePermission('services');

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
            'short_description' => Security::clean($_POST['short_description'] ?? ''),
            'content' => $_POST['content'] ?? '',
            'icon' => Security::clean($_POST['icon'] ?? ''),
            'features' => Security::clean($_POST['features'] ?? ''),
            'status' => Security::clean($_POST['status'] ?? 'published'),
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
            'meta_title' => Security::clean($_POST['meta_title'] ?? ''),
            'meta_description' => Security::clean($_POST['meta_description'] ?? ''),
            'meta_keywords' => Security::clean($_POST['meta_keywords'] ?? ''),
        ];
        
        // Generate slug if empty
        if (empty($data['slug'])) {
            $data['slug'] = createSlug($data['title']);
        }
        
        // Handle image upload or selection
        $imagePath = '';
        if (!empty($_FILES['image_upload']['name'])) {
            $uploader = new FileUpload();
            $imagePath = $uploader->uploadImage($_FILES['image_upload']);
            if (!$imagePath) {
                $error = 'Image upload failed: ' . implode(' ', $uploader->getErrors());
            }
        } elseif (!empty($_POST['image'])) {
            $imagePath = Security::clean($_POST['image']);
        }

        if ($imagePath) {
            $data['image'] = $imagePath;
        }
        
        try {
            if ($id > 0) {
                // Update
                $db->update('services', $data, 'id = ?', [$id]);
                $message = 'Service updated successfully.';
            } else {
                // Insert
                $db->insert('services', $data);
                $message = 'Service created successfully.';
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
        $db->delete('services', 'id = ?', [$id]);
        $message = 'Service deleted successfully.';
    }
    $action = 'list';
}

// Get services list
$services = $db->fetchAll("SELECT * FROM services ORDER BY sort_order ASC, created_at DESC");

// Get single service for edit
$service = null;
if ($action === 'edit' && $id > 0) {
    $service = $db->fetchOne("SELECT * FROM services WHERE id = ?", [$id]);
    if (!$service) {
        $action = 'list';
        $error = 'Service not found.';
    }
}
?>

<?php if ($action === 'list'): ?>
<!-- List View -->
<div class="page-header">
    <div>
        <h1 class="page-title">Services</h1>
        <p class="page-subtitle">Manage your service offerings</p>
    </div>
    <a href="?action=new" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Service
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
        <?php if (!empty($services)): ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Icon</th>
                        <th>Status</th>
                        <th>Order</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $item): ?>
                    <tr>
                        <td>
                            <strong><?= e($item['title']) ?></strong>
                            <div style="font-size: 12px; color: var(--color-gray-500);">
                                /service/<?= e($item['slug']) ?>
                            </div>
                        </td>
                        <td>
                            <?php if ($item['icon']): ?>
                            <i class="fas <?= e($item['icon']) ?>"></i>
                            <?php else: ?>
                            <span style="color: var(--color-gray-400);">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="status-badge status-<?= $item['status'] ?>">
                                <?= ucfirst($item['status']) ?>
                            </span>
                        </td>
                        <td><?= $item['sort_order'] ?></td>
                        <td>
                            <div class="table-actions">
                                <a href="<?= e(SITE_URL) ?>/service/<?= e($item['slug']) ?>" 
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
            <i class="fas fa-concierge-bell"></i>
            <h3>No Services Yet</h3>
            <p>Add your first service to get started.</p>
            <a href="?action=new" class="btn btn-primary" style="margin-top: 16px;">
                <i class="fas fa-plus"></i> Add Service
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php else: ?>
<!-- Add/Edit View -->
<div class="page-header">
    <div>
        <h1 class="page-title"><?= $action === 'edit' ? 'Edit Service' : 'New Service' ?></h1>
        <p class="page-subtitle">
            <a href="?action=list"><i class="fas fa-arrow-left"></i> Back to Services</a>
        </p>
    </div>
</div>

<?php if ($error): ?>
<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= e($error) ?></div>
<?php endif; ?>

<?php if ($message): ?>
<div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= e($message) ?></div>
<?php endif; ?>

<form method="POST" action="" enctype="multipart/form-data" data-validate>
    <?= Security::csrfField() ?>
    
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <!-- Main Content -->
        <div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Service Details</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="title" class="form-label">Title <span class="required">*</span></label>
                        <input type="text" name="title" id="title" class="form-control" 
                               value="<?= e($service['title'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" name="slug" id="slug" class="form-control" 
                               value="<?= e($service['slug'] ?? '') ?>" 
                               placeholder="Auto-generated from title">
                    </div>
                    
                    <div class="form-group">
                        <label for="short_description" class="form-label">Short Description</label>
                        <textarea name="short_description" id="short_description" class="form-control" 
                                  rows="3"><?= e($service['short_description'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="content" class="form-label">Content</label>
                        <textarea name="content" id="content" class="form-control tinymce-editor" 
                                  rows="10"><?= e($service['content'] ?? '') ?></textarea>
                        <p class="form-text">HTML is allowed.</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="features" class="form-label">Features</label>
                        <textarea name="features" id="features" class="form-control" 
                                  rows="5" placeholder="Enter one feature per line"><?= e($service['features'] ?? '') ?></textarea>
                        <p class="form-text">Enter one feature per line.</p>
                    </div>
                </div>
            </div>
            
            <!-- SEO -->
            <div class="card" style="margin-top: 24px;">
                <div class="card-header">
                    <h3 class="card-title">SEO Settings</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="meta_title" class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" id="meta_title" class="form-control" 
                               value="<?= e($service['meta_title'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="meta_description" class="form-label">Meta Description</label>
                        <textarea name="meta_description" id="meta_description" class="form-control" 
                                  rows="3"><?= e($service['meta_description'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="meta_keywords" class="form-label">Meta Keywords</label>
                        <input type="text" name="meta_keywords" id="meta_keywords" class="form-control" 
                               value="<?= e($service['meta_keywords'] ?? '') ?>" 
                               placeholder="keyword1, keyword2, keyword3">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Publish</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="published" <?= ($service['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                            <option value="draft" <?= ($service['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="sort_order" class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" id="sort_order" class="form-control" 
                               value="<?= e($service['sort_order'] ?? 0) ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-save"></i> <?= $action === 'edit' ? 'Update' : 'Save' ?> Service
                    </button>
                </div>
            </div>
            
            <div class="card" style="margin-top: 24px;">
                <div class="card-header">
                    <h3 class="card-title">Display</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="icon" class="form-label">Icon Class</label>
                        <input type="text" name="icon" id="icon" class="form-control" 
                               value="<?= e($service['icon'] ?? '') ?>" 
                               placeholder="e.g., fa-laptop">
                        <p class="form-text">Font Awesome icon class (without 'fas')</p>
                    </div>
                    
                    <div class="form-group">
                <div class="card-body">
                    <!-- Standard File Input -->
                    <div class="form-group">
                        <label>Upload New</label>
                        <input type="file" name="image_upload" class="form-control" accept="image/*">
                    </div>
                    
                    <div style="text-align: center; margin: 10px 0; color: #64748b;">- OR -</div>
                    
                    <!-- Media Picker Trigger -->
                    <div class="form-group">
                        <button type="button" class="btn btn-secondary" style="width: 100%;" 
                                data-media-picker="true" 
                                data-target-input="image" 
                                data-target-preview="image-preview">
                            <i class="fas fa-images"></i> Select from Library
                        </button>
                    </div>

                    <!-- Hidden Input for selected path -->
                    <input type="hidden" name="image" id="image" 
                           value="<?= e($service['image'] ?? '') ?>">

                    <!-- Preview -->
                    <?php if (!empty($service['image'])): ?>
                    <img src="<?= e(SITE_URL . $service['image']) ?>" id="image-preview" 
                         style="margin-top: 12px; max-width: 100%; border-radius: 8px; display: block;">
                    <?php else: ?>
                    <img id="image-preview" style="margin-top: 12px; max-width: 100%; border-radius: 8px; display: none;">
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</form>

<script src="<?= ADMIN_URL ?>/assets/js/media-picker.js"></script>

<style>
@media (max-width: 1024px) {
    div[style*="grid-template-columns: 2fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}
</style>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
