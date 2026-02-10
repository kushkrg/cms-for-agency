<?php
/**
 * Evolvcode CMS - Single Service Page
 */

require_once __DIR__ . '/includes/config.php';

// Get service slug from URL
$slug = isset($_GET['slug']) ? Security::clean($_GET['slug']) : '';

// Get service data
$service = getServiceBySlug($slug);

if (!$service) {
    header('HTTP/1.0 404 Not Found');
    require_once __DIR__ . '/404.php';
    exit;
}

// Page meta
$pageTitle = $service['meta_title'] ?: $service['title'];
$pageDescription = $service['meta_description'] ?: $service['short_description'];
$pageKeywords = $service['meta_keywords'] ?? '';
$pageImage = $service['image'] ?? '';
$bodyClass = 'service-single-page';

// Get other services for sidebar
$allServices = getServices();

require_once INCLUDES_PATH . '/header.php';
?>

<!-- Page Header -->
<section class="section page-header" style="padding-top: 150px; background: var(--color-gray-50);">
    <div class="container">
        <div class="section-header">
            <a href="<?= e(SITE_URL) ?>/services" class="section-label" style="color: var(--color-gray-500);">
                <i class="fas fa-arrow-left"></i> All Services
            </a>
            <h1 class="section-title"><?= e($service['title']) ?></h1>
            <p class="section-description lead">
                <?= e($service['short_description']) ?>
            </p>
        </div>
    </div>
</section>

<!-- Service Content -->
<section class="section">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 350px; gap: var(--space-12);">
            <!-- Main Content -->
            <div class="post-content fade-up">
                <?php if ($service['image']): ?>
                <img src="<?= e(SITE_URL . $service['image']) ?>" 
                     alt="<?= e($service['title']) ?>" 
                     style="width: 100%; border-radius: var(--radius-xl); margin-bottom: var(--space-8);">
                <?php endif; ?>
                
                <?= $service['content'] ?>
                
                <?php if (!empty($service['features'])): ?>
                <div style="margin-top: var(--space-8); padding: var(--space-8); background: var(--color-gray-50); border-radius: var(--radius-xl);">
                    <h3>Key Features</h3>
                    <ul style="padding-left: var(--space-4); margin-top: var(--space-4);">
                        <?php foreach (parseFeatures($service['features']) as $feature): ?>
                        <li style="margin-bottom: var(--space-3); list-style: disc; color: var(--color-gray-700);">
                            <?= e($feature) ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <!-- CTA -->
                <div style="margin-top: var(--space-12); padding: var(--space-8); background: var(--color-black); color: var(--color-white); border-radius: var(--radius-xl); text-align: center;">
                    <h3 style="color: var(--color-white); margin-bottom: var(--space-4);">Ready to Get Started?</h3>
                    <p style="color: var(--color-gray-400); margin-bottom: var(--space-6);">
                        Let's discuss how we can help you with <?= e(strtolower($service['title'])) ?>.
                    </p>
                    <button type="button" class="btn btn-white open-contact-popup" data-service="<?= e($service['title']) ?>">Contact Us Today</button>
                </div>
            </div>
            
            <!-- Sidebar -->
            <aside class="slide-right">
                <!-- Other Services -->
                <div style="background: var(--color-gray-50); padding: var(--space-6); border-radius: var(--radius-xl); position: sticky; top: 100px;">
                    <h4 style="margin-bottom: var(--space-4);">Other Services</h4>
                    <ul style="display: flex; flex-direction: column; gap: var(--space-2);">
                        <?php foreach ($allServices as $s): ?>
                        <li>
                            <a href="<?= e(SITE_URL) ?>/service/<?= e($s['slug']) ?>" 
                               style="display: block; padding: var(--space-3); border-radius: var(--radius-md); transition: all var(--transition-fast); <?= $s['slug'] === $slug ? 'background: var(--color-black); color: var(--color-white);' : '' ?>"
                               class="<?= $s['slug'] !== $slug ? 'hover-bg' : '' ?>">
                                <i class="fas <?= e($s['icon'] ?: 'fa-cog') ?>" style="margin-right: var(--space-2);"></i>
                                <?= e($s['title']) ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <!-- Contact Box -->
                <div style="margin-top: var(--space-6); padding: var(--space-6); border: 1px solid var(--color-gray-200); border-radius: var(--radius-xl);">
                    <h4 style="margin-bottom: var(--space-4);">Need Help?</h4>
                    <p style="margin-bottom: var(--space-4); color: var(--color-gray-600);">
                        Have questions about this service? Get in touch!
                    </p>
                    <a href="https://api.whatsapp.com/send/?phone=<?= e(getSetting('whatsapp_number', '919229045881')) ?>" 
                       class="btn btn-primary" style="width: 100%;" target="_blank" rel="noopener">
                        <i class="fab fa-whatsapp"></i> Chat on WhatsApp
                    </a>
                </div>
            </aside>
        </div>
    </div>
</section>

<style>
.hover-bg:hover {
    background: var(--color-gray-200);
}

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
