<?php
/**
 * Evolvcode CMS - Contact Page
 */

require_once __DIR__ . '/includes/config.php';

// Page meta
$pageTitle = 'Contact Us';
$pageDescription = 'Reach out for expert digital marketing solutions. Our team ensures your website is optimized for success. Send us a message now!';
$pageKeywords = 'contact, get in touch, digital marketing, web development';
$bodyClass = 'contact-page';



require_once INCLUDES_PATH . '/header.php';
?>

<!-- Page Header -->
<section class="section page-header" style="padding-top: 150px; background: var(--color-gray-50);">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Get In Touch</span>
            <h1 class="section-title">Contact Us</h1>
            <p class="section-description lead">
                Ready to start your project? Have a question? We're here to help!
            </p>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="section">
    <div class="container">
        <div class="contact-section">
            <!-- Contact Info -->
            <div class="slide-left">
                <h2 style="margin-bottom: var(--space-4);">Let's Talk</h2>
                <p style="color: var(--color-gray-600); margin-bottom: var(--space-8);">
                    Get in touch with us through any of these channels. We typically respond within 24 hours.
                </p>
                
                <div class="contact-info-list">
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-info-content">
                            <h4>Email</h4>
                            <a href="mailto:<?= e(getSetting('contact_email', 'sales@evolvcode.com')) ?>">
                                <?= e(getSetting('contact_email', 'sales@evolvcode.com')) ?>
                            </a>
                        </div>
                    </div>
                    
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-info-content">
                            <h4>Phone</h4>
                            <a href="tel:<?= e(getSetting('contact_phone', '+91-9229045881')) ?>">
                                <?= e(getSetting('contact_phone', '+91-9229045881')) ?>
                            </a>
                        </div>
                    </div>
                    
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div class="contact-info-content">
                            <h4>WhatsApp</h4>
                            <a href="https://api.whatsapp.com/send/?phone=<?= e(getSetting('whatsapp_number', '919229045881')) ?>" 
                               target="_blank" rel="noopener">
                                Chat with us
                            </a>
                        </div>
                    </div>
                    
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-info-content">
                            <h4>Location</h4>
                            <p><?= e(getSetting('address', 'Patna, Bihar, India')) ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Social Links -->
                <div style="margin-top: var(--space-8);">
                    <h4 style="margin-bottom: var(--space-4);">Follow Us</h4>
                    <div style="display: flex; gap: var(--space-3);">
                        <?php if ($facebook = getSetting('facebook_url')): ?>
                        <a href="<?= e($facebook) ?>" target="_blank" rel="noopener"
                           style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; background: var(--color-gray-100); border-radius: var(--radius-full); transition: all var(--transition-fast);">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <?php endif; ?>
                        <?php if ($twitter = getSetting('twitter_url')): ?>
                        <a href="<?= e($twitter) ?>" target="_blank" rel="noopener"
                           style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; background: var(--color-gray-100); border-radius: var(--radius-full); transition: all var(--transition-fast);">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <?php endif; ?>
                        <?php if ($linkedin = getSetting('linkedin_url')): ?>
                        <a href="<?= e($linkedin) ?>" target="_blank" rel="noopener"
                           style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; background: var(--color-gray-100); border-radius: var(--radius-full); transition: all var(--transition-fast);">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <?php endif; ?>
                        <?php if ($instagram = getSetting('instagram_url')): ?>
                        <a href="<?= e($instagram) ?>" target="_blank" rel="noopener"
                           style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; background: var(--color-gray-100); border-radius: var(--radius-full); transition: all var(--transition-fast);">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="slide-right">
                <div style="background: var(--color-gray-50); padding: var(--space-8); border-radius: var(--radius-xl);">
                    <h3 style="margin-bottom: var(--space-6);">Send us a Message</h3>
                    

                    

                    
                    <form method="POST" action="submit-contact.php" id="contact-form">
                        <?= Security::csrfField() ?>
                        
                        <div class="form-group">
                            <label for="name" class="form-label">Your Name *</label>
                            <input type="text" name="name" id="name" class="form-control" 
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" name="email" id="email" class="form-control" 
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" name="phone" id="phone" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" name="subject" id="subject" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <label for="message" class="form-label">Your Message *</label>
                            <textarea name="message" id="message" class="form-control" rows="5" required></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>
