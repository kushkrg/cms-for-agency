/**
 * Evolvcode CMS - Admin Panel JavaScript
 */

document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    initSubmenu();
    initDeleteConfirm();
    initFormValidation();
    initMediaUpload();
});

/**
 * Sidebar Toggle
 */
function initSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mobileToggle = document.getElementById('mobile-toggle');
    
    if (mobileToggle && sidebar) {
        mobileToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
        
        // Close sidebar when clicking outside
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 1024 && 
                sidebar.classList.contains('active') && 
                !sidebar.contains(e.target) && 
                !mobileToggle.contains(e.target)) {
                sidebar.classList.remove('active');
            }
        });
    }
}

/**
 * Delete Confirmation
 */
function initDeleteConfirm() {
    document.querySelectorAll('.delete-btn, .delete').forEach(btn => {
        btn.addEventListener('click', (e) => {
            if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
}

/**
 * Form Validation
 */
function initFormValidation() {
    const forms = document.querySelectorAll('form[data-validate]');
    
    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            let valid = true;
            
            // Check required fields
            form.querySelectorAll('[required]').forEach(field => {
                if (!field.value.trim()) {
                    valid = false;
                    field.classList.add('is-invalid');
                    showFieldError(field, 'This field is required');
                } else {
                    field.classList.remove('is-invalid');
                    hideFieldError(field);
                }
            });
            
            // Check email fields
            form.querySelectorAll('input[type="email"]').forEach(field => {
                if (field.value && !isValidEmail(field.value)) {
                    valid = false;
                    field.classList.add('is-invalid');
                    showFieldError(field, 'Please enter a valid email address');
                }
            });
            
            if (!valid) {
                e.preventDefault();
            }
        });
    });
}

function showFieldError(field, message) {
    let errorEl = field.parentNode.querySelector('.field-error');
    if (!errorEl) {
        errorEl = document.createElement('span');
        errorEl.className = 'field-error';
        errorEl.style.cssText = 'color: #C62828; font-size: 12px; margin-top: 4px; display: block;';
        field.parentNode.appendChild(errorEl);
    }
    errorEl.textContent = message;
}

function hideFieldError(field) {
    const errorEl = field.parentNode.querySelector('.field-error');
    if (errorEl) {
        errorEl.remove();
    }
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

/**
 * Media Upload
 */
function initMediaUpload() {
    const uploadArea = document.getElementById('upload-area');
    const fileInput = document.getElementById('file-input');
    
    if (!uploadArea || !fileInput) return;
    
    // Click to upload
    uploadArea.addEventListener('click', () => {
        fileInput.click();
    });
    
    // Drag and drop
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });
    
    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });
    
    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFileUpload(files);
        }
    });
    
    // File input change
    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            handleFileUpload(fileInput.files);
        }
    });
}

function handleFileUpload(files) {
    const formData = new FormData();
    const csrfToken = document.querySelector('input[name="csrf_token"]')?.value;
    
    for (let i = 0; i < files.length; i++) {
        formData.append('files[]', files[i]);
    }
    
    if (csrfToken) {
        formData.append('csrf_token', csrfToken);
    }
    
    // Show upload progress
    showNotification('info', 'Uploading files...');
    
    fetch('/ajax/upload.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', data.message || 'Files uploaded successfully!');
            // Reload page to show new files
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showNotification('error', data.message || 'Upload failed');
        }
    })
    .catch(error => {
        showNotification('error', 'An error occurred during upload');
        console.error('Upload error:', error);
    });
}

/**
 * Show notification
 */
function showNotification(type, message) {
    // Remove existing notification
    const existing = document.querySelector('.admin-notification');
    if (existing) existing.remove();
    
    const notification = document.createElement('div');
    notification.className = `admin-notification notification-${type}`;
    notification.innerHTML = `
        <span>${message}</span>
        <button onclick="this.parentNode.remove()">&times;</button>
    `;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 80px;
        right: 24px;
        padding: 14px 20px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        z-index: 9999;
        animation: slideIn 0.3s ease;
        ${type === 'success' ? 'background: #E8F5E9; color: #2E7D32;' : ''}
        ${type === 'error' ? 'background: #FFEBEE; color: #C62828;' : ''}
        ${type === 'info' ? 'background: #E3F2FD; color: #1565C0;' : ''}
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

/**
 * Generate Slug from Title
 */
function generateSlug(text) {
    return text
        .toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/--+/g, '-')
        .trim();
}

// Auto-generate slug
const titleInput = document.getElementById('title');
const slugInput = document.getElementById('slug');

if (titleInput && slugInput) {
    titleInput.addEventListener('input', () => {
        if (!slugInput.dataset.manual) {
            slugInput.value = generateSlug(titleInput.value);
        }
    });
    
    slugInput.addEventListener('input', () => {
        slugInput.dataset.manual = 'true';
    });
}

// Rich text editor initialization (TinyMCE / CKEditor placeholder)
// Add your preferred editor initialization here

/**
 * Image Preview
 */
document.querySelectorAll('input[type="file"][data-preview]').forEach(input => {
    input.addEventListener('change', function() {
        const previewId = this.dataset.preview;
        const preview = document.getElementById(previewId);
        
        if (preview && this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
});

/**
 * Sidebar Submenu Toggle
 */
function initSubmenu() {
    const toggles = document.querySelectorAll('.submenu-toggle');
    
    toggles.forEach(toggle => {
        toggle.addEventListener('click', (e) => {
            e.preventDefault();
            const parent = toggle.parentElement;
            parent.classList.toggle('open');
        });
    });
}
