<?php
/**
 * Evolvcode CMS - Portfolio Page
 */

require_once __DIR__ . '/includes/config.php';

// Page meta
$pageTitle = 'Portfolio';
$pageDescription = 'See our successful projects and get inspired to elevate your business!';
$pageKeywords = 'portfolio, projects, web development, case studies';
$bodyClass = 'portfolio-page';

// Get all projects
$projects = getProjects();
$categories = getPortfolioCategories();

require_once INCLUDES_PATH . '/header.php';
?>

<!-- Page Header -->
<section class="section page-header" style="padding-top: 150px; background: var(--color-gray-50);">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Our Work</span>
            <h1 class="section-title">Portfolio</h1>
            <p class="section-description lead">
                Take a look at some of our recent projects and see how we help businesses succeed online.
            </p>
        </div>
    </div>
</section>

<!-- Portfolio Grid -->
<section class="section">
    <div class="container">
        <!-- Filters -->
        <?php if (count($categories) > 0): ?>
        <div class="portfolio-filters">
            <button class="filter-btn active" data-filter="all">All Projects</button>
            <?php foreach ($categories as $category): ?>
            <button class="filter-btn" data-filter="<?= e($category['slug']) ?>">
                <?= e($category['name']) ?>
            </button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <!-- Projects Grid -->
        <div class="portfolio-grid stagger-children">
            <?php foreach ($projects as $project): ?>
            <article class="blog-card" data-category="<?= e($project['category_slug'] ?? 'uncategorized') ?>">
                <a href="<?= e(SITE_URL) ?>/project/<?= e($project['slug']) ?>" class="blog-card-image-wrap">
                    <?php if ($project['featured_image']): ?>
                    <img src="<?= e(SITE_URL . $project['featured_image']) ?>" 
                         alt="<?= e($project['title']) ?>"
                         class="blog-card-img">
                    <?php else: ?>
                    <div class="blog-card-img blog-card-placeholder"></div>
                    <?php endif; ?>
                    <?php if ($project['category_name']): ?>
                    <span class="blog-card-badge"><?= e($project['category_name']) ?></span>
                    <?php endif; ?>
                </a>
                <div class="blog-card-body">
                    <h3 class="blog-card-title">
                        <a href="<?= e(SITE_URL) ?>/project/<?= e($project['slug']) ?>">
                            <?= e($project['title']) ?>
                        </a>
                    </h3>
                    <a href="<?= e(SITE_URL) ?>/project/<?= e($project['slug']) ?>" class="blog-card-link">
                        Learn more <span class="blog-card-arrow"><i class="fas fa-arrow-right"></i></span>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        
        <?php if (empty($projects)): ?>
        <div class="text-center" style="padding: var(--space-16);">
            <p style="color: var(--color-gray-500); font-size: var(--font-size-lg);">
                No projects found. Check back soon for our latest work!
            </p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA -->
<section class="section cta-section" style="background: var(--color-black); color: var(--color-white);">
    <div class="container">
        <div class="text-center" style="max-width: 700px; margin: 0 auto;">
            <h2 class="section-title" style="color: var(--color-white);">Have a Project in Mind?</h2>
            <p class="section-description" style="color: var(--color-gray-400);">
                Let's work together to bring your ideas to life.
            </p>
            <a href="<?= e(SITE_URL) ?>/contact" class="btn btn-white btn-lg mt-8">Start Your Project</a>
        </div>
    </div>
</section>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>
