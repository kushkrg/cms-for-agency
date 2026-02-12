    </main>
    
    <!-- Footer -->
    <footer class="site-footer">
        <!-- Newsletter Bar -->
        <div class="footer-newsletter-bar">
            <div class="container">
                <div class="newsletter-content">
                    <div class="newsletter-text">
                        <h3><span>Subscribe to our</span> NEWSLETTER</h3>
                    </div>
                    <div class="newsletter-form-wrap">
                        <form class="newsletter-form" id="newsletter-form">
                            <input type="email" name="email" class="newsletter-input" placeholder="Enter email address" required>
                            <button type="submit" class="newsletter-btn">Submit</button>
                        </form>
                        <div class="newsletter-msg" id="newsletter-msg"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Footer -->
        <div class="footer-main">
            <div class="container">
                <div class="footer-grid-new">
                    <!-- Col 1: Information -->
                    <div class="footer-col-new">
                        <h4>Information</h4>
                        <ul class="footer-links-new">
                            <li><a href="<?= e(SITE_URL) ?>/about">Our Company</a></li>
                            <li><a href="<?= e(SITE_URL) ?>/services">Services</a></li>
                            <li><a href="<?= e(SITE_URL) ?>/portfolio">Portfolio</a></li>
                            <li><a href="<?= e(SITE_URL) ?>/contact">Contact Us</a></li>
                            <li><a href="<?= e(SITE_URL) ?>/blog">Blog</a></li>
                        </ul>
                    </div>

                    <!-- Col 2: Services (Dynamic) -->
                    <div class="footer-col-new">
                        <h4>Services</h4>
                        <ul class="footer-links-new">
                            <?php foreach (getServices(5) as $service): ?>
                            <li><a href="<?= e(SITE_URL) ?>/service/<?= e($service['slug']) ?>"><?= e($service['title']) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Col 3: Quick Links (Dynamic Footer Menu) -->
                    <div class="footer-col-new">
                        <h4>Quick Links</h4>
                        <ul class="footer-links-new">
                             <?php foreach (getMenu('footer') as $item): ?>
                            <li>
                                <a href="<?= strpos($item['url'], 'http') === 0 ? e($item['url']) : e(SITE_URL . $item['url']) ?>"
                                   target="<?= e($item['target']) ?>">
                                    <?= e($item['title']) ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <!-- Col 4: Contact/Branding -->
                     <div class="footer-col-new">
                        <h4>Contact</h4>
                        <ul class="footer-links-new">
                            <li>
                                <a href="#"><?= e(getSetting('address', 'Patna, Bihar, India')) ?></a>
                            </li>
                            <li>
                                <a href="tel:<?= e(getSetting('contact_phone')) ?>"><?= e(getSetting('contact_phone')) ?></a>
                            </li>
                            <li>
                                <a href="mailto:<?= e(getSetting('contact_email')) ?>"><?= e(getSetting('contact_email')) ?></a>
                            </li>
                             <li>
                                <a href="https://api.whatsapp.com/send/?phone=<?= e(getSetting('whatsapp_number')) ?>" target="_blank">Chat on WhatsApp</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="footer-bottom-new">
            <div class="container">
                <p class="copyright-text">
                    <?= e(getSetting('footer_text', 'Copyright © ' . date('Y') . ' All rights reserved')) ?> | Made with <i class="fas fa-heart"></i> by <a href="<?= e(SITE_URL) ?>" target="_blank">Evolvcode</a>
                </p>
            </div>
        </div>
    </footer>
    
    <!-- Back to Top Button -->
    <button class="back-to-top" id="back-to-top" aria-label="Back to top">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <!-- Contact Popup Modal -->
    <div id="contact-popup" class="popup-overlay">
        <div class="popup-modal">
            <button class="popup-close" id="popup-close" aria-label="Close">&times;</button>
            <div class="popup-header">
                <h3>Get a Free Consultation</h3>
                <p>Fill out the form below and we'll get back to you within 24 hours.</p>
            </div>
            <?php 
            require_once __DIR__ . '/FormRenderer.php';
            echo FormRenderer::render('contact');
            ?>
        </div>
    </div>
    
    <!-- Main JavaScript -->
    <script src="<?= e(ASSETS_URL) ?>/js/main.js?v=<?= time() ?>" defer></script>
    
    <!-- Custom JS (Body) -->
    <?php if ($customJsBody = getSetting('custom_js_body')): ?>
    <?= $customJsBody ?>
    <?php endif; ?>
</body>
</html>
