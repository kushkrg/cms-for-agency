<?php
/**
 * Evolvcode CMS - Admin Blog Posts Management
 */

$pageTitle = 'Blog Posts';
require_once __DIR__ . '/includes/header.php';
Auth::requirePermission('posts');

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
            'excerpt' => Security::clean($_POST['excerpt'] ?? ''),
            'content' => $_POST['content'] ?? '',
            'category_id' => !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null,
            'author' => Security::clean($_POST['author'] ?? ''),
            'status' => Security::clean($_POST['status'] ?? 'draft'),
            'meta_title' => Security::clean($_POST['meta_title'] ?? ''),
            'meta_description' => Security::clean($_POST['meta_description'] ?? ''),
            'meta_keywords' => Security::clean($_POST['meta_keywords'] ?? ''),
        ];
        
        // Generate slug if empty
        if (empty($data['slug'])) {
            $data['slug'] = createSlug($data['title']);
        }
        
        // Set publish date
        if ($data['status'] === 'published') {
            $data['published_at'] = date('Y-m-d H:i:s');
        }
        
        // Handle image upload or selection
        $imagePath = '';
        if (!empty($_FILES['featured_image_upload']['name'])) {
            $uploader = new FileUpload();
            $imagePath = $uploader->uploadImage($_FILES['featured_image_upload']);
            if (!$imagePath) {
                $error = 'Image upload failed: ' . implode(' ', $uploader->getErrors());
            }
        } elseif (!empty($_POST['featured_image'])) {
            $imagePath = Security::clean($_POST['featured_image']);
        }

        if ($imagePath) {
            $data['featured_image'] = $imagePath;
        }
        
        try {
            if ($id > 0) {
                $db->update('posts', $data, 'id = ?', [$id]);
                $message = 'Post updated successfully.';
            } else {
                $db->insert('posts', $data);
                $message = 'Post created successfully.';
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
        $db->delete('posts', 'id = ?', [$id]);
        $message = 'Post deleted successfully.';
    }
    $action = 'list';
}

// Pagination
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 15;
$offset = ($page - 1) * $limit;
$total = $db->count('posts');

// Get posts
$posts = $db->fetchAll(
    "SELECT p.*, c.name as category_name 
     FROM posts p 
     LEFT JOIN blog_categories c ON p.category_id = c.id 
     ORDER BY p.created_at DESC 
     LIMIT ? OFFSET ?",
    [$limit, $offset]
);

// Get categories
$categories = $db->fetchAll("SELECT * FROM blog_categories ORDER BY name ASC");

// Get single post for edit
$post = null;
if ($action === 'edit' && $id > 0) {
    $post = $db->fetchOne("SELECT * FROM posts WHERE id = ?", [$id]);
    if (!$post) {
        $action = 'list';
        $error = 'Post not found.';
    }
}
?>

<?php if ($action === 'list'): ?>
<!-- List View -->
<div class="page-header">
    <div>
        <h1 class="page-title">Blog Posts</h1>
        <p class="page-subtitle"><?= $total ?> total posts</p>
    </div>
    <a href="?action=new" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Post
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
        <?php if (!empty($posts)): ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $item): ?>
                    <tr>
                        <td>
                            <strong><?= e($item['title']) ?></strong>
                            <div style="font-size: 12px; color: var(--color-gray-500);">
                                /post/<?= e($item['slug']) ?>
                            </div>
                        </td>
                        <td><?= e($item['category_name'] ?? '—') ?></td>
                        <td><?= e($item['author'] ?: '—') ?></td>
                        <td>
                            <span class="status-badge status-<?= $item['status'] ?>">
                                <?= ucfirst($item['status']) ?>
                            </span>
                        </td>
                        <td style="font-size: 13px; color: var(--color-gray-600);">
                            <?= formatDate($item['created_at'], 'M j, Y') ?>
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="<?= e(SITE_URL) ?>/post/<?= e($item['slug']) ?>" 
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
        
        <?php if ($total > $limit): ?>
        <div style="padding: 20px;">
            <?= paginate($total, $page, $limit, ADMIN_URL . '/posts.php?') ?>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-newspaper"></i>
            <h3>No Posts Yet</h3>
            <p>Create your first blog post to get started.</p>
            <a href="?action=new" class="btn btn-primary" style="margin-top: 16px;">
                <i class="fas fa-plus"></i> Add Post
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php else: ?>
<!-- Add/Edit View -->
<div class="page-header">
    <div>
        <h1 class="page-title"><?= $action === 'edit' ? 'Edit Post' : 'New Post' ?></h1>
        <p class="page-subtitle">
            <a href="?action=list"><i class="fas fa-arrow-left"></i> Back to Posts</a>
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
                    <h3 class="card-title">Post Details</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="title" class="form-label">Title <span class="required">*</span></label>
                        <input type="text" name="title" id="title" class="form-control" 
                               value="<?= e($post['title'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" name="slug" id="slug" class="form-control" 
                               value="<?= e($post['slug'] ?? '') ?>" 
                               placeholder="Auto-generated from title">
                    </div>
                    
                    <div class="form-group">
                        <label for="excerpt" class="form-label">Excerpt</label>
                        <textarea name="excerpt" id="excerpt" class="form-control" 
                                  rows="3"><?= e($post['excerpt'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="content" class="form-label">Content</label>
                        <textarea name="content" id="content" class="form-control tinymce-editor" 
                                  rows="15"><?= e($post['content'] ?? '') ?></textarea>
                        <p class="form-text">HTML is allowed.</p>
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
                               value="<?= e($post['meta_title'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="meta_description" class="form-label">Meta Description</label>
                        <textarea name="meta_description" id="meta_description" class="form-control" 
                                  rows="3"><?= e($post['meta_description'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="meta_keywords" class="form-label">Meta Keywords</label>
                        <input type="text" name="meta_keywords" id="meta_keywords" class="form-control" 
                               value="<?= e($post['meta_keywords'] ?? '') ?>"
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
                            <option value="draft" <?= ($post['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
                            <option value="published" <?= ($post['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="category_id" class="form-label">Category</label>
                        <select name="category_id" id="category_id" class="form-control">
                            <option value="">— No Category —</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($post['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                <?= e($cat['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="author" class="form-label">Author</label>
                        <input type="text" name="author" id="author" class="form-control" 
                               value="<?= e($post['author'] ?? 'Evolvcode Team') ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-save"></i> <?= $action === 'edit' ? 'Update' : 'Save' ?> Post
                    </button>
                </div>
            </div>
            
            <div class="card" style="margin-top: 24px;">
                <div class="card-header">
                    <h3 class="card-title">Featured Image</h3>
                </div>
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
                           value="<?= e($post['featured_image'] ?? '') ?>">

                    <!-- Preview -->
                    <?php if (!empty($post['featured_image'])): ?>
                    <img src="<?= e(SITE_URL . $post['featured_image']) ?>" id="image-preview" 
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
