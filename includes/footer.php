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
                    <!-- Col 1: Branding and Description -->
                    <div class="footer-col-new">
                        <div class="footer-branding">
                            <a href="<?= e(SITE_URL) ?>" class="footer-logo">
                                <?php if ($whiteLogo = getSetting('logo_white_path')): ?>
                                <img src="<?= e(SITE_URL . $whiteLogo) ?>" alt="<?= e(getSetting('site_name')) ?>" class="footer-logo-img">
                                <?php else: ?>
                                <h3 class="footer-logo-text"><?= e(getSetting('site_name', 'Evolvcode')) ?></h3>
                                <?php endif; ?>
                            </a>
                            <p class="footer-description">
                                <?= e(getSetting('site_description', 'Building digital experiences that matter. We help businesses grow through innovative web solutions and strategic design.')) ?>
                            </p>
                            
                            <div class="footer-social">
                                <?php if ($fb = getSetting('facebook_url')): ?>
                                <a href="<?= e($fb) ?>" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                                <?php endif; ?>
                                
                                <?php if ($tw = getSetting('twitter_url')): ?>
                                <a href="<?= e($tw) ?>" target="_blank" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                                <?php endif; ?>
                                
                                <?php if ($li = getSetting('linkedin_url')): ?>
                                <a href="<?= e($li) ?>" target="_blank" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                                <?php endif; ?>
                                
                                <?php if ($ig = getSetting('instagram_url')): ?>
                                <a href="<?= e($ig) ?>" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                                <?php endif; ?>
                                
                                <?php if ($yt = getSetting('youtube_url')): ?>
                                <a href="<?= e($yt) ?>" target="_blank" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Col 2: Legal (Pages from Backend) -->
                    <div class="footer-col-new">
                        <h4>Legal</h4>
                        <ul class="footer-links-new">
                            <li><a href="<?= e(SITE_URL) ?>/privacy-policy">Privacy Policy</a></li>
                            <li><a href="<?= e(SITE_URL) ?>/disclaimer">Disclaimer</a></li>
                            <li><a href="<?= e(SITE_URL) ?>/cookies-policy">Cookies Policy</a></li>
                            <li><a href="<?= e(SITE_URL) ?>/terms-and-conditions">Terms & Conditions</a></li>
                            <li><a href="<?= e(SITE_URL) ?>/refund-policy">Refund Policy</a></li>
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
                        <ul class="footer-contact-list">
                            <li>
                                <a href="#">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?= e(getSetting('address', 'Patna, Bihar, India')) ?></span>
                                </a>
                            </li>
                            <li>
                                <a href="tel:<?= e(getSetting('contact_phone')) ?>">
                                    <i class="fas fa-phone-alt"></i>
                                    <span><?= e(getSetting('contact_phone')) ?></span>
                                </a>
                            </li>
                            <li>
                                <a href="mailto:<?= e(getSetting('contact_email')) ?>">
                                    <i class="fas fa-envelope"></i>
                                    <span><?= e(getSetting('contact_email')) ?></span>
                                </a>
                            </li>
                             <li>
                                <a href="https://api.whatsapp.com/send/?phone=<?= e(getSetting('whatsapp_number')) ?>" target="_blank">
                                    <i class="fab fa-whatsapp"></i>
                                    <span>Chat on WhatsApp</span>
                                </a>
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
