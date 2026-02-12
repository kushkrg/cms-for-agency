/**
 * Evolvcode CMS - Main JavaScript
 * GSAP Animations & Interactions
 */

document.addEventListener('DOMContentLoaded', () => {
    // Initialize all modules
    // initPreloader();
    initHeader();
    initMobileMenu();
    initBackToTop();
    initAnimations();
    initContactForm();
    initContactPopup();
    initLazyLoading();
    initPortfolioFilters();
    initCareerForm();
    initNewsletterForm();
});

/**
 * Get reCAPTCHA Token
 */
function getRecaptchaToken(action) {
    return new Promise((resolve) => {
        if (window.grecaptcha && window.recaptchaSiteKey) {
            grecaptcha.ready(() => {
                grecaptcha.execute(window.recaptchaSiteKey, { action: action })
                    .then(token => resolve(token))
                    .catch(err => {
                        console.error('reCAPTCHA error:', err);
                        resolve('');
                    });
            });
        } else {
            resolve('');
        }
    });
}

/**
 * Career Form Handling
 */
function initCareerForm() {
    const form = document.querySelector('.career-form');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

        try {
            const formData = new FormData(form);
            
            // Get reCAPTCHA token if enabled
            const token = await getRecaptchaToken('career_submit');
            if (token) {
                formData.append('recaptcha_token', token);
            }

            // Since career form is a standard POST, we need to create inputs dynamically if using fetch isn't an option
            // or just use fetch and reload. But career.php expects a POST request and renders HTML.
            // Let's use fetch and handle the response message, or convert it to AJAX.
            // Given the existing code structure in career.php, it's a standard PHP form submission.
            // To support reCAPTCHA, we must intercept, get token, add to form, then submit.
            
            if (token) {
                let tokenInput = form.querySelector('input[name="recaptcha_token"]');
                if (!tokenInput) {
                    tokenInput = document.createElement('input');
                    tokenInput.type = 'hidden';
                    tokenInput.name = 'recaptcha_token';
                    form.appendChild(tokenInput);
                }
                tokenInput.value = token;
            }
            
            form.submit();

        } catch (error) {
            console.error('Career form error:', error);
            alert('An error occurred. Please try again.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
}
// Existing code...
/**
 * Contact form handling
 */
function initContactForm() {
    const form = document.getElementById('contact-form');
    if (!form) return;
    
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Disable button and show loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        
        try {
            const formData = new FormData(form);
            
            // Get reCAPTCHA token
            const token = await getRecaptchaToken('contact_submit');
            if (token) {
                formData.append('recaptcha_token', token);
            }
            
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Show success message
                showNotification('success', result.message || 'Message sent successfully!');
                form.reset();
            } else {
                showNotification('error', result.message || 'Failed to send message. Please try again.');
            }
        } catch (error) {
            showNotification('error', 'An error occurred. Please try again later.');
            console.error('Form submission error:', error);
        } finally {
            // Re-enable button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
}

// ... initContactPopup ...

/**
 * Initialize dynamic forms handling
 */
function initDynamicForms() {
    const forms = document.querySelectorAll('form.dynamic-form');
    
    forms.forEach(form => {
        // Prevent double initialization
        if (form.dataset.initialized) return;
        form.dataset.initialized = 'true';
        
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const submitBtn = form.querySelector('button[type="submit"]');
            const btnText = submitBtn.querySelector('.btn-text');
            const btnLoading = submitBtn.querySelector('.btn-loading');
            const formMessage = form.querySelector('.form-message');
            
            // Show loading state
            submitBtn.disabled = true;
            if (btnText) btnText.style.display = 'none';
            if (btnLoading) btnLoading.style.display = 'inline';
            
            // Reset message
            if (formMessage) {
                formMessage.className = 'form-message';
                formMessage.textContent = '';
            }
            
            try {
                const formData = new FormData(form);
                
                // Get reCAPTCHA token
                const token = await getRecaptchaToken('dynamic_form_submit');
                if (token) {
                    formData.append('recaptcha_token', token);
                }
                
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    if (formMessage) {
                        formMessage.className = 'form-message success';
                        formMessage.textContent = result.message;
                    }
                    form.reset();
                    
                    // If it's the popup contact form, close it after delay
                    if (form.closest('#contact-popup')) {
                        setTimeout(() => {
                            const popup = document.getElementById('contact-popup');
                            if (popup) popup.classList.remove('active');
                            document.body.style.overflow = '';
                        }, 3000);
                    }
                    
                    // Handle redirect
                    if (result.redirect) {
                        window.location.href = result.redirect;
                    }
                } else {
                    if (formMessage) {
                        formMessage.className = 'form-message error';
                        formMessage.textContent = result.message;
                    }
                }
            } catch (error) {
                if (formMessage) {
                    formMessage.className = 'form-message error';
                    formMessage.textContent = 'An error occurred. Please try again later.';
                }
                console.error('Form submission error:', error);
            } finally {
                submitBtn.disabled = false;
                if (btnText) btnText.style.display = 'inline';
                if (btnLoading) btnLoading.style.display = 'none';
            }
        });
    });
}

/**
 * Page load animations
 */
function animatePageLoad() {
    // Animate hero content
    const heroContent = document.querySelector('.hero-content');
    if (heroContent) {
        gsap.from(heroContent.children, {
            y: 30,
            opacity: 0,
            duration: 0.8,
            stagger: 0.15,
            ease: 'power2.out'
        });
    }
}

/**
 * Header scroll behavior
 */
function initHeader() {
    const header = document.getElementById('header');
    if (!header) return;
    
    let lastScroll = 0;
    
    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;
        
        // Add scrolled class
        if (currentScroll > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
        
        lastScroll = currentScroll;
    });
}

/**
 * Mobile menu toggle
 */
function initMobileMenu() {
    const toggle = document.getElementById('mobile-menu-toggle');
    const menu = document.getElementById('nav-menu');
    
    if (!toggle || !menu) return;
    
    toggle.addEventListener('click', () => {
        toggle.classList.toggle('active');
        menu.classList.toggle('active');
        document.body.classList.toggle('menu-open');
    });
    
    // Close menu when clicking on a link
    const menuLinks = menu.querySelectorAll('.nav-link');
    menuLinks.forEach(link => {
        link.addEventListener('click', () => {
            toggle.classList.remove('active');
            menu.classList.remove('active');
            document.body.classList.remove('menu-open');
        });
    });
    
    // Close menu on window resize
    window.addEventListener('resize', () => {
        if (window.innerWidth > 1024) {
            toggle.classList.remove('active');
            menu.classList.remove('active');
            document.body.classList.remove('menu-open');
        }
    });
}

/**
 * Back to top button
 */
function initBackToTop() {
    const button = document.getElementById('back-to-top');
    if (!button) return;
    
    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
            button.classList.add('visible');
        } else {
            button.classList.remove('visible');
        }
    });
    
    button.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

/**
 * GSAP Scroll Animations
 */
function initAnimations() {
    // Check if GSAP and ScrollTrigger are loaded
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
        console.warn('GSAP or ScrollTrigger not loaded');
        // Show all elements if GSAP not available
        document.querySelectorAll('.fade-up, .fade-in, .scale-in, .slide-left, .slide-right').forEach(el => {
            el.style.opacity = '1';
            el.style.transform = 'none';
        });
        return;
    }
    
    gsap.registerPlugin(ScrollTrigger);
    
    // Fade up animation
    gsap.utils.toArray('.fade-up').forEach(element => {
        gsap.from(element, {
            y: 30,
            opacity: 0,
            duration: 0.8,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: element,
                start: 'top 85%',
                toggleActions: 'play none none none'
            }
        });
    });
    
    // Fade in animation
    gsap.utils.toArray('.fade-in').forEach(element => {
        gsap.from(element, {
            opacity: 0,
            duration: 0.8,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: element,
                start: 'top 85%',
                toggleActions: 'play none none none'
            }
        });
    });
    
    // Scale in animation
    gsap.utils.toArray('.scale-in').forEach(element => {
        gsap.from(element, {
            scale: 0.9,
            opacity: 0,
            duration: 0.8,
            ease: 'back.out(1.7)',
            scrollTrigger: {
                trigger: element,
                start: 'top 85%',
                toggleActions: 'play none none none'
            }
        });
    });
    
    // Slide left animation
    gsap.utils.toArray('.slide-left').forEach(element => {
        gsap.from(element, {
            x: -30,
            opacity: 0,
            duration: 0.8,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: element,
                start: 'top 85%',
                toggleActions: 'play none none none'
            }
        });
    });
    
    // Slide right animation
    gsap.utils.toArray('.slide-right').forEach(element => {
        gsap.from(element, {
            x: 30,
            opacity: 0,
            duration: 0.8,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: element,
                start: 'top 85%',
                toggleActions: 'play none none none'
            }
        });
    });
    
    // Stagger children animation
    // Stagger children animation
    gsap.utils.toArray('.stagger-children').forEach(container => {
        gsap.from(container.children, {
            y: 20,
            // opacity removed to ensure visibility
            duration: 0.6,
            stagger: 0.1,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: container,
                start: 'top 85%',
                toggleActions: 'play none none none'
            }
        });
    });
    
    // Animate section headers
    gsap.utils.toArray('.section-header').forEach(header => {
        gsap.from(header, {
            y: 30,
            opacity: 0,
            duration: 0.8,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: header,
                start: 'top 85%',
                toggleActions: 'play none none none'
            }
        });
    });
    
    // Animate cards with stagger
    const cardGrids = document.querySelectorAll('.grid, .portfolio-grid, .blog-grid');
    cardGrids.forEach(grid => {
        const cards = grid.querySelectorAll('.card, .service-card, .project-card, .post-card, .blog-card');
        if (cards.length > 0) {
            gsap.from(cards, {
                y: 40,
                // opacity removed to ensure visibility
                duration: 0.6,
                stagger: 0.1,
                ease: 'power2.out',
                scrollTrigger: {
                    trigger: grid,
                    start: 'top 80%',
                    toggleActions: 'play none none none'
                }
            });
        }
    });
    
    // Animate process steps
    const processSteps = document.querySelectorAll('.process-step');
    if (processSteps.length > 0) {
        gsap.from(processSteps, {
            y: 30,
            // opacity removed to ensure visibility
            duration: 0.6,
            stagger: 0.15,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: '.process-grid',
                start: 'top 80%',
                toggleActions: 'play none none none'
            }
        });
    }
}

/**
 * Contact form handling
 */
function initContactForm() {
    const form = document.getElementById('contact-form');
    if (!form) return;
    
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Disable button and show loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        
        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Show success message
                showNotification('success', result.message || 'Message sent successfully!');
                form.reset();
            } else {
                showNotification('error', result.message || 'Failed to send message. Please try again.');
            }
        } catch (error) {
            showNotification('error', 'An error occurred. Please try again later.');
            console.error('Form submission error:', error);
        } finally {
            // Re-enable button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
}

/**
 * Contact popup handling
 */
function initContactPopup() {
    const popup = document.getElementById('contact-popup');
    const closeBtn = document.getElementById('popup-close');
    const openBtns = document.querySelectorAll('.open-contact-popup');
    
    // Find generic dynamic form inside popup
    const form = popup ? popup.querySelector('form.dynamic-form') : null;
    
    if (!popup) return;
    
    // Open popup
    function openPopup(service = '') {
        popup.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        // Pre-select service if provided
        if (service && form) {
            // Try to find subject/service field by name
            const serviceSelect = form.querySelector('select[name="subject"]') || form.querySelector('select[name="service"]');
            if (serviceSelect) {
                for (let option of serviceSelect.options) {
                    // Check value or text content match
                    if (option.value === service || option.text === service) {
                        option.selected = true;
                        break;
                    }
                }
            }
            
            // Update hidden source field if exists
            const sourceInput = form.querySelector('input[name="source"]');
            if (sourceInput) {
                sourceInput.value = 'Service Page: ' + service;
            }
        }
        
        // Focus first input
        setTimeout(() => {
            if (form) {
                const firstInput = form.querySelector('input:not([type="hidden"]), select, textarea');
                if (firstInput) firstInput.focus();
            }
        }, 300);
    }
    
    // Close popup
    function closePopup() {
        popup.classList.remove('active');
        document.body.style.overflow = '';
        
        // Reset form message
        if (form) {
            const formMessage = form.querySelector('.form-message');
            if (formMessage) {
                formMessage.className = 'form-message';
                formMessage.textContent = '';
            }
        }
    }
    
    // Open button clicks
    openBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const service = btn.dataset.service || '';
            openPopup(service);
        });
    });
    
    // Close button
    if (closeBtn) {
        closeBtn.addEventListener('click', closePopup);
    }
    
    // Close on overlay click
    popup.addEventListener('click', (e) => {
        if (e.target === popup) {
            closePopup();
        }
    });
    
    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && popup.classList.contains('active')) {
            closePopup();
        }
    });
    
    // Initialize all dynamic forms (including popup form)
    initDynamicForms();
}

/**
 * Initialize dynamic forms handling
 */
function initDynamicForms() {
    const forms = document.querySelectorAll('form.dynamic-form');
    
    forms.forEach(form => {
        // Prevent double initialization
        if (form.dataset.initialized) return;
        form.dataset.initialized = 'true';
        
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const submitBtn = form.querySelector('button[type="submit"]');
            const btnText = submitBtn.querySelector('.btn-text');
            const btnLoading = submitBtn.querySelector('.btn-loading');
            const formMessage = form.querySelector('.form-message');
            
            // Show loading state
            submitBtn.disabled = true;
            if (btnText) btnText.style.display = 'none';
            if (btnLoading) btnLoading.style.display = 'inline';
            
            // Reset message
            if (formMessage) {
                formMessage.className = 'form-message';
                formMessage.textContent = '';
            }
            
            try {
                const formData = new FormData(form);
                
                // Send the current page title for source tracking
                formData.append('source_page', document.title);
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    if (formMessage) {
                        formMessage.className = 'form-message success';
                        formMessage.textContent = result.message;
                    }
                    form.reset();
                    
                    // If it's the popup contact form, close it after delay
                    if (form.closest('#contact-popup')) {
                        setTimeout(() => {
                            const popup = document.getElementById('contact-popup');
                            if (popup) popup.classList.remove('active');
                            document.body.style.overflow = '';
                        }, 3000);
                    }
                    
                    // Handle redirect
                    if (result.redirect) {
                        window.location.href = result.redirect;
                    }
                } else {
                    if (formMessage) {
                        formMessage.className = 'form-message error';
                        formMessage.textContent = result.message;
                    }
                }
            } catch (error) {
                if (formMessage) {
                    formMessage.className = 'form-message error';
                    formMessage.textContent = 'An error occurred. Please try again later.';
                }
                console.error('Form submission error:', error);
            } finally {
                submitBtn.disabled = false;
                if (btnText) btnText.style.display = 'inline';
                if (btnLoading) btnLoading.style.display = 'none';
            }
        });
    });
}

/**
 * Show notification
 */
function showNotification(type, message) {
    // Remove existing notification
    const existing = document.querySelector('.notification');
    if (existing) existing.remove();
    
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <span>${message}</span>
        <button class="notification-close">&times;</button>
    `;
    
    // Add styles if not exists
    if (!document.getElementById('notification-styles')) {
        const styles = document.createElement('style');
        styles.id = 'notification-styles';
        styles.textContent = `
            .notification {
                position: fixed;
                top: 100px;
                right: 20px;
                padding: 16px 24px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                gap: 16px;
                box-shadow: 0 10px 25px rgba(0,0,0,0.1);
                z-index: 9999;
                animation: slideIn 0.3s ease;
            }
            .notification-success {
                background: #E8F5E9;
                color: #2E7D32;
            }
            .notification-error {
                background: #FFEBEE;
                color: #C62828;
            }
            .notification-close {
                background: none;
                border: none;
                font-size: 20px;
                cursor: pointer;
                opacity: 0.7;
            }
            .notification-close:hover {
                opacity: 1;
            }
            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        `;
        document.head.appendChild(styles);
    }
    
    document.body.appendChild(notification);
    
    // Close button
    notification.querySelector('.notification-close').addEventListener('click', () => {
        notification.remove();
    });
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

/**
 * Lazy loading images
 */
function initLazyLoading() {
    const lazyImages = document.querySelectorAll('img[data-src]');
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        lazyImages.forEach(img => {
            imageObserver.observe(img);
        });
    } else {
        // Fallback
        lazyImages.forEach(img => {
            img.src = img.dataset.src;
        });
    }
}

/**
 * Newsletter Form Handling
 */
function initNewsletterForm() {
    const form = document.getElementById('newsletter-form');
    if (!form) return;

    const msgEl = document.getElementById('newsletter-msg');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const btn = form.querySelector('button');
        const originalText = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = 'Subscribing...';
        if (msgEl) { msgEl.textContent = ''; msgEl.className = 'newsletter-msg'; }
        
        try {
            const formData = new FormData(form);
            const response = await fetch('/ajax/subscribe.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                if (msgEl) {
                    msgEl.textContent = result.message || 'Thank you for subscribing!';
                    msgEl.className = 'newsletter-msg success';
                }
                form.reset();
                setTimeout(() => { if (msgEl) { msgEl.textContent = ''; msgEl.className = 'newsletter-msg'; } }, 5000);
            } else {
                if (msgEl) {
                    msgEl.textContent = result.message || 'Something went wrong.';
                    msgEl.className = 'newsletter-msg error';
                }
            }
        } catch (error) {
            console.error('Newsletter error:', error);
            if (msgEl) {
                msgEl.textContent = 'An error occurred. Please try again.';
                msgEl.className = 'newsletter-msg error';
            }
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });
}

/**
 * Portfolio filter functionality
 */
function initPortfolioFilters() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const portfolioItems = document.querySelectorAll('.portfolio-grid .blog-card');
    
    if (filterBtns.length === 0 || portfolioItems.length === 0) return;
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Update active button
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            const filter = btn.dataset.filter;
            
            // Filter items with animation
            portfolioItems.forEach(item => {
                const category = item.dataset.category;
                const shouldShow = filter === 'all' || category === filter;
                
                if (shouldShow) {
                    gsap.to(item, {
                        scale: 1,
                        opacity: 1,
                        duration: 0.4,
                        ease: 'power2.out',
                        onStart: () => {
                            item.style.display = 'block';
                        }
                    });
                } else {
                    gsap.to(item, {
                        scale: 0.8,
                        opacity: 0,
                        duration: 0.4,
                        ease: 'power2.in',
                        onComplete: () => {
                            item.style.display = 'none';
                        }
                    });
                }
            });
        });
    });
}

/**
 * Smooth scroll for anchor links
 */
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        if (href === '#') return;
        
        const target = document.querySelector(href);
        if (target) {
            e.preventDefault();
            const headerOffset = 100;
            const elementPosition = target.getBoundingClientRect().top;
            const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
            
            window.scrollTo({
                top: offsetPosition,
                behavior: 'smooth'
            });
        }
    });
});
