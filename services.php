<?php
/**
 * Evolvcode CMS - Services Page
 */

require_once __DIR__ . '/includes/config.php';

// Page meta
$pageTitle = 'Our Services';
$pageDescription = 'Explore our comprehensive digital marketing and web development services designed to help your business grow online.';
$pageKeywords = 'digital marketing services, web development, SEO, PPC, social media marketing';
$bodyClass = 'services-page';

// Get all services
$services = getServices();

require_once INCLUDES_PATH . '/header.php';
?>

<!-- Page Header -->
<section class="section page-header" style="padding-top: 150px; background: var(--color-gray-50);">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Our Services</span>
            <h1 class="section-title">Digital Solutions for Your Business</h1>
            <p class="section-description lead">
                We offer a comprehensive suite of digital marketing and web development services tailored to help your business grow and succeed online.
            </p>
        </div>
    </div>
</section>

<!-- Services Grid -->
<section class="section">
    <div class="container">
        <div class="grid grid-3 stagger-children">
            <?php foreach ($services as $service): ?>
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas <?= e($service['icon'] ?: 'fa-cog') ?>"></i>
                </div>
                <h3 class="service-title"><?= e($service['title']) ?></h3>
                <p class="service-description"><?= e($service['short_description']) ?></p>
                
                <?php if (!empty($service['features'])): ?>
                <ul class="service-features">
                    <?php foreach (array_slice(parseFeatures($service['features']), 0, 4) as $feature): ?>
                    <li><?= e($feature) ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
                
                <a href="<?= e(SITE_URL) ?>/service/<?= e($service['slug']) ?>" class="service-link">
                    Learn More <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="section" style="background: var(--color-black); color: var(--color-white);">
    <div class="container">
        <div class="section-header">
            <span class="section-label" style="color: var(--color-gray-400);">Why Choose Us</span>
            <h2 class="section-title" style="color: var(--color-white);">Benefits of Working With Us</h2>
        </div>
        
        <div class="grid grid-4 stagger-children">
            <div class="text-center" style="padding: var(--space-6);">
                <div style="font-size: 2.5rem; margin-bottom: var(--space-4);">📊</div>
                <h4 style="color: var(--color-white); font-size: var(--font-size-xl); margin-bottom: var(--space-3);">Data-Driven</h4>
                <p style="color: var(--color-gray-300); font-size: var(--font-size-base); line-height: 1.6;">Every decision backed by analytics</p>
            </div>
            <div class="text-center" style="padding: var(--space-6);">
                <div style="font-size: 2.5rem; margin-bottom: var(--space-4);">🎯</div>
                <h4 style="color: var(--color-white); font-size: var(--font-size-xl); margin-bottom: var(--space-3);">Results-Focused</h4>
                <p style="color: var(--color-gray-300); font-size: var(--font-size-base); line-height: 1.6;">Measurable outcomes guaranteed</p>
            </div>
            <div class="text-center" style="padding: var(--space-6);">
                <div style="font-size: 2.5rem; margin-bottom: var(--space-4);">🤝</div>
                <h4 style="color: var(--color-white); font-size: var(--font-size-xl); margin-bottom: var(--space-3);">Client-Centric</h4>
                <p style="color: var(--color-gray-300); font-size: var(--font-size-base); line-height: 1.6;">Your goals are our priority</p>
            </div>
            <div class="text-center" style="padding: var(--space-6);">
                <div style="font-size: 2.5rem; margin-bottom: var(--space-4);">⚡</div>
                <h4 style="color: var(--color-white); font-size: var(--font-size-xl); margin-bottom: var(--space-3);">Fast Delivery</h4>
                <p style="color: var(--color-gray-300); font-size: var(--font-size-base); line-height: 1.6;">Quick turnaround without compromising quality</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="section cta-section" style="background: var(--color-gray-100);">
    <div class="container">
        <div class="text-center" style="max-width: 700px; margin: 0 auto;">
            <h2 class="section-title">Ready to Get Started?</h2>
            <p class="section-description mb-8">
                Contact us today to discuss your project and get a free consultation.
            </p>
            <a href="<?= e(SITE_URL) ?>/contact" class="btn btn-primary btn-lg">Get Free Consultation</a>
        </div>
    </div>
</section>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>
