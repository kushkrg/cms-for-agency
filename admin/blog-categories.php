<?php
/**
 * Evolvcode CMS - Blog Categories Management
 */

$pageTitle = 'Blog Categories';
require_once __DIR__ . '/includes/header.php';
Auth::requirePermission('posts');

$db = Database::getInstance();
$message = '';
$error = '';
$editCat = null;

// Handle form submissions
if (Security::isPost()) {
    $token = $_POST[CSRF_TOKEN_NAME] ?? '';

    // ADD CATEGORY
    if (isset($_POST['add_category'])) {
        if (!Security::validateCSRFToken($token)) {
            $error = 'Invalid CSRF token.';
        } else {
            $name = Security::clean($_POST['name'] ?? '');
            $slug = Security::clean($_POST['slug'] ?? '');
            $description = Security::clean($_POST['description'] ?? '');

            if (empty($slug)) {
                $slug = createSlug($name);
            }

            if (!$name) {
                $error = 'Category name is required.';
            } elseif ($db->exists('blog_categories', 'slug = ?', [$slug])) {
                $error = 'A category with this slug already exists.';
            } else {
                try {
                    $data = ['name' => $name, 'slug' => $slug];
                    // Add description if column exists
                    try {
                        $db->fetchOne("SELECT description FROM blog_categories LIMIT 0");
                        $data['description'] = $description;
                    } catch (Exception $e) {}
                    
                    $db->insert('blog_categories', $data);
                    $message = "Category <strong>{$name}</strong> created successfully.";
                } catch (Exception $e) {
                    $error = 'Failed to create category: ' . $e->getMessage();
                }
            }
        }
    }

    // EDIT CATEGORY
    elseif (isset($_POST['edit_category'])) {
        if (!Security::validateCSRFToken($token)) {
            $error = 'Invalid CSRF token.';
        } else {
            $catId = (int)($_POST['cat_id'] ?? 0);
            $name = Security::clean($_POST['name'] ?? '');
            $slug = Security::clean($_POST['slug'] ?? '');
            $description = Security::clean($_POST['description'] ?? '');

            if (empty($slug)) {
                $slug = createSlug($name);
            }

            if (!$name) {
                $error = 'Category name is required.';
            } else {
                $dup = $db->fetchOne("SELECT id FROM blog_categories WHERE slug = ? AND id != ?", [$slug, $catId]);
                if ($dup) {
                    $error = 'A category with this slug already exists.';
                } else {
                    $data = ['name' => $name, 'slug' => $slug];
                    try {
                        $db->fetchOne("SELECT description FROM blog_categories LIMIT 0");
                        $data['description'] = $description;
                    } catch (Exception $e) {}
                    
                    $db->update('blog_categories', $data, 'id = ?', [$catId]);
                    $message = "Category <strong>{$name}</strong> updated successfully.";
                }
            }
        }
    }

    // DELETE CATEGORY
    elseif (isset($_POST['delete_category'])) {
        if (!Security::validateCSRFToken($token)) {
            $error = 'Invalid CSRF token.';
        } else {
            $catId = (int)($_POST['cat_id'] ?? 0);
            
            // Count posts in this category
            $postCount = $db->count('posts', 'category_id = ?', [$catId]);
            
            $db->delete('blog_categories', 'id = ?', [$catId]);
            $msg = 'Category deleted successfully.';
            if ($postCount > 0) {
                $msg .= " {$postCount} post(s) were uncategorized.";
            }
            $message = $msg;
        }
    }
}

// Check if editing
if (isset($_GET['edit'])) {
    $editCat = $db->fetchOne("SELECT * FROM blog_categories WHERE id = ?", [(int)$_GET['edit']]);
}

// Fetch all categories with post count
$categories = $db->fetchAll(
    "SELECT c.*, COUNT(p.id) as post_count 
     FROM blog_categories c 
     LEFT JOIN posts p ON c.id = p.category_id 
     GROUP BY c.id 
     ORDER BY c.name ASC"
);

// Check if description column exists
$hasDescription = false;
try {
    $db->fetchOne("SELECT description FROM blog_categories LIMIT 0");
    $hasDescription = true;
} catch (Exception $e) {}
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Blog Categories</h1>
        <p class="page-subtitle"><?= count($categories) ?> total categories</p>
    </div>
    <a href="<?= e(ADMIN_URL) ?>/posts.php?action=new" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Post
    </a>
</div>

<?php if ($message): ?>
<div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $message ?></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= e($error) ?></div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
    <!-- Categories List -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Categories</h3>
        </div>
        <div class="card-body" style="padding: 0;">
            <?php if (!empty($categories)): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Slug</th>
                        <th>Posts</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td>
                            <strong><?= e($cat['name']) ?></strong>
                            <?php if ($hasDescription && !empty($cat['description'])): ?>
                            <div style="font-size: 0.8rem; color: var(--color-gray-500); margin-top: 2px;">
                                <?= e($cat['description']) ?>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <code style="font-size: 0.8rem; background: var(--color-gray-100); padding: 2px 8px; border-radius: 4px;">
                                <?= e($cat['slug']) ?>
                            </code>
                        </td>
                        <td>
                            <?php if ($cat['post_count'] > 0): ?>
                            <a href="<?= e(ADMIN_URL) ?>/posts.php" style="color: var(--color-primary); font-weight: 500;">
                                <?= $cat['post_count'] ?> post<?= $cat['post_count'] > 1 ? 's' : '' ?>
                            </a>
                            <?php else: ?>
                            <span style="color: var(--color-gray-400);">0 posts</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="?edit=<?= $cat['id'] ?>" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="" onsubmit="return confirm('Delete this category? Posts in it will become uncategorized.');" style="display: inline;">
                                    <?= Security::csrfField() ?>
                                    <input type="hidden" name="delete_category" value="1">
                                    <input type="hidden" name="cat_id" value="<?= $cat['id'] ?>">
                                    <button type="submit" class="delete" title="Delete" style="background: none; border: none; cursor: pointer; color: inherit; padding: 0;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-folder-open"></i>
                <h3>No Categories Yet</h3>
                <p>Create your first blog category to organize your posts.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add/Edit Category Form -->
    <div class="card" style="height: fit-content; position: sticky; top: 24px;">
        <div class="card-header">
            <h3 class="card-title"><?= $editCat ? 'Edit Category' : 'Add New Category' ?></h3>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <?= Security::csrfField() ?>
                <?php if ($editCat): ?>
                    <input type="hidden" name="edit_category" value="1">
                    <input type="hidden" name="cat_id" value="<?= $editCat['id'] ?>">
                <?php else: ?>
                    <input type="hidden" name="add_category" value="1">
                <?php endif; ?>

                <div class="form-group">
                    <label class="form-label">Name <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" 
                           value="<?= e($editCat['name'] ?? '') ?>" required
                           placeholder="e.g. Technology">
                </div>

                <div class="form-group">
                    <label class="form-label">Slug</label>
                    <input type="text" name="slug" class="form-control" 
                           value="<?= e($editCat['slug'] ?? '') ?>"
                           placeholder="Auto-generated from name">
                    <small class="form-text">URL-friendly identifier. Leave blank to auto-generate.</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"
                              placeholder="Brief description of this category"><?= e($editCat['description'] ?? '') ?></textarea>
                </div>

                <div class="form-group" style="margin-top: 16px;">
                    <?php if ($editCat): ?>
                        <button type="submit" class="btn btn-success" style="width: 100%;">
                            <i class="fas fa-save"></i> Update Category
                        </button>
                        <a href="blog-categories.php" class="btn btn-secondary" style="width: 100%; margin-top: 8px;">Cancel</a>
                    <?php else: ?>
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            <i class="fas fa-plus"></i> Add Category
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
@media (max-width: 1024px) {
    div[style*="grid-template-columns: 2fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
