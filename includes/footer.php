    </main>
    
    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <!-- Company Info -->
                <div class="footer-col footer-about">
                    <a href="<?= e(SITE_URL) ?>" class="footer-logo">
                        <span class="logo-text">EVOLVCODE</span>
                    </a>
                    <p class="footer-description">
                        We're a team of creative minds, tech experts, and marketing pros who love helping businesses shine online.
                    </p>
                    <div class="footer-social">
                        <?php if ($facebook = getSetting('facebook_url')): ?>
                        <a href="<?= e($facebook) ?>" target="_blank" rel="noopener" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <?php endif; ?>
                        <?php if ($twitter = getSetting('twitter_url')): ?>
                        <a href="<?= e($twitter) ?>" target="_blank" rel="noopener" aria-label="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <?php endif; ?>
                        <?php if ($linkedin = getSetting('linkedin_url')): ?>
                        <a href="<?= e($linkedin) ?>" target="_blank" rel="noopener" aria-label="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <?php endif; ?>
                        <?php if ($instagram = getSetting('instagram_url')): ?>
                        <a href="<?= e($instagram) ?>" target="_blank" rel="noopener" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="footer-col">
                    <h4 class="footer-title">Quick Links</h4>
                    <ul class="footer-links">
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
                
                <!-- Services -->
                <div class="footer-col">
                    <h4 class="footer-title">Our Services</h4>
                    <ul class="footer-links">
                        <?php foreach (getServices(6) as $service): ?>
                        <li>
                            <a href="<?= e(SITE_URL) ?>/service/<?= e($service['slug']) ?>">
                                <?= e($service['title']) ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div class="footer-col footer-contact">
                    <h4 class="footer-title">Contact Us</h4>
                    <ul class="contact-list">
                        <li>
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:<?= e(getSetting('contact_email', 'sales@evolvcode.com')) ?>">
                                <?= e(getSetting('contact_email', 'sales@evolvcode.com')) ?>
                            </a>
                        </li>
                        <li>
                            <i class="fas fa-phone"></i>
                            <a href="tel:<?= e(getSetting('contact_phone', '+91-9229045881')) ?>">
                                <?= e(getSetting('contact_phone', '+91-9229045881')) ?>
                            </a>
                        </li>
                        <li>
                            <i class="fab fa-whatsapp"></i>
                            <a href="https://api.whatsapp.com/send/?phone=<?= e(getSetting('whatsapp_number', '919229045881')) ?>" 
                               target="_blank" rel="noopener">
                                Chat on WhatsApp
                            </a>
                        </li>
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?= e(getSetting('address', 'Patna, Bihar, India')) ?></span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Copyright -->
            <div class="footer-bottom">
                <p class="copyright">
                    <?= e(getSetting('footer_text', '© ' . date('Y') . ' Evolvcode Solutions. All rights reserved.')) ?>
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
