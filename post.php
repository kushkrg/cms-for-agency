<?php
/**
 * Evolvcode CMS - Single Post Page
 */

require_once __DIR__ . '/includes/config.php';

// Get post slug from URL
$slug = isset($_GET['slug']) ? Security::clean($_GET['slug']) : '';

// Get post data
$post = getPostBySlug($slug);

if (!$post) {
    header('HTTP/1.0 404 Not Found');
    require_once __DIR__ . '/404.php';
    exit;
}

// Increment view count
$db = Database::getInstance();
$db->update('posts', ['views' => $post['views'] + 1], 'id = ?', [$post['id']]);

// Page meta
$pageTitle = $post['meta_title'] ?: $post['title'];
$pageDescription = $post['meta_description'] ?: createExcerpt($post['content'], 160);
$pageImage = $post['featured_image'] ?? '';
$bodyClass = 'post-single-page';

// Get related posts
$relatedPosts = getPosts(3, 0, $post['category_id']);
$relatedPosts = array_filter($relatedPosts, fn($p) => $p['id'] !== $post['id']);
$relatedPosts = array_slice($relatedPosts, 0, 3);

require_once INCLUDES_PATH . '/header.php';
?>

<!-- Post Header -->
<section class="post-header" style="padding-top: 150px;">
    <div class="container">
        <div style="max-width: 900px; margin: 0 auto;">
            <?php if ($post['category_name']): ?>
            <a href="<?= e(SITE_URL) ?>/blog?category=<?= e($post['category_slug']) ?>" class="post-category">
                <?= e($post['category_name']) ?>
            </a>
            <?php endif; ?>
            
            <h1 class="post-title"><?= e($post['title']) ?></h1>
            
            <div class="post-meta" style="justify-content: center;">
                <?php if ($post['author']): ?>
                <span>
                    <i class="far fa-user"></i>
                    <?= e($post['author']) ?>
                </span>
                <?php endif; ?>
                <span>
                    <i class="far fa-calendar"></i>
                    <?= formatDate($post['published_at'] ?: $post['created_at']) ?>
                </span>
                <span>
                    <i class="far fa-clock"></i>
                    <?= getReadingTime($post['content']) ?> min read
                </span>
            </div>
        </div>
    </div>
</section>

<!-- Featured Image -->
<?php if ($post['featured_image']): ?>
<section style="padding-bottom: var(--space-12);">
    <div class="container">
        <div class="fade-up" style="max-width: 900px; margin: 0 auto; border-radius: var(--radius-xl); overflow: hidden;">
            <img src="<?= e(SITE_URL . $post['featured_image']) ?>" 
                 alt="<?= e($post['title']) ?>" 
                 style="width: 100%; display: block;">
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Post Content -->
<section class="section" style="padding-top: 0;">
    <div class="container">
        <article class="post-content fade-up">
            <?= $post['content'] ?>
        </article>
        
        <!-- Author Box -->
        <div class="author-box fade-up">
            <div class="author-image">
                <i class="fas fa-gem"></i>
            </div>
            <div class="author-info">
                <h3>Evolvcode</h3>
                <p>EvolvCode Solutions is a dynamic digital agency based in Patna, Bihar, India, specializing in web development and digital marketing services. Established in December 2023, the company aims to empower businesses by crafting innovative digital experiences that drive growth and success.</p>
            </div>
        </div>
        
        <!-- Share & Tags -->
        <div style="max-width: 750px; margin: var(--space-12) auto 0; padding-top: var(--space-8); border-top: 1px solid var(--color-gray-200);">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: var(--space-4);">
                <div>
                    <span style="font-weight: var(--font-semibold);">Share this post:</span>
                    <div style="display: inline-flex; gap: var(--space-2); margin-left: var(--space-2);">
                        <a href="https://twitter.com/intent/tweet?url=<?= urlencode(SITE_URL . '/post/' . $post['slug']) ?>&text=<?= urlencode($post['title']) ?>" 
                           target="_blank" rel="noopener"
                           style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background: var(--color-gray-100); border-radius: var(--radius-full);">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(SITE_URL . '/post/' . $post['slug']) ?>" 
                           target="_blank" rel="noopener"
                           style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background: var(--color-gray-100); border-radius: var(--radius-full);">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= urlencode(SITE_URL . '/post/' . $post['slug']) ?>&title=<?= urlencode($post['title']) ?>" 
                           target="_blank" rel="noopener"
                           style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background: var(--color-gray-100); border-radius: var(--radius-full);">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
                
                <a href="<?= e(SITE_URL) ?>/blog" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back to Blog
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Related Posts -->
<?php if (!empty($relatedPosts)): ?>
<section class="section" style="background: var(--color-gray-50);">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Keep Reading</span>
            <h2 class="section-title">Related Posts</h2>
        </div>
        
        <div class="blog-grid">
            <?php foreach ($relatedPosts as $relatedPost): ?>
            <article class="card post-card">
                <?php if ($relatedPost['featured_image']): ?>
                <a href="<?= e(SITE_URL) ?>/post/<?= e($relatedPost['slug']) ?>" class="card-image">
                    <img src="<?= e(SITE_URL . $relatedPost['featured_image']) ?>" 
                         alt="<?= e($relatedPost['title']) ?>">
                </a>
                <?php endif; ?>
                <div class="card-body">
                    <?php if ($relatedPost['category_name']): ?>
                    <span class="card-category"><?= e($relatedPost['category_name']) ?></span>
                    <?php endif; ?>
                    <h3 class="card-title">
                        <a href="<?= e(SITE_URL) ?>/post/<?= e($relatedPost['slug']) ?>">
                            <?= e($relatedPost['title']) ?>
                        </a>
                    </h3>
                    <div class="post-meta">
                        <span>
                            <i class="far fa-calendar"></i>
                            <?= formatDate($relatedPost['published_at'] ?: $relatedPost['created_at']) ?>
                        </span>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>
