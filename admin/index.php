<?php
/**
 * Evolvcode CMS - Admin Dashboard
 */

$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/header.php';

$db = Database::getInstance();

// Get statistics
$stats = [
    'services' => $db->count('services'),
    'projects' => $db->count('projects'),
    'posts' => $db->count('posts'),
    'contacts' => $db->count('contact_submissions'),
    'unread' => $db->count('contact_submissions', 'is_read = 0'),
    'pages' => $db->count('pages'),
];

// Recent contacts
$recentContacts = $db->fetchAll(
    "SELECT * FROM contact_submissions ORDER BY created_at DESC LIMIT 5"
);

// Recent posts
$recentPosts = $db->fetchAll(
    "SELECT * FROM posts ORDER BY created_at DESC LIMIT 5"
);
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Welcome back! Here's what's happening with your site.</p>
    </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-concierge-bell"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?= $stats['services'] ?></div>
            <div class="stat-label">Services</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-briefcase"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?= $stats['projects'] ?></div>
            <div class="stat-label">Projects</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-newspaper"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?= $stats['posts'] ?></div>
            <div class="stat-label">Blog Posts</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-envelope"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?= $stats['contacts'] ?></div>
            <div class="stat-label">Contact Messages</div>
            <?php if ($stats['unread'] > 0): ?>
            <span style="font-size: 12px; color: var(--color-error);">
                <?= $stats['unread'] ?> unread
            </span>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
    <!-- Recent Contacts -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Messages</h3>
            <a href="<?= e(ADMIN_URL) ?>/contacts.php" class="btn btn-sm btn-secondary">View All</a>
        </div>
        <div class="card-body" style="padding: 0;">
            <?php if (!empty($recentContacts)): ?>
            <table class="data-table">
                <tbody>
                    <?php foreach ($recentContacts as $contact): ?>
                    <tr>
                        <td>
                            <strong><?= e($contact['name']) ?></strong>
                            <div style="font-size: 12px; color: var(--color-gray-500);">
                                <?= e($contact['email']) ?>
                            </div>
                        </td>
                        <td style="text-align: right; font-size: 12px; color: var(--color-gray-500);">
                            <?= formatDate($contact['created_at'], 'M j') ?>
                            <?php if (!$contact['is_read']): ?>
                            <span style="display: inline-block; width: 8px; height: 8px; background: var(--color-error); border-radius: 50%; margin-left: 6px;"></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <p>No messages yet</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Recent Posts -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Posts</h3>
            <a href="<?= e(ADMIN_URL) ?>/posts.php" class="btn btn-sm btn-secondary">View All</a>
        </div>
        <div class="card-body" style="padding: 0;">
            <?php if (!empty($recentPosts)): ?>
            <table class="data-table">
                <tbody>
                    <?php foreach ($recentPosts as $post): ?>
                    <tr>
                        <td>
                            <strong><?= e($post['title']) ?></strong>
                            <div style="font-size: 12px; color: var(--color-gray-500);">
                                <?= e(createExcerpt($post['content'], 50)) ?>
                            </div>
                        </td>
                        <td style="text-align: right;">
                            <span class="status-badge status-<?= $post['status'] ?>">
                                <?= ucfirst($post['status']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <p>No posts yet</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card" style="margin-top: 24px;">
    <div class="card-header">
        <h3 class="card-title">Quick Actions</h3>
    </div>
    <div class="card-body">
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="<?= e(ADMIN_URL) ?>/posts.php?action=new" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Blog Post
            </a>
            <a href="<?= e(ADMIN_URL) ?>/projects.php?action=new" class="btn btn-secondary">
                <i class="fas fa-plus"></i> New Project
            </a>
            <a href="<?= e(ADMIN_URL) ?>/services.php?action=new" class="btn btn-secondary">
                <i class="fas fa-plus"></i> New Service
            </a>
            <a href="<?= e(SITE_URL) ?>" class="btn btn-secondary" target="_blank">
                <i class="fas fa-external-link-alt"></i> View Website
            </a>
        </div>
    </div>
</div>

<style>
@media (max-width: 1024px) {
    div[style*="grid-template-columns: 1fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
