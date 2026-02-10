<?php
/**
 * Evolvcode CMS - Site Header
 * 
 * Include this at the top of all frontend pages.
 */

// Set default meta values if not set
$pageTitle = $pageTitle ?? '';
$pageDescription = $pageDescription ?? '';
$pageKeywords = $pageKeywords ?? '';
$pageImage = $pageImage ?? '';
$bodyClass = $bodyClass ?? '';

// Set security headers
Security::setSecurityHeaders();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <?= generateMetaTags($pageTitle, $pageDescription, $pageKeywords, $pageImage) ?>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= e(ASSETS_URL) ?>/images/favicon.png">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="<?= e(ASSETS_URL) ?>/css/style.css?v=<?= time() + 13 ?>">
    
    <!-- GSAP for animations -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.4/gsap.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.4/ScrollTrigger.min.js" defer></script>
    
    <!-- Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "<?= e(getSetting('site_name', 'Evolvcode Solutions')) ?>",
        "url": "<?= e(SITE_URL) ?>",
        "logo": "<?= e(SITE_URL . getSetting('logo_path', '/assets/images/logo.png')) ?>",
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "<?= e(getSetting('contact_phone', '+91-9229045881')) ?>",
            "contactType": "customer service",
            "email": "<?= e(getSetting('contact_email', 'sales@evolvcode.com')) ?>"
        },
        "sameAs": [
            "<?= e(getSetting('facebook_url', '')) ?>",
            "<?= e(getSetting('twitter_url', '')) ?>",
            "<?= e(getSetting('linkedin_url', '')) ?>",
            "<?= e(getSetting('instagram_url', '')) ?>"
        ]
    }
    </script>
    <?php if ($recaptchaKey = getSetting('recaptcha_site_key')): ?>
    <!-- Google reCAPTCHA v3 -->
    <script src="https://www.google.com/recaptcha/api.js?render=<?= e($recaptchaKey) ?>"></script>
    <script>
        window.recaptchaSiteKey = '<?= e($recaptchaKey) ?>';
    </script>
    <?php endif; ?>
    <!-- Custom CSS -->
    <?php if ($customCss = getSetting('custom_css')): ?>
    <style>
        <?= $customCss ?>
    </style>
    <?php endif; ?>

    <!-- Custom JS (Head) -->
    <?php if ($customJsHead = getSetting('custom_js_head')): ?>
    <?= $customJsHead ?>
    <?php endif; ?>
</head>
<body class="<?= e($bodyClass) ?>">
    <!-- Preloader -->
    <div class="preloader" id="preloader">
        <div class="preloader-inner">
            <div class="preloader-logo">EVOLVCODE</div>
        </div>
    </div>
    
    <!-- Header -->
    <header class="site-header" id="header">
        <div class="container">
            <nav class="main-nav">
                <!-- Logo -->
                <a href="<?= e(SITE_URL) ?>" class="logo">
                     <?php if ($logo = getSetting('logo_path')): ?>
                         <img src="<?= e(SITE_URL . $logo) ?>" alt="<?= e(getSetting('site_name', 'Evolvcode')) ?>" class="logo-image">
                     <?php else: ?>
                        <span class="logo-text"><?= e(getSetting('site_name', 'EVOLVCODE')) ?></span>
                     <?php endif; ?>
                </a>
                
                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle" id="mobile-menu-toggle" aria-label="Toggle menu">
                    <span class="hamburger">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>
                
                <!-- Navigation Menu -->
                <div class="nav-menu" id="nav-menu">
                    <ul class="nav-list">
                        <?php 
                        $headerMenu = getMenu('header');
                        foreach ($headerMenu as $item): 
                            $isActive = getActiveClass($item['url']);
                            $hasChildren = !empty($item['children']);
                            // Special case for Services: if URL is /services and no children, show dynamic services
                            $isServices = $item['url'] === '/services' || $item['url'] === 'services.php';
                            if ($isServices && !$hasChildren) {
                                $hasChildren = true;
                                $dynamicServices = getServices(6);
                            } else {
                                $dynamicServices = [];
                            }
                        ?>
                        <li class="nav-item <?= $hasChildren ? 'has-dropdown' : '' ?>">
                            <a href="<?= strpos($item['url'], 'http') === 0 ? e($item['url']) : e(SITE_URL . $item['url']) ?>" 
                               class="nav-link <?= $isActive ?>" 
                               target="<?= e($item['target']) ?>">
                                <?= e($item['title']) ?>
                            </a>
                            
                            <?php if ($hasChildren): ?>
                            <ul class="dropdown-menu">
                                <?php if (!empty($item['children'])): ?>
                                    <?php foreach ($item['children'] as $child): ?>
                                    <li>
                                        <a href="<?= strpos($child['url'], 'http') === 0 ? e($child['url']) : e(SITE_URL . $child['url']) ?>" 
                                           target="<?= e($child['target']) ?>">
                                            <?= e($child['title']) ?>
                                        </a>
                                    </li>
                                    <?php endforeach; ?>
                                <?php elseif ($isServices): ?>
                                    <?php foreach ($dynamicServices as $service): ?>
                                    <li>
                                        <a href="<?= e(SITE_URL) ?>/service/<?= e($service['slug']) ?>">
                                            <?= e($service['title']) ?>
                                        </a>
                                    </li>
                                    <?php endforeach; ?>
                                    <li class="view-all">
                                        <a href="<?= e(SITE_URL) ?>/services">View All Services</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <!-- CTA Button -->
                    <a href="https://api.whatsapp.com/send/?phone=<?= e(getSetting('whatsapp_number', '919229045881')) ?>" 
                       class="btn btn-primary nav-cta" target="_blank" rel="noopener">
                        Get Free Consultation
                    </a>
                </div>
            </nav>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="site-main" id="main">
