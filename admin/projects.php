<?php
/**
 * Evolvcode CMS - Admin Projects Management
 */

$pageTitle = 'Portfolio';
require_once __DIR__ . '/includes/header.php';
Auth::requirePermission('projects');

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
            'category_id' => !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null,
            'client_name' => Security::clean($_POST['client_name'] ?? ''),
            'project_url' => Security::clean($_POST['project_url'] ?? ''),
            'tech_stack' => Security::clean($_POST['tech_stack'] ?? ''),
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'status' => Security::clean($_POST['status'] ?? 'published'),
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
        if (!empty($_FILES['featured_image_upload']['name'])) {
            $uploader = new FileUpload();
            $imagePath = $uploader->uploadImage($_FILES['featured_image_upload']);
            if (!$imagePath) {
                $error = implode(' ', $uploader->getErrors());
            }
        } elseif (!empty($_POST['featured_image'])) {
            $imagePath = Security::clean($_POST['featured_image']);
        }

        if ($imagePath) {
            $data['featured_image'] = $imagePath;
        }
        
        try {
            if ($id > 0) {
                $db->update('projects', $data, 'id = ?', [$id]);
                $message = 'Project updated successfully.';
            } else {
                $db->insert('projects', $data);
                $message = 'Project created successfully.';
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
        $db->delete('projects', 'id = ?', [$id]);
        $message = 'Project deleted successfully.';
    }
    $action = 'list';
}

// Get projects
$projects = $db->fetchAll(
    "SELECT p.*, c.name as category_name 
     FROM projects p 
     LEFT JOIN portfolio_categories c ON p.category_id = c.id 
     ORDER BY p.created_at DESC"
);

// Get categories
$categories = $db->fetchAll("SELECT * FROM portfolio_categories ORDER BY name ASC");

// Get single project for edit
$project = null;
if ($action === 'edit' && $id > 0) {
    $project = $db->fetchOne("SELECT * FROM projects WHERE id = ?", [$id]);
    if (!$project) {
        $action = 'list';
        $error = 'Project not found.';
    }
}
?>

<?php if ($action === 'list'): ?>
<div class="page-header">
    <div>
        <h1 class="page-title">Portfolio</h1>
        <p class="page-subtitle">Manage your project showcase</p>
    </div>
    <a href="?action=new" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Project
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
        <?php if (!empty($projects)): ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">Image</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Client</th>
                        <th>Status</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $item): ?>
                    <tr>
                        <td>
                            <?php if ($item['featured_image']): ?>
                            <img src="<?= e(SITE_URL . $item['featured_image']) ?>" 
                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;">
                            <?php else: ?>
                            <div style="width: 50px; height: 50px; background: var(--color-gray-200); border-radius: 6px;"></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= e($item['title']) ?></strong>
                            <?php if ($item['is_featured']): ?>
                            <span style="color: var(--color-warning); margin-left: 4px;" title="Featured">★</span>
                            <?php endif; ?>
                            <div style="font-size: 12px; color: var(--color-gray-500);">
                                /project/<?= e($item['slug']) ?>
                            </div>
                        </td>
                        <td><?= e($item['category_name'] ?? '—') ?></td>
                        <td><?= e($item['client_name'] ?: '—') ?></td>
                        <td>
                            <span class="status-badge status-<?= $item['status'] ?>">
                                <?= ucfirst($item['status']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="<?= e(SITE_URL) ?>/project/<?= e($item['slug']) ?>" 
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
            <i class="fas fa-briefcase"></i>
            <h3>No Projects Yet</h3>
            <p>Showcase your work by adding projects.</p>
            <a href="?action=new" class="btn btn-primary" style="margin-top: 16px;">
                <i class="fas fa-plus"></i> Add Project
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php else: ?>
<div class="page-header">
    <div>
        <h1 class="page-title"><?= $action === 'edit' ? 'Edit Project' : 'New Project' ?></h1>
        <p class="page-subtitle">
            <a href="?action=list"><i class="fas fa-arrow-left"></i> Back to Projects</a>
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
        <div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Project Details</h3>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="title" class="form-label">Title <span class="required">*</span></label>
                            <input type="text" name="title" id="title" class="form-control" 
                                   value="<?= e($project['title'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" name="slug" id="slug" class="form-control" 
                                   value="<?= e($project['slug'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="client_name" class="form-label">Client Name</label>
                            <input type="text" name="client_name" id="client_name" class="form-control" 
                                   value="<?= e($project['client_name'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="project_url" class="form-label">Project URL</label>
                            <input type="url" name="project_url" id="project_url" class="form-control" 
                                   value="<?= e($project['project_url'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="short_description" class="form-label">Short Description</label>
                        <textarea name="short_description" id="short_description" class="form-control" 
                                  rows="3"><?= e($project['short_description'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="content" class="form-label">Content</label>
                        <textarea name="content" id="content" class="form-control tinymce-editor" 
                                  rows="10"><?= e($project['content'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="tech_stack" class="form-label">Technologies Used</label>
                        <input type="text" name="tech_stack" id="tech_stack" class="form-control" 
                               value="<?= e($project['tech_stack'] ?? '') ?>" 
                               placeholder="e.g., PHP, WordPress, JavaScript">
                        <p class="form-text">Comma-separated list of technologies</p>
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
                               value="<?= e($project['meta_title'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="meta_description" class="form-label">Meta Description</label>
                        <textarea name="meta_description" id="meta_description" class="form-control" 
                                  rows="3"><?= e($project['meta_description'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="meta_keywords" class="form-label">Meta Keywords</label>
                        <input type="text" name="meta_keywords" id="meta_keywords" class="form-control" 
                               value="<?= e($project['meta_keywords'] ?? '') ?>"
                               placeholder="keyword1, keyword2, keyword3">
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
                            <option value="published" <?= ($project['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                            <option value="draft" <?= ($project['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="category_id" class="form-label">Category</label>
                        <select name="category_id" id="category_id" class="form-control">
                            <option value="">— No Category —</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($project['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                <?= e($cat['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="is_featured" value="1" 
                                   <?= ($project['is_featured'] ?? 0) ? 'checked' : '' ?>>
                            Featured Project
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-save"></i> <?= $action === 'edit' ? 'Update' : 'Save' ?> Project
                    </button>
                </div>
            </div>
            
            <div class="card" style="margin-top: 24px;">
                <div class="card-header">
                    <h3 class="card-title">Featured Image</h3>
                </div>
                <div class="card-body">
                <div class="card-body">
                    <!-- Standard File Input -->
                    <div class="form-group">
                        <label>Upload New</label>
                        <input type="file" name="featured_image_upload" class="form-control" accept="image/*">
                    </div>
                    
                    <div style="text-align: center; margin: 10px 0; color: #64748b;">- OR -</div>
                    
                    <!-- Media Picker Trigger -->
                    <div class="form-group">
                        <button type="button" class="btn btn-secondary" style="width: 100%;" 
                                data-media-picker="true" 
                                data-target-input="featured_image" 
                                data-target-preview="image-preview">
                            <i class="fas fa-images"></i> Select from Library
                        </button>
                    </div>

                    <!-- Hidden Input for selected path -->
                    <input type="hidden" name="featured_image" id="featured_image" 
                           value="<?= e($project['featured_image'] ?? '') ?>">

                    <!-- Preview -->
                    <?php if (!empty($project['featured_image'])): ?>
                    <img src="<?= e(SITE_URL . $project['featured_image']) ?>" id="image-preview" 
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
