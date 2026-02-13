<?php
/**
 * Evolvcode CMS - FAQ Page Template (Static)
 */

require_once __DIR__ . '/includes/config.php';

// Static Page Data
$pageTitle = 'Frequency Asked Questions';
$pageDescription = 'Find answers to common questions about Evolvcode services.';
$pageKeywords = 'faq, questions, digital marketing, web development';
$bodyClass = 'page-faq';

// Page Header Content
$headerTitle = 'Common Questions';
$headerSubtitle = 'Find answers to common questions about Evolvcode services. If you cannot find what you are looking for, please contact us.';

require_once INCLUDES_PATH . '/header.php';
?>

<!-- Page Header -->
<section class="section page-header" style="padding-top: 150px; background: var(--color-gray-50);">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Common Questions</span>
            <h1 class="section-title"><?= e($pageTitle) ?></h1>
             <div class="lead" style="max-width: 800px; margin: 0 auto; margin-top: 20px;">
                <p><?= e($headerSubtitle) ?></p>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Accordion -->
<section class="section">
    <div class="container">
        <div class="faq-container">
            <!-- General Questions -->
            <div class="faq-category">
                
                <details class="faq-item">
                    <summary>What services does Evolvcode Solutions Pvt. Ltd. provide?</summary>
                    <div class="faq-content">
                        <p>We provide website development, eCommerce development, custom software development, SEO, digital marketing, lead generation, branding, and graphic design services.</p>
                    </div>
                </details>

                <details class="faq-item">
                    <summary>Which platforms do you work with?</summary>
                    <div class="faq-content">
                        <p>We work with WordPress, Shopify, Webflow, WooCommerce, custom PHP development, and modern frontend technologies like ReactJs/NextJs.</p>
                    </div>
                </details>
                

                <details class="faq-item">
                    <summary>Do you provide custom website development?</summary>
                    <div class="faq-content">
                        <p>Yes, we build fully custom websites based on your business needs, including custom UI/UX, features, integrations, and scalable performance.</p>
                    </div>
                </details>

                <details class="faq-item">
                    <summary>How long does it take to build a website?</summary>
                    <div class="faq-content">
                        <p>Project timelines depend on requirements. A basic website usually takes 7–15 days, while eCommerce and custom software projects may take 4–8 weeks or more according to the custom feature.</p>
                    </div>
                </details>

                <details class="faq-item">
                    <summary>Do you provide SEO services?</summary>
                    <div class="faq-content">
                        <p>Yes, we offer comprehensive SEO services including keyword research, on-page optimization, technical SEO updates, and off-page link building to improve your search engine rankings and drive organic traffic.</p>
                    </div>
                </details>

                <details class="faq-item">
                    <summary>Can you help with Google Ads and lead generation?</summary>
                    <div class="faq-content">
                        <p>Absolutely. We specialize in creating high-converting Google Ads campaigns and lead generation strategies. We focus on targeting the right audience to maximize your ROI and generate quality leads for your business.</p>
                    </div>
                </details>
                
                <details class="faq-item">
                    <summary>Do you provide domain and hosting?</summary>
                    <div class="faq-content">
                        <p>Yes, we can assist you with domain registration and provide reliable hosting solutions tailored to your website's needs, ensuring high performance and security.</p>
                    </div>
                </details>

                <details class="faq-item">
                    <summary>Do you provide website maintenance services?</summary>
                    <div class="faq-content">
                        <p>Yes, we offer ongoing website maintenance packages. This includes regular updates, security checks, backups, and content updates to keep your website running smoothly and securely.</p>
                    </div>
                </details>

                <details class="faq-item">
                    <summary>What information do you need to start a project?</summary>
                    <div class="faq-content">
                        <p>To get started, we typically need details about your business goals, target audience, preferred design style, content (text and images), and any specific functionality requirements. We'll guide you through a simple onboarding process to gather everything needed.</p>
                    </div>
                </details>

                <details class="faq-item">
                    <summary>Do you provide support after project completion?</summary>
                    <div class="faq-content">
                        <p>Yes, we provide post-launch support to ensure everything works as expected. We also offer maintenance plans for long-term support and updates as your business grows.</p>
                    </div>
                </details>

                <details class="faq-item">
                    <summary>How much does a website or software project cost?</summary>
                    <div class="faq-content">
                        <p>The cost varies depending on the complexity and scope of the project. We offer customized quotes based on your specific requirements. Contact us for a free consultation and a detailed estimate.</p>
                    </div>
                </details>

                <details class="faq-item">
                    <summary>Do you provide invoice and GST billing?</summary>
                    <div class="faq-content">
                        <p>Yes, we provide proper GST invoices for all our services, ensuring full compliance and transparency in billing for your business accounting.</p>
                    </div>
                </details>

                <details class="faq-item">
                    <summary>How long does it typically take to build a website?</summary>
                    <div class="faq-content">
                        <p>A standard business website usually takes 3-5 weeks from start to finish, while more complex e-commerce sites or custom applications may take 6-10 weeks. Timelines vary based on the scope and how quickly we receive content from you.</p>
                    </div>
                </details>

                <details class="faq-item">
                    <summary>Can you redesign my existing website?</summary>
                    <div class="faq-content">
                        <p>Yes! We specialize in website redesigns. We can give your site a modern look, improve its performance, and ensure it's mobile-friendly while preserving your existing SEO value.</p>
                    </div>
                </details>

                <details class="faq-item">
                    <summary>Do you build online stores (e-commerce)?</summary>
                    <div class="faq-content">
                        <p>Absolutely. We build robust and secure e-commerce websites using platforms like WooCommerce and Shopify, complete with payment gateway integration, inventory management, and easy-to-use admin panels.</p>
                    </div>
                </details>

                <details class="faq-item">
                    <summary>Which social media platforms do you manage?</summary>
                    <div class="faq-content">
                        <p>We manage all major platforms including Facebook, Instagram, LinkedIn, Twitter (X), and YouTube. We can help identify which platforms are most effective for your specific business goals.</p>
                    </div>
                </details>

                <details class="faq-item">
                    <summary>Will I get reports on my campaign performance?</summary>
                    <div class="faq-content">
                        <p>Transparency is key. We provide detailed monthly reports for SEO and marketing campaigns, showing you exactly how your investment is performing with metrics like traffic, leads, and conversions.</p>
                    </div>
                </details>

                <details class="faq-item">
                    <summary>Will I own the website and code after payment?</summary>
                    <div class="faq-content">
                        <p>Yes. Once the project is fully paid for, you own 100% of your website, design, and code. There are no ongoing licensing fees for the website itself.</p>
                    </div>
                </details>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form Section -->
<section class="section" style="background: var(--color-gray-50);">
    <div class="container">
        <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:20px;">
            <!-- Left Side Content -->
            <div>
                <span class="section-label">Still have questions?</span>
                <h2 style="margin-bottom: 20px;">Get in Touch</h2>
                <p class="lead" style="margin-bottom: 30px; color: var(--color-gray-600);">
                    Can't find the answer you're looking for? Fill out the form and our team will get back to you within 24 hours.
                </p>
                
                <ul class="contact-list" style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 20px;">
                        <a href="mailto:<?= e(getSetting('contact_email')) ?>" style="display: flex; align-items: center; gap: 15px; font-weight: 500; font-size: 1.1rem; color: var(--color-gray-900);">
                            <div style="width: 40px; height: 40px; background: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-sm);">
                                <i class="fas fa-envelope" style="color: var(--color-primary);"></i>
                            </div>
                            <?= e(getSetting('contact_email')) ?>
                        </a>
                    </li>
                    <li style="margin-bottom: 20px;">
                        <a href="tel:<?= e(getSetting('contact_phone')) ?>" style="display: flex; align-items: center; gap: 15px; font-weight: 500; font-size: 1.1rem; color: var(--color-gray-900);">
                            <div style="width: 40px; height: 40px; background: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-sm);">
                                <i class="fas fa-phone" style="color: var(--color-primary);"></i>
                            </div>
                            <?= e(getSetting('contact_phone')) ?>
                        </a>
                    </li>
                     <li>
                        <a href="https://api.whatsapp.com/send/?phone=<?= e(getSetting('whatsapp_number', '919229045881')) ?>" target="_blank" style="display: flex; align-items: center; gap: 15px; font-weight: 500; font-size: 1.1rem; color: var(--color-gray-900);">
                            <div style="width: 40px; height: 40px; background: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-sm);">
                                <i class="fab fa-whatsapp" style="color: var(--color-primary);"></i>
                            </div>
                            Chat with us
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Right Side Form -->
            <div class="card" style="padding: 40px; background: #fff; border-radius: var(--radius-xl); ">
                <h3 style="margin-bottom: 25px;">Send us a Message</h3>
                <form method="POST" action="submit-contact.php" id="faq-contact-form">
                    <?= Security::csrfField() ?>
                    <input type="hidden" name="subject" value="Inquiry from FAQ Page">
                    
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="name" class="form-label" style="display: block; margin-bottom: 8px; font-weight: 500;">Your Name *</label>
                        <input type="text" name="name" id="name" class="form-control" style="width: 100%; padding: 12px; border: 1px solid var(--color-gray-200); border-radius: var(--radius-md); transition: border-color 0.2s;" required>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="email" class="form-label" style="display: block; margin-bottom: 8px; font-weight: 500;">Email Address *</label>
                        <input type="email" name="email" id="email" class="form-control" style="width: 100%; padding: 12px; border: 1px solid var(--color-gray-200); border-radius: var(--radius-md); transition: border-color 0.2s;" required>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="phone" class="form-label" style="display: block; margin-bottom: 8px; font-weight: 500;">Phone Number</label>
                        <input type="tel" name="phone" id="phone" class="form-control" style="width: 100%; padding: 12px; border: 1px solid var(--color-gray-200); border-radius: var(--radius-md); transition: border-color 0.2s;">
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label for="message" class="form-label" style="display: block; margin-bottom: 8px; font-weight: 500;">Your Message *</label>
                        <textarea name="message" id="message" class="form-control" rows="5" style="width: 100%; padding: 12px; border: 1px solid var(--color-gray-200); border-radius: var(--radius-md); transition: border-color 0.2s;" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-weight: 600; font-size: 1.05rem;">
                        <i class="fas fa-paper-plane" style="margin-right: 8px;"></i> Send Message
                    </button>
                    <div id="form-message" style="margin-top: 15px;"></div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Inline script for contact form (simple handler) -->
<script>
// Simple form handler
document.getElementById('faq-contact-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const btn = form.querySelector('button[type="submit"]');
    const msg = document.getElementById('form-message');
    
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right: 8px;"></i> Sending...';
    
    const formData = new FormData(form);
    
    fetch('submit-contact.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        msg.innerHTML = `<div class="alert ${data.success ? 'alert-success' : 'alert-error'}" style="margin-top: 15px; padding: 15px; border-radius: var(--radius-md); background: ${data.success ? '#dcfce7' : '#fee2e2'}; color: ${data.success ? '#166534' : '#991b1b'}; display: flex; align-items: center; gap: 10px;"><i class="fas ${data.success ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i> ${data.message}</div>`;
        if (data.success) form.reset();
    })
    .catch(error => {
        msg.innerHTML = '<div class="alert alert-error" style="margin-top: 15px; padding: 15px; border-radius: var(--radius-md); background: #fee2e2; color: #991b1b; display: flex; align-items: center; gap: 10px;"><i class="fas fa-exclamation-circle"></i> Something went wrong. Please try again.</div>';
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
});
</script>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>
