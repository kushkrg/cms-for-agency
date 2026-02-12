<?php
/**
 * Evolvcode CMS - Single Project Page
 */

require_once __DIR__ . '/includes/config.php';

// Get project slug from URL
$slug = isset($_GET['slug']) ? Security::clean($_GET['slug']) : '';

// Get project data
$project = getProjectBySlug($slug);

if (!$project) {
    header('HTTP/1.0 404 Not Found');
    require_once __DIR__ . '/404.php';
    exit;
}

// Page meta
$pageTitle = $project['meta_title'] ?: $project['title'];
$pageDescription = $project['meta_description'] ?: $project['short_description'];
$pageImage = $project['featured_image'] ?? '';
$bodyClass = 'project-single-page';

// Get related projects
$relatedProjects = getProjects(3);
$relatedProjects = array_filter($relatedProjects, fn($p) => $p['id'] !== $project['id']);
$relatedProjects = array_slice($relatedProjects, 0, 3);

require_once INCLUDES_PATH . '/header.php';
?>

<!-- Project Header -->
<section class="section page-header" style="padding-top: 150px; background: var(--color-gray-50);">
    <div class="container">
        <div class="section-header">
            <a href="<?= e(SITE_URL) ?>/portfolio" class="section-label" style="color: var(--color-gray-500);">
                <i class="fas fa-arrow-left"></i> All Projects
            </a>
            <?php if ($project['category_name']): ?>
            <span class="section-label"><?= e($project['category_name']) ?></span>
            <?php endif; ?>
            <h1 class="section-title"><?= e($project['title']) ?></h1>
            <?php if ($project['client_name']): ?>
            <p class="section-description">Client: <?= e($project['client_name']) ?></p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Project Featured Image -->
<?php if ($project['featured_image']): ?>
<section style="padding-bottom: var(--space-12);">
    <div class="container">
        <div class="fade-up" style="background: var(--color-gray-100); border-radius: var(--radius-xl); overflow: hidden;">
            <img src="<?= e(SITE_URL . $project['featured_image']) ?>" 
                 alt="<?= e($project['title']) ?>" 
                 style="width: 100%; display: block;">
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Project Content -->
<section class="section">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 350px; gap: var(--space-12);">
            <!-- Main Content -->
            <div class="post-content fade-up">
                <?php if ($project['short_description']): ?>
                <p class="lead"><?= e($project['short_description']) ?></p>
                <?php endif; ?>
                
                <?= $project['content'] ?>
                
                <?php if ($project['tech_stack']): ?>
                <div style="margin-top: var(--space-8);">
                    <h3>Technologies Used</h3>
                    <div style="display: flex; flex-wrap: wrap; gap: var(--space-2); margin-top: var(--space-4);">
                        <?php foreach (explode(',', $project['tech_stack']) as $tech): ?>
                        <span style="padding: var(--space-2) var(--space-4); background: var(--color-gray-100); border-radius: var(--radius-full); font-size: var(--font-size-sm);">
                            <?= e(trim($tech)) ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar -->
            <aside class="slide-right">
                <!-- Project Details -->
                <div style="background: var(--color-gray-50); padding: var(--space-6); border-radius: var(--radius-xl);">
                    <h4 style="margin-bottom: var(--space-4);">Project Details</h4>
                    
                    <dl style="display: flex; flex-direction: column; gap: var(--space-4);">
                        <?php if ($project['client_name']): ?>
                        <div>
                            <dt style="font-size: var(--font-size-sm); color: var(--color-gray-500);">Client</dt>
                            <dd style="font-weight: var(--font-medium);"><?= e($project['client_name']) ?></dd>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($project['category_name']): ?>
                        <div>
                            <dt style="font-size: var(--font-size-sm); color: var(--color-gray-500);">Category</dt>
                            <dd style="font-weight: var(--font-medium);"><?= e($project['category_name']) ?></dd>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($project['completed_at']): ?>
                        <div>
                            <dt style="font-size: var(--font-size-sm); color: var(--color-gray-500);">Completed</dt>
                            <dd style="font-weight: var(--font-medium);"><?= formatDate($project['completed_at'], 'F Y') ?></dd>
                        </div>
                        <?php endif; ?>
                    </dl>
                    
                    <?php if ($project['project_url']): ?>
                    <a href="<?= e($project['project_url']) ?>" 
                       class="btn btn-primary" 
                       style="width: 100%; margin-top: var(--space-6);"
                       target="_blank" rel="noopener">
                        <i class="fas fa-external-link-alt"></i> Visit Website
                    </a>
                    <?php endif; ?>
                </div>
                
                <!-- Start Project CTA -->
                <div style="margin-top: var(--space-6); padding: var(--space-6); background: var(--color-black); border-radius: var(--radius-xl); color: var(--color-white);">
                    <h4 style="color: var(--color-white); margin-bottom: var(--space-2);">Start Your Project</h4>
                    <p style="color: var(--color-gray-400); margin-bottom: var(--space-4); font-size: var(--font-size-sm);">
                        Ready to bring your ideas to life? Let's talk!
                    </p>
                    <a href="<?= e(SITE_URL) ?>/contact" class="btn btn-white" style="width: 100%;">
                        Contact Us
                    </a>
                </div>
            </aside>
        </div>
    </div>
</section>

<!-- Related Projects -->
<?php if (!empty($relatedProjects)): ?>
<section class="section" style="background: var(--color-gray-50);">
    <div class="container">
        <div class="section-header">
            <span class="section-label">More Work</span>
            <h2 class="section-title">Related Projects</h2>
        </div>
        
        <div class="portfolio-grid stagger-children">
            <?php foreach ($relatedProjects as $relatedProject): ?>
            <article class="blog-card">
                <a href="<?= e(SITE_URL) ?>/project/<?= e($relatedProject['slug']) ?>" class="blog-card-image-wrap">
                    <?php if ($relatedProject['featured_image']): ?>
                    <img src="<?= e(SITE_URL . $relatedProject['featured_image']) ?>" 
                         alt="<?= e($relatedProject['title']) ?>"
                         class="blog-card-img">
                    <?php else: ?>
                    <div class="blog-card-img blog-card-placeholder"></div>
                    <?php endif; ?>
                    <?php if ($relatedProject['category_name']): ?>
                    <span class="blog-card-badge"><?= e($relatedProject['category_name']) ?></span>
                    <?php endif; ?>
                </a>
                <div class="blog-card-body">
                    <h3 class="blog-card-title">
                        <a href="<?= e(SITE_URL) ?>/project/<?= e($relatedProject['slug']) ?>">
                            <?= e($relatedProject['title']) ?>
                        </a>
                    </h3>
                    <a href="<?= e(SITE_URL) ?>/project/<?= e($relatedProject['slug']) ?>" class="blog-card-link">
                        Learn more <span class="blog-card-arrow"><i class="fas fa-arrow-right"></i></span>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

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
}
</style>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>
