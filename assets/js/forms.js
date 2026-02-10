/**
 * Evolvcode CMS - Forms Frontend
 */

const EvolvForm = {
    apiUrl: '/ajax/',

    /**
     * Initialize a form in a container
     */
    init: function(slug, containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;

        // If container is empty, fetch form HTML
        if (!container.innerHTML.trim()) {
            this.loadForm(slug, container, 'embedded');
        }
    },

    /**
     * Open a form in a popup
     */
    openPopup: function(slug) {
        let containerId = 'evolv-form-' + slug + '-popup';
        let container = document.getElementById(containerId);

        if (!container) {
            // Create container
            container = document.createElement('div');
            container.id = containerId;
            container.className = 'evolv-form-popup-wrapper';
            document.body.appendChild(container);
            
            // Load form
            this.loadForm(slug, container, 'popup', () => {
                container.classList.add('active');
            });
        } else {
            container.classList.add('active');
        }
    },

    /**
     * Close a popup
     */
    closePopup: function(slug) {
        // If slug is event (clicked close button), find parent
        if (typeof slug === 'object') {
            const wrapper = slug.target.closest('.evolv-form-popup-wrapper') || slug.target.closest('.evolv-form-container.evolv-form-popup');
            if (wrapper) wrapper.classList.remove('active');
            return;
        }

        // Try to find by slug first
        const containerId = 'evolv-form-' + slug + '-popup';
        const container = document.getElementById(containerId);
        if (container) {
            container.classList.remove('active');
            return;
        }

        // Fallback: finding by class
        document.querySelectorAll('.evolv-form-popup.active, .evolv-form-popup-wrapper.active').forEach(el => {
            el.classList.remove('active');
        });
    },

    /**
     * Load form HTML from server
     */
    loadForm: function(slug, container, type, callback) {
        fetch(`${this.apiUrl}get-form.php?slug=${slug}&type=${type}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    container.innerHTML = data.html;
                    if (callback) callback();
                    
                    // Re-run scripts if any (though innerHTML doesn't exec scripts usually)
                } else {
                    container.innerHTML = `<div class="evolv-form-error">Error: ${data.error}</div>`;
                }
            })
            .catch(error => {
                console.error('Error loading form:', error);
                container.innerHTML = `<div class="evolv-form-error">Failed to load form.</div>`;
            });
    },

    /**
     * Handle form submission
     */
    submit: function(form, event) {
        event.preventDefault();
        
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        const responseEl = form.querySelector('.evolv-form-response');
        const slug = form.dataset.slug;

        // Disable button
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="evolv-spinner"></span> Sending...';
        responseEl.innerHTML = '';
        responseEl.className = 'evolv-form-response';

        const formData = new FormData(form);
        formData.append('form_slug', slug); // Ensure slug is sent

        fetch(`${this.apiUrl}submit-form.php`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                responseEl.innerHTML = data.message;
                responseEl.classList.add('success');
                form.reset();
                
                // Redirect if needed
                if (data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 2000);
                } else if (form.closest('.evolv-form-popup') || form.closest('.evolv-form-popup-wrapper')) {
                    // Close popup after delay
                    setTimeout(() => {
                        this.closePopup(slug);
                        responseEl.innerHTML = '';
                        responseEl.className = 'evolv-form-response';
                    }, 3000);
                }
            } else {
                responseEl.innerHTML = data.error || 'An error occurred.';
                responseEl.classList.add('error');
            }
        })
        .catch(error => {
            console.error('Submission error:', error);
            responseEl.innerHTML = 'Network error. Please try again.';
            responseEl.classList.add('error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });

        return false;
    }
};
