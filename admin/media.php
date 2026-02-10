<?php
/**
 * Evolvcode CMS - Advanced Media Library
 */

$pageTitle = 'Media Library';
require_once __DIR__ . '/includes/header.php';
Auth::requirePermission('media');
?>

<div class="media-library-app">
    <!-- Toolbar -->
    <div class="media-toolbar">
        <div class="toolbar-left">
            <h1 class="page-title">Media Library</h1>
            <button class="btn btn-primary" id="upload-btn">
                <i class="fas fa-upload"></i> Add New
            </button>
        </div>
        <div class="toolbar-right">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="media-search" placeholder="Search media...">
            </div>
            <select id="media-filter" class="form-control">
                <option value="">All Media Items</option>
                <option value="image">Images</option>
                <option value="document">Documents</option>
            </select>
        </div>
    </div>

    <!-- Upload Area (Hidden by default) -->
    <div id="upload-zone" class="upload-zone hidden">
        <div class="upload-inner">
            <i class="fas fa-cloud-upload-alt"></i>
            <h3>Drop files to upload</h3>
            <p>or</p>
            <button class="btn btn-secondary" onclick="document.getElementById('file-input').click()">Select Files</button>
            <input type="file" id="file-input" multiple style="display: none;">
        </div>
        <div id="upload-progress" class="hidden">
            <div class="progress-bar"><div class="progress-fill"></div></div>
            <span class="progress-text">Uploading...</span>
        </div>
    </div>

    <!-- Media Grid -->
    <div class="media-grid-container">
        <div id="media-grid" class="media-grid">
            <!-- Items injected via JS -->
        </div>
        <div id="loading" class="loading-spinner hidden">
            <i class="fas fa-spinner fa-spin"></i> Loading...
        </div>
        <div id="no-results" class="empty-state hidden">
            <i class="far fa-images"></i>
            <h3>No media found</h3>
        </div>
    </div>

    <!-- Details Modal -->
    <div id="media-modal" class="modal">
        <div class="modal-content media-modal-content">
            <button class="modal-close" onclick="closeModal()">&times;</button>
            <div class="media-details-layout">
                <div class="media-preview-pane">
                    <!-- Preview injected via JS -->
                </div>
                <div class="media-meta-pane">
                    <h3>Attachment Details</h3>
                    <div class="meta-info">
                        <strong>Uploaded on:</strong> <span id="meta-date"></span><br>
                        <strong>Uploaded by:</strong> <span id="meta-user">Admin</span><br>
                        <strong>File name:</strong> <span id="meta-filename"></span><br>
                        <strong>File type:</strong> <span id="meta-type"></span><br>
                        <strong>File size:</strong> <span id="meta-size"></span><br>
                        <strong>Dimensions:</strong> <span id="meta-dimensions"></span>
                    </div>
                    
                    <hr>

                    <form id="meta-form">
                        <input type="hidden" id="edit-id">
                        <div class="form-group">
                            <label>Alternative Text</label>
                            <input type="text" id="edit-alt" class="form-control" placeholder="Describe this image...">
                            <small>Learn how to describe the purpose of the image.</small>
                        </div>
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" id="edit-title" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea id="edit-desc" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label>File URL</label>
                            <div class="input-group">
                                <input type="text" id="meta-url" class="form-control" readonly>
                                <button type="button" class="btn btn-secondary" onclick="copyUrl()">Copy</button>
                            </div>
                        </div>
                    </form>
                    
                    <div class="modal-actions">
                        <a href="#" id="view-link" target="_blank" class="btn-link">View Attachment Page</a>
                        <button class="btn-link text-danger" onclick="deleteMedia()">Delete Permanently</button>
                    </div>
                    <div id="save-msg" class="save-indicator hidden">Saved.</div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* App Layout */
    .media-library-app {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 100px);
        padding: 30px;
        background: #f8f9fa;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    /* Toolbar */
    .media-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #ffffff;
        padding: 16px 24px;
        border-radius: 12px;
        margin-bottom: 24px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.03);
        border: 1px solid rgba(0,0,0,0.02);
    }
    .toolbar-left h1 {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1a1f36;
        margin: 0;
    }
    .toolbar-right {
        display: flex;
        gap: 12px;
    }
    
    /* Search Box */
    .search-box {
        position: relative;
    }
    .search-box i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #a0aec0;
        font-size: 14px;
        pointer-events: none;
    }
    .search-box input {
        padding-left: 40px;
        padding-right: 16px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        height: 40px;
        width: 240px;
        background: #f8fafc;
        transition: all 0.2s ease;
        font-size: 0.9rem;
    }
    .search-box input:focus {
        background: #fff;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    /* Filters & Buttons */
    #media-filter {
        height: 40px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        padding: 0 32px 0 16px;
        background-color: #fff;
        cursor: pointer;
        font-size: 0.9rem;
    }
    .btn-primary {
        background: #3b82f6;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        box-shadow: 0 4px 6px rgba(59, 130, 246, 0.2);
        transition: transform 0.1s, box-shadow 0.2s;
    }
    .btn-primary:active {
        transform: translateY(1px);
        box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
    }

    /* Upload Zone */
    .upload-zone {
        border: 2px dashed #cbd5e1;
        background: #f8fafc;
        border-radius: 16px;
        padding: 60px;
        text-align: center;
        margin-bottom: 24px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .upload-zone:hover, .upload-zone.dragover {
        border-color: #3b82f6;
        background: #eff6ff;
        transform: scale(1.005);
    }
    .upload-inner i { 
        font-size: 48px; 
        color: #94a3b8; 
        margin-bottom: 16px; 
        display: block;
        transition: color 0.3s;
    }
    .upload-zone:hover .upload-inner i { color: #3b82f6; }
    .upload-zone h3 {
        font-size: 1.1rem;
        color: #334155;
        margin-bottom: 8px;
    }
    .upload-zone p {
        color: #94a3b8;
        font-size: 0.9rem;
    }
    .upload-zone.hidden { display: none; }
    .hidden { display: none !important; }

    /* Grid */
    .media-grid-container {
        flex: 1;
        overflow-y: auto;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        padding: 24px;
        border: 1px solid rgba(0,0,0,0.02);
    }
    .media-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 20px;
    }
    .media-item {
        position: relative;
        aspect-ratio: 1;
        background: #f1f5f9;
        border-radius: 12px;
        cursor: pointer;
        overflow: hidden;
        transition: all 0.2s ease;
        border: 2px solid transparent;
    }
    .media-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    .media-item.selected {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
    }
    .media-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .media-item:hover img {
        transform: scale(1.05);
    }
    .media-item-icon {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        padding: 16px;
        background: #f8fafc;
        color: #64748b;
        text-align: center;
    }
    .media-item-title {
        font-size: 13px;
        margin-top: 12px;
        font-weight: 500;
        color: #475569;
        word-break: break-all;
        line-height: 1.4;
    }

    /* Modal - Glassmorphism */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        backdrop-filter: blur(8px);
        background-color: rgba(15, 23, 42, 0.4);
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s ease;
    }
    .modal.active {
        display: flex;
        opacity: 1;
    }
    .media-modal-content {
        width: 90%;
        max-width: 1100px;
        height: 85vh;
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        animation: modalSlideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes modalSlideUp {
        from { transform: translateY(20px) scale(0.98); }
        to { transform: translateY(0) scale(1); }
    }
    
    .modal-close {
        position: absolute;
        right: 20px;
        top: 20px;
        width: 32px;
        height: 32px;
        background: rgba(0,0,0,0.05);
        border: none;
        border-radius: 50%;
        font-size: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 10;
        transition: background 0.2s;
        color: #475569;
    }
    .modal-close:hover { background: rgba(0,0,0,0.1); color: #000; }

    .media-details-layout {
        display: flex;
        height: 100%;
    }
    .media-preview-pane {
        flex: 1.5;
        background: #f1f5f9; /* Checkered pattern ideally */
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px;
        position: relative;
    }
    .media-preview-pane img {
        max-width: 100%;
        max-height: 100%;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-radius: 4px;
    }
    .media-meta-pane {
        flex: 1;
        min-width: 350px;
        padding: 32px;
        background: #ffffff;
        overflow-y: auto;
        border-left: 1px solid #e2e8f0;
    }
    .media-meta-pane h3 {
        margin-top: 0;
        font-size: 1.1rem;
        color: #0f172a;
        margin-bottom: 20px;
    }
    
    .meta-info {
        font-size: 0.9rem;
        color: #64748b;
        line-height: 1.6;
        padding-bottom: 20px;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 24px;
    }
    .meta-info strong { color: #334155; font-weight: 600; min-width: 100px; display: inline-block; }

    .form-group { margin-bottom: 20px; }
    .form-group label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: #475569;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }
    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 0.95rem;
        transition: border-color 0.2s;
    }
    .form-control:focus {
        border-color: #3b82f6;
        outline: none;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .input-group { display: flex; gap: 8px; }
    
    .modal-actions {
        margin-top: 32px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 20px;
        border-top: 1px solid #e2e8f0;
    }
    .btn-link {
        background: none;
        border: none;
        color: #3b82f6;
        cursor: pointer;
        font-weight: 500;
        padding: 0;
        font-size: 0.9rem;
    }
    .btn-link:hover { text-decoration: underline; }
    .text-danger { color: #ef4444; }
    .text-danger:hover { color: #dc2626; }

    .save-indicator {
        position: fixed;
        bottom: 30px;
        right: 40px;
        background: #10b981;
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 500;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        z-index: 1100;
    }

    /* Mobile */
    @media (max-width: 768px) {
        .media-library-app { padding: 10px; height: auto; }
        .media-details-layout { flex-direction: column; overflow-y: auto; }
        .media-preview-pane { min-height: 250px; flex: none; }
        .media-meta-pane { flex: none; overflow: visible; }
    }
</style>

<script>
const API_URL = '<?= ADMIN_URL ?>/api-media.php';
const CSRF_TOKEN = '<?= Security::generateCSRFToken() ?>';

// State
let page = 1;
let loading = false;
let hasMore = true;
let currentFile = null;

// Init
document.addEventListener('DOMContentLoaded', () => {
    loadMedia();
    setupEventListeners();
});

function setupEventListeners() {
    // Toolbar
    document.getElementById('upload-btn').onclick = () => {
        document.getElementById('upload-zone').classList.toggle('hidden');
    };
    
    document.getElementById('media-search').addEventListener('input', debounce(() => {
        page = 1;
        document.getElementById('media-grid').innerHTML = '';
        hasMore = true;
        loadMedia();
    }, 500));

    document.getElementById('media-filter').addEventListener('change', () => {
        page = 1;
        document.getElementById('media-grid').innerHTML = '';
        hasMore = true;
        loadMedia();
    });

    // Upload
    const dropZone = document.getElementById('upload-zone');
    dropZone.addEventListener('dragover', e => {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });
    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
    dropZone.addEventListener('drop', handleDrop);
    document.getElementById('file-input').addEventListener('change', handleFileSelect);

    // Infinite Scroll
    const gridContainer = document.querySelector('.media-grid-container');
    gridContainer.addEventListener('scroll', () => {
        if (gridContainer.scrollTop + gridContainer.clientHeight >= gridContainer.scrollHeight - 50) {
            loadMedia();
        }
    });

    // Meta Save
    const inputs = ['edit-title', 'edit-alt', 'edit-desc'];
    inputs.forEach(id => {
        document.getElementById(id).addEventListener('blur', saveMeta);
    });
}

function loadMedia() {
    if (loading || !hasMore) return;
    loading = true;
    document.getElementById('loading').classList.remove('hidden');

    const search = document.getElementById('media-search').value;
    const type = document.getElementById('media-filter').value;

    fetch(`${API_URL}?action=list&page=${page}&q=${encodeURIComponent(search)}&type=${type}`)
        .then(res => {
            if (!res.ok) throw new Error('Network response was not ok');
            return res.json();
        })
        .then(data => {
            if (data.success) {
                renderGrid(data.items);
                page++;
                hasMore = page <= data.pagination.pages;
                
                // Show empty state if no items
                if (data.pagination.total === 0) {
                    document.getElementById('no-results').classList.remove('hidden');
                } else {
                    document.getElementById('no-results').classList.add('hidden');
                }
            } else {
                 console.error('API Error:', data.message);
            }
        })
        .catch(err => {
            console.error('Fetch error:', err);
            // Optionally show error message to user
        })
        .finally(() => {
            loading = false;
            const loader = document.getElementById('loading');
            if (loader) loader.classList.add('hidden');
        });
}

function renderGrid(items) {
    const grid = document.getElementById('media-grid');
    items.forEach(item => {
        const div = document.createElement('div');
        div.className = 'media-item';
        div.dataset.id = item.id;
        div.onclick = () => openModal(item);

        if (item.is_image) {
            div.innerHTML = `<img src="${item.url}" alt="${item.title}" loading="lazy">`;
        } else {
            const ext = item.filename.split('.').pop().toUpperCase();
            div.innerHTML = `
                <div class="media-item-icon">
                    <i class="fas fa-file-alt fa-3x" style="color: #8c8f94"></i>
                    <div class="media-item-title">${item.title}.${ext}</div>
                </div>`;
        }
        grid.appendChild(div);
    });
}

// Upload Handling
function handleDrop(e) {
    e.preventDefault();
    document.getElementById('upload-zone').classList.remove('dragover');
    uploadFiles(e.dataTransfer.files);
}

function handleFileSelect(e) {
    uploadFiles(e.target.files);
}

function uploadFiles(files) {
    if (!files.length) return;
    
    document.getElementById('upload-progress').classList.remove('hidden');
    
    Array.from(files).forEach(file => {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('action', 'upload');
        formData.append('csrf_token', CSRF_TOKEN);

        fetch(API_URL, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Image uploaded successfully', 'success');
                    // Prepend to grid
                    page = 1;
                    loading = false; // Reset loading state
                    hasMore = true; // Reset hasMore state
                    document.getElementById('media-grid').innerHTML = '';
                    document.getElementById('no-results').classList.add('hidden'); // Hide empty state
                    loadMedia();
                } else {
                    showToast(data.message || 'Unknown error', 'error');
                }
            })
            .catch(err => {
                console.error('Upload Error:', err);
                alert('Upload error. Check console for details.');
            })
            .finally(() => {
                document.getElementById('upload-progress').classList.add('hidden');
            });
    });
}

// Modal Functions
function openModal(item) {
    currentFile = item;
    const modal = document.getElementById('media-modal');
    modal.classList.add('active');
    
    // Set Preview
    const preview = document.querySelector('.media-preview-pane');
    if (item.is_image) {
        preview.innerHTML = `<img src="${item.url}">`;
    } else {
        preview.innerHTML = `<i class="fas fa-file-alt fa-5x" style="color: #8c8f94"></i><br><h3>${item.filename}</h3>`;
    }

    // Set Meta
    document.getElementById('meta-filename').textContent = item.filename;
    document.getElementById('meta-type').textContent = item.file_type;
    document.getElementById('meta-size').textContent = formatBytes(item.file_size);
    document.getElementById('meta-dimensions').textContent = item.dimensions || '-';
    document.getElementById('meta-date').textContent = new Date(item.uploaded_at).toLocaleDateString();
    
    document.getElementById('edit-id').value = item.id;
    document.getElementById('edit-title').value = item.title || '';
    document.getElementById('edit-alt').value = item.alt_text || '';
    document.getElementById('edit-desc').value = item.description || '';
    document.getElementById('meta-url').value = item.url;
    document.getElementById('view-link').href = item.url;
}

function closeModal() {
    document.getElementById('media-modal').classList.remove('active');
}

function saveMeta() {
    if (!currentFile) return;
    
    const id = document.getElementById('edit-id').value;
    const title = document.getElementById('edit-title').value;
    const alt = document.getElementById('edit-alt').value;
    const desc = document.getElementById('edit-desc').value;
    
    const formData = new FormData();
    formData.append('action', 'update');
    formData.append('id', id);
    formData.append('title', title);
    formData.append('alt_text', alt);
    formData.append('description', desc);
    formData.append('csrf_token', CSRF_TOKEN);

    fetch(API_URL, { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const msg = document.getElementById('save-msg');
                msg.classList.remove('hidden');
                setTimeout(() => msg.classList.add('hidden'), 2000);
                
                // Update local model
                currentFile.title = title;
                currentFile.alt_text = alt;
                currentFile.description = desc;
            }
        });
}

function deleteMedia() {
    if (!confirm('Are you sure you want to delete this file permanently?')) return;
    
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', currentFile.id);
    formData.append('csrf_token', CSRF_TOKEN);

    fetch(API_URL, { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                closeModal();
                // Remove from grid
                document.querySelector(`.media-item[data-id="${currentFile.id}"]`).remove();
            } else {
                alert('Delete failed: ' + data.message);
            }
        });
}

function copyUrl() {
    const el = document.getElementById('meta-url');
    el.select();
    document.execCommand('copy');
    alert('URL copied to clipboard');
}

// Utils
function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
// Toast Notification
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type}`;
    toast.innerHTML = `
        <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(toast);
    
    // Animate in
    setTimeout(() => toast.classList.add('show'), 10);
    
    // Remove after 3s
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>

<style>
/* Toast Notifications */
.toast-notification {
    position: fixed;
    bottom: 30px;
    right: 40px;
    background: #ffffff;
    color: #1e293b;
    padding: 12px 20px;
    border-radius: 12px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    display: flex;
    align-items: center;
    gap: 12px;
    z-index: 2000;
    transform: translateY(20px);
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    border: 1px solid #e2e8f0;
    font-size: 0.95rem;
    font-weight: 500;
}
.toast-notification.show {
    transform: translateY(0);
    opacity: 1;
}
.toast-success i { color: #10b981; }
.toast-error i { color: #ef4444; }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
