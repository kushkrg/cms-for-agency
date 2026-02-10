<?php
/**
 * Evolvcode CMS - Blog Page
 */

require_once __DIR__ . '/includes/config.php';

// Page meta
$pageTitle = 'Blog';
$pageDescription = 'Evolvcode solutions | Our Blogs | Get Latest Insights';
$pageKeywords = 'blog, digital marketing tips, web development articles';
$bodyClass = 'blog-page';

// Pagination
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$page = max(1, $page);

// Category filter
$categorySlug = isset($_GET['category']) ? Security::clean($_GET['category']) : null;
$categoryId = null;

if ($categorySlug) {
    $db = Database::getInstance();
    $category = $db->fetchOne("SELECT id, name FROM blog_categories WHERE slug = ?", [$categorySlug]);
    if ($category) {
        $categoryId = $category['id'];
        $pageTitle = $category['name'] . ' - Blog';
    }
}

// Get posts
$posts = getPosts(ITEMS_PER_PAGE, ($page - 1) * ITEMS_PER_PAGE, $categoryId);
$totalPosts = Database::getInstance()->count('posts', "status = 'published'" . ($categoryId ? " AND category_id = {$categoryId}" : ''));
$categories = getBlogCategories();

require_once INCLUDES_PATH . '/header.php';
?>

<!-- Page Header -->
<section class="section page-header" style="padding-top: 150px; background: var(--color-gray-50);">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Our Blog</span>
            <h1 class="section-title"><?= $categorySlug && isset($category) ? e($category['name']) : 'Latest Insights' ?></h1>
            <p class="section-description lead">
                Stay updated with the latest trends, tips, and insights in digital marketing and web development.
            </p>
        </div>
    </div>
</section>

<!-- Blog Content -->
<section class="section">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 300px; gap: var(--space-12);">
            <!-- Posts Grid -->
            <div>
                <?php if (!empty($posts)): ?>
                <div class="blog-grid stagger-children">
                    <?php foreach ($posts as $post): ?>
                    <article class="blog-card">
                        <a href="<?= e(SITE_URL) ?>/post/<?= e($post['slug']) ?>" class="blog-card-image-wrap">
                            <?php if ($post['featured_image']): ?>
                            <img src="<?= e(SITE_URL . $post['featured_image']) ?>" 
                                 alt="<?= e($post['title']) ?>"
                                 class="blog-card-img">
                            <?php else: ?>
                            <div class="blog-card-img blog-card-placeholder"></div>
                            <?php endif; ?>
                            <?php if ($post['category_name']): ?>
                            <span class="blog-card-badge"><?= e($post['category_name']) ?></span>
                            <?php endif; ?>
                        </a>
                        <div class="blog-card-body">
                            <h3 class="blog-card-title">
                                <a href="<?= e(SITE_URL) ?>/post/<?= e($post['slug']) ?>">
                                    <?= e($post['title']) ?>
                                </a>
                            </h3>
                            <a href="<?= e(SITE_URL) ?>/post/<?= e($post['slug']) ?>" class="blog-card-link">
                                Learn more <span class="blog-card-arrow"><i class="fas fa-arrow-right"></i></span>
                            </a>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?= paginate($totalPosts, $page, ITEMS_PER_PAGE, SITE_URL . '/blog' . ($categorySlug ? "?category={$categorySlug}&" : '?')) ?>
                
                <?php else: ?>
                <div class="text-center" style="padding: var(--space-16);">
                    <p style="color: var(--color-gray-500); font-size: var(--font-size-lg);">
                        No posts found. Check back soon for new content!
                    </p>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar -->
            <aside class="slide-right">
                <!-- Categories -->
                <div style="background: var(--color-gray-50); padding: var(--space-6); border-radius: var(--radius-xl);">
                    <h4 style="margin-bottom: var(--space-4);">Categories</h4>
                    <ul style="display: flex; flex-direction: column; gap: var(--space-2);">
                        <li>
                            <a href="<?= e(SITE_URL) ?>/blog" 
                               style="display: flex; justify-content: space-between; padding: var(--space-3); border-radius: var(--radius-md); transition: all var(--transition-fast); <?= !$categorySlug ? 'background: var(--color-black); color: var(--color-white);' : '' ?>">
                                All Posts
                                <span style="opacity: 0.7;"><?= $totalPosts ?></span>
                            </a>
                        </li>
                        <?php foreach ($categories as $cat): ?>
                        <li>
                            <a href="<?= e(SITE_URL) ?>/blog?category=<?= e($cat['slug']) ?>" 
                               style="display: flex; justify-content: space-between; padding: var(--space-3); border-radius: var(--radius-md); transition: all var(--transition-fast); <?= $categorySlug === $cat['slug'] ? 'background: var(--color-black); color: var(--color-white);' : '' ?>">
                                <?= e($cat['name']) ?>
                                <span style="opacity: 0.7;"><?= e($cat['post_count']) ?></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <!-- Newsletter CTA -->
                <div style="margin-top: var(--space-6); padding: var(--space-6); background: var(--color-black); border-radius: var(--radius-xl); color: var(--color-white);">
                    <h4 style="color: var(--color-white); margin-bottom: var(--space-2);">Get Expert Tips</h4>
                    <p style="color: var(--color-gray-400); margin-bottom: var(--space-4); font-size: var(--font-size-sm);">
                        Have questions? Let's chat about your digital goals!
                    </p>
                    <a href="https://api.whatsapp.com/send/?phone=<?= e(getSetting('whatsapp_number', '919229045881')) ?>" 
                       class="btn btn-white" style="width: 100%;" target="_blank" rel="noopener">
                        <i class="fab fa-whatsapp"></i> Chat on WhatsApp
                    </a>
                </div>
            </aside>
        </div>
    </div>
</section>

<style>
@media (max-width: 1024px) {
    .section > .container > div {
        grid-template-columns: 1fr !important;
    }
    aside {
        order: -1;
    }
    aside > div {
        position: static !important;
    }
    .blog-grid {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>
