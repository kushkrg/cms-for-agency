<?php
/**
 * Evolvcode CMS - Dynamic Page Template
 * 
 * Renders pages from the `pages` table by slug.
 */

require_once __DIR__ . '/includes/config.php';

// Get page slug from URL
$slug = isset($_GET['slug']) ? Security::clean($_GET['slug']) : '';

// Get page data
$pageData = getPageBySlug($slug);

if (!$pageData) {
    header('HTTP/1.0 404 Not Found');
    require_once __DIR__ . '/404.php';
    exit;
}

// Page meta
$pageTitle = $pageData['meta_title'] ?: $pageData['title'];
$pageDescription = $pageData['meta_description'] ?? '';
$pageKeywords = $pageData['meta_keywords'] ?? '';
$bodyClass = 'page-' . $pageData['slug'];

require_once INCLUDES_PATH . '/header.php';
?>

<!-- Page Header -->
<section class="section page-header" style="padding-top: 150px; background: var(--color-gray-50);">
    <div class="container">
        <div class="section-header">
            <h1 class="section-title"><?= e($pageData['title']) ?></h1>
        </div>
    </div>
</section>

<!-- Page Content -->
<section class="section">
    <div class="container">
        <div class="post-content fade-up" style="max-width: 800px; margin: 0 auto;">
            <?= $pageData['content'] ?>
        </div>
    </div>
</section>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>
