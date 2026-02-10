<?php
require_once __DIR__ . '/includes/config.php';

// --- FRONT CONTROLLER ROUTING ---
// This handles clean URLs if the server is started with index.php as the entry point
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// If running via PHP built-in server as a router, let it handle static files
if ($uri !== '/' && file_exists(__DIR__ . $uri) && pathinfo($uri, PATHINFO_EXTENSION) !== 'php') {
    return false;
}

$routes = [
    '/about' => 'about.php',
    '/services' => 'services.php',
    '/portfolio' => 'portfolio.php',
    '/blog' => 'blog.php',
    '/contact' => 'contact.php',
    '/sitemap' => 'sitemap.php',
];

// Exact route match
if (isset($routes[$uri])) {
    require_once __DIR__ . '/' . $routes[$uri];
    exit;
}

// Dynamic routes
if (preg_match('#^/service/([a-zA-Z0-9-]+)/?$#', $uri, $matches)) {
    $_GET['slug'] = $matches[1];
    require_once __DIR__ . '/service.php';
    exit;
}
if (preg_match('#^/project/([a-zA-Z0-9-]+)/?$#', $uri, $matches)) {
    $_GET['slug'] = $matches[1];
    require_once __DIR__ . '/project.php';
    exit;
}
if (preg_match('#^/(?:post|blog)/([a-zA-Z0-9-]+)/?$#', $uri, $matches)) {
    $_GET['slug'] = $matches[1];
    require_once __DIR__ . '/post.php';
    exit;
}

// If uri is not home and not index.php, it's a 404
if ($uri !== '/' && $uri !== '/index.php' && !file_exists(__DIR__ . $uri)) {
    http_response_code(404);
    require_once __DIR__ . '/404.php';
    exit;
}
// --------------------------------


// Page meta
$pageTitle = '';
$pageDescription = getSetting('site_description', 'Boost your business with our digital solutions. Join Evolvcode now!');
$pageKeywords = 'digital marketing, web development, SEO, social media marketing, Patna';
$bodyClass = 'home-page';

// Get featured content
$featuredProjects = getProjects(3, true);
$services = getServices(6);
$latestPosts = getPosts(3);

require_once INCLUDES_PATH . '/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <!-- Background patterns -->
    <div class="hero-patterns">
        <div class="dots"></div>
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
        <div class="shape shape-4"></div>
        <div class="shape shape-5"></div>
        <div class="shape shape-6"></div>
    </div>
    
    <div class="hero-content">
        <span class="hero-badge fade-up"><?= e(getSetting('home_hero_badge', 'Digital Marketing & Web Development Agency')) ?></span>
        <h1 class="hero-title">
            <span><?= e(getSetting('home_hero_title_1', 'Transform Your')) ?></span>
            <span><?= e(getSetting('home_hero_title_2', 'Digital Presence')) ?></span>
        </h1>
        <p class="hero-description">
            <?= e(getSetting('home_hero_description', 'Design visuals that speak louder than words — turning ideas into creative designs that connect, inspire, and engage your audience.')) ?>
        </p>
        <div class="hero-buttons">
            <a href="<?= e(SITE_URL . getSetting('home_hero_btn_primary_link', '/contact')) ?>" class="btn btn-primary btn-lg"><?= e(getSetting('home_hero_btn_primary_text', 'Get Started')) ?></a>
            <a href="<?= e(SITE_URL . getSetting('home_hero_btn_secondary_link', '/portfolio')) ?>" class="btn btn-secondary btn-lg"><?= e(getSetting('home_hero_btn_secondary_text', 'View Our Work')) ?></a>
        </div>
    </div>
    <div class="scroll-indicator">
        <span>Scroll</span>
        <i class="fas fa-arrow-down"></i>
    </div>
</section>

<!-- Services Section -->
<?php if (getSetting('home_show_services', '1') === '1'): ?>
<section class="section services-section">
    <div class="container">
        <div class="section-header">
            <span class="section-label"><?= e(getSetting('home_services_label', 'What We Do')) ?></span>
            <h2 class="section-title"><?= e(getSetting('home_services_title', 'Our Services')) ?></h2>
            <p class="section-description">
                <?= e(getSetting('home_services_desc', 'We offer a comprehensive suite of digital solutions tailored to help your business grow and succeed online.')) ?>
            </p>
        </div>
        
        <div class="grid grid-3 stagger-children">
            <?php foreach ($services as $service): ?>
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas <?= e($service['icon'] ?: 'fa-cog') ?>"></i>
                </div>
                <h3 class="service-title"><?= e($service['title']) ?></h3>
                <p class="service-description"><?= e($service['short_description']) ?></p>
                <a href="<?= e(SITE_URL) ?>/service/<?= e($service['slug']) ?>" class="service-link">
                    Learn More <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center" style="margin-top: 100px;">
            <a href="<?= e(SITE_URL) ?>/services" class="btn btn-secondary">View All Services</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Why Choose Us Section -->
<?php if (getSetting('home_show_wcu', '1') === '1'): ?>
<section class="section why-choose-us-section">
    <div class="container">
        <div class="grid grid-2 items-center gap-8">
            <div class="why-choose-content">
                <div class="section-header" style="text-align: left; margin: 0 0 var(--space-8) 0; max-width: 100%;">
                    <span class="section-label"><?= e(getSetting('home_wcu_label', 'Why Choose Evolvcode')) ?></span>
                    <h2 class="section-title"><?= e(getSetting('home_wcu_title', 'Your Growth, Our Mission')) ?></h2>
                    <p class="section-description">
                        <?= e(getSetting('home_wcu_desc', 'We combine creativity with technical expertise to deliver digital solutions that drive real business results.')) ?>
                    </p>
                </div>
                
                <ul class="why-choose-list">
                    <li class="why-item">
                        <div class="why-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="why-text">
                            <h4>Data-Driven Strategy</h4>
                            <p>We don't guess. We analyze data to create strategies that maximize your ROI.</p>
                        </div>
                    </li>
                    <li class="why-item">
                        <div class="why-icon">
                            <i class="fas fa-gem"></i>
                        </div>
                        <div class="why-text">
                            <h4>Premium Quality</h4>
                            <p>We deliver pixel-perfect designs and clean code that stands out in the market.</p>
                        </div>
                    </li>
                    <li class="why-item">
                        <div class="why-icon">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <div class="why-text">
                            <h4>Dedicated Support</h4>
                            <p>We are not just a vendor; we are your partner in long-term success.</p>
                        </div>
                    </li>
                </ul>
                
                <div style="margin-top: var(--space-8);">
                    <a href="<?= e(SITE_URL) ?>/contact" class="btn btn-primary">Start Your Project</a>
                </div>
            </div>
            
            <div class="why-choose-image">
                <img src="<?= e(SITE_URL) ?>/assets/images/why-choose-us.png" alt="Why Choose Evolvcode" width="600" height="400">
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Featured Projects Section -->
<?php if (getSetting('home_show_projects', '1') === '1'): ?>
<section class="section portfolio-section">
    <div class="container">
        <div class="section-header">
            <span class="section-label"><?= e(getSetting('home_projects_label', 'Our Work')) ?></span>
            <h2 class="section-title"><?= e(getSetting('home_projects_title', 'Featured Projects')) ?></h2>
            <p class="section-description">
                <?= e(getSetting('home_projects_desc', 'Take a look at some of our recent work and see how we help businesses succeed online.')) ?>
            </p>
        </div>
        
        <div class="portfolio-grid stagger-children">
            <?php foreach ($featuredProjects as $project): ?>
            <article class="blog-card">
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
        
        <div class="text-center" style="margin-top: 100px;">
            <a href="<?= e(SITE_URL) ?>/portfolio" class="btn btn-primary">View All Projects</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Digital Marketing Process Section -->
<!-- Digital Marketing Process Section -->
<!-- Digital Marketing Process Section -->
<?php if (getSetting('home_show_process', '1') === '1'): ?>
<section class="section dm-process-section">
    <div class="container">
        <div class="section-header">
            <span class="section-label"><?= e(getSetting('home_process_label', 'Our Process')) ?></span>
            <h2 class="section-title"><?= e(getSetting('home_process_title', 'How We Do Digital Marketing at Evolvcode')) ?></h2>
            <p class="section-description">
                <?= e(getSetting('home_process_desc', 'We keep things simple, clear, and results-focused—taking your brand from "just online" to "growing online."')) ?>
            </p>
        </div>
        
        <div class="dm-process-grid stagger-children">
            <!-- Step 1 -->
            <div class="dm-process-item">
                <div class="dm-step-number">01</div>
                <div class="dm-process-icon">
                    <i class="fas fa-handshake"></i>
                </div>
                <h4 class="dm-process-title">Understand</h4>
                <p class="dm-process-desc">We dive deep into your business goals and audience.</p>
            </div>
            
            <!-- Step 2 -->
            <div class="dm-process-item">
                <div class="dm-step-number">02</div>
                <div class="dm-process-icon">
                    <i class="fas fa-search-dollar"></i>
                </div>
                <h4 class="dm-process-title">Strategy</h4>
                <p class="dm-process-desc">Data-driven planning to target the right customers.</p>
            </div>
            
            <!-- Step 3 -->
            <div class="dm-process-item">
                <div class="dm-step-number">03</div>
                <div class="dm-process-icon">
                    <i class="fas fa-pencil-ruler"></i>
                </div>
                <h4 class="dm-process-title">Create</h4>
                <p class="dm-process-desc">Designing assets and copy that converts.</p>
            </div>
            
            <!-- Step 4 -->
            <div class="dm-process-item">
                <div class="dm-step-number">04</div>
                <div class="dm-process-icon">
                    <i class="fas fa-rocket"></i>
                </div>
                <h4 class="dm-process-title">Launch</h4>
                <p class="dm-process-desc">Executing campaigns across optimized channels.</p>
            </div>
            
            <!-- Step 5 -->
            <div class="dm-process-item">
                <div class="dm-step-number">05</div>
                <div class="dm-process-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h4 class="dm-process-title">Optimize</h4>
                <p class="dm-process-desc">Continuous monitoring and ROI improvement.</p>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Trust Section -->
<!-- Trust Section -->
<?php if (getSetting('home_show_trust', '1') === '1'): ?>
<section class="section trust-section">
    <div class="container">
        <div class="grid grid-3 stagger-children">
            <div class="text-center">
                <div style="font-size: 3rem; margin-bottom: var(--space-4);">✨</div>
                <h3>Transparency</h3>
                <p>Open communication and clear expectations every step of the way.</p>
            </div>
            <div class="text-center">
                <div style="font-size: 3rem; margin-bottom: var(--space-4);">🏆</div>
                <h3>Experienced Team</h3>
                <p>A wealth of expertise and knowledge to deliver exceptional results.</p>
            </div>
            <div class="text-center">
                <div style="font-size: 3rem; margin-bottom: var(--space-4);">🎯</div>
                <h3>Result Guarantee</h3>
                <p>We ensure you receive the outcomes you expect, backed by our commitment.</p>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Latest Blog Section -->
<?php if (!empty($latestPosts)): ?>
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Our Blog</span>
            <h2 class="section-title">Latest Insights</h2>
            <p class="section-description">
                Stay updated with the latest trends, tips, and insights in digital marketing and web development.
            </p>
        </div>
        
        <div class="blog-grid stagger-children">
            <?php foreach ($latestPosts as $post): ?>
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
        
        <div class="text-center" style="margin-top: 100px;">
            <a href="<?= e(SITE_URL) ?>/blog" class="btn btn-secondary">View All Posts</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<?php if (getSetting('home_show_cta', '1') === '1'): ?>
<section class="section cta-section" style="background: var(--color-gray-100);">
    <div class="container">
        <div class="text-center" style="max-width: 700px; margin: 0 auto;">
            <h2 class="section-title"><?= e(getSetting('home_cta_title', 'Ready to Transform Your Business?')) ?></h2>
            <p class="section-description mb-8">
                <?= e(getSetting('home_cta_desc', "Let's discuss how we can help you achieve your digital goals. Get a free consultation today!")) ?>
            </p>
            <div class="hero-buttons">
                <a href="https://api.whatsapp.com/send/?phone=<?= e(getSetting('whatsapp_number', '919229045881')) ?>" 
                   class="btn btn-primary btn-lg" target="_blank" rel="noopener">
                    <i class="fab fa-whatsapp"></i> Chat on WhatsApp
                </a>
                <a href="<?= e(SITE_URL . getSetting('home_cta_btn_link', '/contact')) ?>" class="btn btn-secondary btn-lg"><?= e(getSetting('home_cta_btn_text', 'Contact Us')) ?></a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>
