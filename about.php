<?php
/**
 * Evolvcode CMS - About Page
 */

require_once __DIR__ . '/includes/config.php';

// Page meta
$pageTitle = 'About Us';
$pageDescription = 'Transform your business with our innovative digital solutions. Get a free consultation today and watch your growth soar!';
$pageKeywords = 'about evolvcode, digital marketing agency, web development, team';
$bodyClass = 'about-page';

require_once INCLUDES_PATH . '/header.php';
?>

<!-- Page Header -->
<section class="section page-header" style="padding-top: 150px; background: var(--color-gray-50);">
    <div class="container">
        <div class="section-header">
            <span class="section-label">About Us</span>
            <h1 class="section-title">Empowering Businesses with Smart Digital Solutions</h1>
            <p class="section-description lead">
                We're a team of creative minds, tech experts, and marketing pros who love helping businesses shine online.
            </p>
        </div>
    </div>
</section>

<!-- Who We Are Section -->
<section class="section">
    <div class="container">
        <div class="grid grid-2" style="gap: var(--space-16); align-items: center;">
            <div class="slide-left">
                <span class="section-label">Who We Are</span>
                <h2 class="section-title" style="text-align: left;">Your Digital Growth Partner</h2>
                <p class="lead">
                    Based in India and serving clients worldwide, we bring a human touch to every digital project.
                </p>
                <p>
                    Whether it's a website, a mobile app, or a digital campaign — we treat every project like our own. 
                    Our mission is simple: to help businesses of all sizes establish a strong online presence and achieve 
                    measurable growth through smart digital strategies.
                </p>
                <a style="margin-top: var(--space-12);" href="https://api.whatsapp.com/send/?phone=<?= e(getSetting('whatsapp_number', '919229045881')) ?>" 
                   class="btn btn-primary" target="_blank" rel="noopener">
                    Get Free Consultation
                </a>
            </div>
            <div class="slide-right" style="background: var(--color-gray-100); border-radius: var(--radius-xl); padding: var(--space-12); aspect-ratio: 4/3; display: flex; align-items: center; justify-content: center;">
                <div style="font-size: 8rem;">🚀</div>
            </div>
        </div>
    </div>
</section>

<!-- What We Do Section -->
<section class="section" style="background: var(--color-black); color: var(--color-white);">
    <div class="container">
        <div class="section-header">
            <span class="section-label" style="color: var(--color-gray-400);">What We Do</span>
            <h2 class="section-title" style="color: var(--color-white);">Our Expertise</h2>
        </div>
        
        <div class="grid grid-4 stagger-children" style="gap: var(--space-6);">
            <div style="padding: var(--space-6); background: var(--color-gray-900); border-radius: var(--radius-lg);">
                <div style="font-size: 2rem; margin-bottom: var(--space-3);">✨</div>
                <h4 style="color: var(--color-white); margin-bottom: var(--space-2);">Website Development</h4>
                <p style="color: var(--color-gray-400); margin: 0; font-size: var(--font-size-sm);">WordPress, Shopify, Custom</p>
            </div>
            <div style="padding: var(--space-6); background: var(--color-gray-900); border-radius: var(--radius-lg);">
                <div style="font-size: 2rem; margin-bottom: var(--space-3);">📈</div>
                <h4 style="color: var(--color-white); margin-bottom: var(--space-2);">Digital Marketing</h4>
                <p style="color: var(--color-gray-400); margin: 0; font-size: var(--font-size-sm);">SEO, Google Ads, Social Media</p>
            </div>
            <div style="padding: var(--space-6); background: var(--color-gray-900); border-radius: var(--radius-lg);">
                <div style="font-size: 2rem; margin-bottom: var(--space-3);">🛒</div>
                <h4 style="color: var(--color-white); margin-bottom: var(--space-2);">E-commerce Solutions</h4>
                <p style="color: var(--color-gray-400); margin: 0; font-size: var(--font-size-sm);">Online store setup, payments, tracking</p>
            </div>
            <div style="padding: var(--space-6); background: var(--color-gray-900); border-radius: var(--radius-lg);">
                <div style="font-size: 2rem; margin-bottom: var(--space-3);">🔄</div>
                <h4 style="color: var(--color-white); margin-bottom: var(--space-2);">Migration & Upgrades</h4>
                <p style="color: var(--color-gray-400); margin: 0; font-size: var(--font-size-sm);">From old to new, we make it easy</p>
            </div>
            <div style="padding: var(--space-6); background: var(--color-gray-900); border-radius: var(--radius-lg);">
                <div style="font-size: 2rem; margin-bottom: var(--space-3);">📊</div>
                <h4 style="color: var(--color-white); margin-bottom: var(--space-2);">PPC & Lead Generation</h4>
                <p style="color: var(--color-gray-400); margin: 0; font-size: var(--font-size-sm);">Get real leads that grow your business</p>
            </div>
            <div style="padding: var(--space-6); background: var(--color-gray-900); border-radius: var(--radius-lg);">
                <div style="font-size: 2rem; margin-bottom: var(--space-3);">🎨</div>
                <h4 style="color: var(--color-white); margin-bottom: var(--space-2);">Design & Branding</h4>
                <p style="color: var(--color-gray-400); margin: 0; font-size: var(--font-size-sm);">Logos, creatives, and clean UI/UX</p>
            </div>
            <div style="padding: var(--space-6); background: var(--color-gray-900); border-radius: var(--radius-lg);">
                <div style="font-size: 2rem; margin-bottom: var(--space-3);">🧠</div>
                <h4 style="color: var(--color-white); margin-bottom: var(--space-2);">Strategy & Consulting</h4>
                <p style="color: var(--color-gray-400); margin: 0; font-size: var(--font-size-sm);">Tech advice you can trust</p>
            </div>
            <div style="padding: var(--space-6); background: var(--color-gray-900); border-radius: var(--radius-lg);">
                <div style="font-size: 2rem; margin-bottom: var(--space-3);">📱</div>
                <h4 style="color: var(--color-white); margin-bottom: var(--space-2);">App Development</h4>
                <p style="color: var(--color-gray-400); margin: 0; font-size: var(--font-size-sm);">iOS, Android, Cross-Platform</p>
            </div>
        </div>
    </div>
</section>

<!-- Who We Work With Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Who We Work With</span>
            <h2 class="section-title">Industries We Serve</h2>
            <p class="section-description">
                No matter your industry, we'll build what works best for your audience.
            </p>
        </div>
        
        <div class="grid grid-4 stagger-children" style="text-align: center;">
            <div class="fade-up">
                <div style="font-size: 3rem; margin-bottom: var(--space-3);">🏪</div>
                <h4>Local Shops</h4>
            </div>
            <div class="fade-up">
                <div style="font-size: 3rem; margin-bottom: var(--space-3);">🧑‍💼</div>
                <h4>Startups</h4>
            </div>
            <div class="fade-up">
                <div style="font-size: 3rem; margin-bottom: var(--space-3);">🏢</div>
                <h4>Enterprises</h4>
            </div>
            <div class="fade-up">
                <div style="font-size: 3rem; margin-bottom: var(--space-3);">💻</div>
                <h4>Freelancers & Coaches</h4>
            </div>
            <div class="fade-up">
                <div style="font-size: 3rem; margin-bottom: var(--space-3);">🏥</div>
                <h4>Clinics & Medical Brands</h4>
            </div>
            <div class="fade-up">
                <div style="font-size: 3rem; margin-bottom: var(--space-3);">🧵</div>
                <h4>Fashion & Lifestyle</h4>
            </div>
            <div class="fade-up">
                <div style="font-size: 3rem; margin-bottom: var(--space-3);">🛠️</div>
                <h4>Service Companies</h4>
            </div>
            <div class="fade-up">
                <div style="font-size: 3rem; margin-bottom: var(--space-3);">🎓</div>
                <h4>Education & Training</h4>
            </div>
        </div>
    </div>
</section>

<!-- How We Work Section -->
<section class="section" style="background: var(--color-gray-50);">
    <div class="container">
        <div class="section-header">
            <span class="section-label">How We Work</span>
            <h2 class="section-title">Our Simple Process</h2>
            <p class="section-description">
                We don't overcomplicate things. We keep you involved and informed at every step.
            </p>
        </div>
        
        <div class="process-grid">
            <div class="process-step">
                <h4 class="process-title">Understand your needs 🤝</h4>
                <p class="process-description">We listen first, then advise</p>
            </div>
            <div class="process-step">
                <h4 class="process-title">Plan smart 📋</h4>
                <p class="process-description">Strategy before execution</p>
            </div>
            <div class="process-step">
                <h4 class="process-title">Design with purpose 🎨</h4>
                <p class="process-description">Beautiful and functional</p>
            </div>
            <div class="process-step">
                <h4 class="process-title">Build with care 🛠️</h4>
                <p class="process-description">Quality in every detail</p>
            </div>
            <div class="process-step">
                <h4 class="process-title">Launch with confidence 🚀</h4>
                <p class="process-description">Ready for the world</p>
            </div>
            <div class="process-step">
                <h4 class="process-title">Support you always ❤️</h4>
                <p class="process-description">We're here for you</p>
            </div>
        </div>
    </div>
</section>

<!-- Trust Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Why Choose Us</span>
            <h2 class="section-title">Find a Team You Can Rely On</h2>
            <p class="section-description">
                Every day, we build trust through communication, transparency, & results.
            </p>
        </div>
        
        <div class="grid grid-3 stagger-children">
            <div class="service-card text-center" style="padding: var(--space-10);">
                <div style="font-size: 3rem; margin-bottom: var(--space-4);">🔍</div>
                <h3 style="font-size: var(--font-size-2xl); margin-bottom: var(--space-4); color: var(--color-black);">Transparency</h3>
                <p style="font-size: var(--font-size-lg); line-height: 1.7; color: var(--color-gray-700);">Transparency is our promise, ensuring open communication and clear expectations every step of the way.</p>
            </div>
            <div class="service-card text-center" style="padding: var(--space-10);">
                <div style="font-size: 3rem; margin-bottom: var(--space-4);">👨‍💻</div>
                <h3 style="font-size: var(--font-size-2xl); margin-bottom: var(--space-4); color: var(--color-black);">Experienced Team</h3>
                <p style="font-size: var(--font-size-lg); line-height: 1.7; color: var(--color-gray-700);">Our experienced team brings a wealth of expertise and knowledge to deliver exceptional results for every project.</p>
            </div>
            <div class="service-card text-center" style="padding: var(--space-10);">
                <div style="font-size: 3rem; margin-bottom: var(--space-4);">✅</div>
                <h3 style="font-size: var(--font-size-2xl); margin-bottom: var(--space-4); color: var(--color-black);">Result Guarantee</h3>
                <p style="font-size: var(--font-size-lg); line-height: 1.7; color: var(--color-gray-700);">With our result guarantee, we ensure you receive the outcomes you expect, backed by our commitment to your success.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section cta-section" style="background: var(--color-black); color: var(--color-white);">
    <div class="container">
        <div class="text-center" style="max-width: 700px; margin: 0 auto;">
            <h2 class="section-title" style="color: var(--color-white);">Ready to Work Together?</h2>
            <p class="section-description" style="color: var(--color-gray-400);" >
                Let's discuss your project and create something amazing together.
            </p>
            <div class="hero-buttons mt-8">
                <a href="<?= e(SITE_URL) ?>/contact" class="btn btn-white btn-lg">Contact Us</a>
                <a href="<?= e(SITE_URL) ?>/portfolio" class="btn btn-secondary btn-lg" style="border-color: var(--color-white); color: var(--color-white);">View Our Work</a>
            </div>
        </div>
    </div>
</section>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>
