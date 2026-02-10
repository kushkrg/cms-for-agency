<?php
/**
 * Evolvcode CMS - 404 Error Page
 */

require_once __DIR__ . '/includes/config.php';

// Set 404 header if not already set
if (!headers_sent()) {
    header('HTTP/1.0 404 Not Found');
}

// Page meta
$pageTitle = 'Page Not Found';
$pageDescription = 'The page you are looking for could not be found.';
$bodyClass = 'error-page-body';

require_once INCLUDES_PATH . '/header.php';
?>

<section class="error-page">
    <div class="container">
        <div class="fade-up">
            <div class="error-code">404</div>
            <h1 class="error-title">Page Not Found</h1>
            <p class="error-description">
                Oops! The page you are looking for doesn't exist or has been moved.
            </p>
            <div style="display: flex; gap: var(--space-4); justify-content: center; flex-wrap: wrap;">
                <a href="<?= e(SITE_URL) ?>" class="btn btn-primary btn-lg">
                    <i class="fas fa-home"></i> Go Home
                </a>
                <a href="<?= e(SITE_URL) ?>/contact" class="btn btn-secondary btn-lg">
                    <i class="fas fa-envelope"></i> Contact Us
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>
