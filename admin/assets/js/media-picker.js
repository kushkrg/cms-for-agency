/**
 * Evolvcode CMS - Media Picker
 */

class MediaPicker {
    constructor(options = {}) {
        this.onSelect = options.onSelect || (() => {});
        this.apiUrl = options.apiUrl || '/admin/api-media.php';
        this.modalId = 'media-picker-modal';
        this.isOpen = false;
        
        this.init();
    }

    init() {
        this.createModal();
        this.attachGlobalEvents();
    }

    createModal() {
        if (document.getElementById(this.modalId)) return;

        const modal = document.createElement('div');
        modal.id = this.modalId;
        modal.className = 'media-picker-modal';
        modal.innerHTML = `
            <div class="media-picker-content">
                <div class="media-picker-header">
                    <h2>Select Media</h2>
                    <button class="close-picker">&times;</button>
                </div>
                <div class="media-picker-body">
                    <div class="picker-sidebar">
                        <select id="picker-filter" class="form-control">
                            <option value="">All Media</option>
                            <option value="image">Images</option>
                        </select>
                         <input type="text" id="picker-search" class="form-control" placeholder="Search...">
                    </div>
                    <div class="picker-grid" id="picker-grid">
                        <!-- Items -->
                    </div>
                </div>
                <div class="media-picker-footer">
                     <div class="pagination-controls">
                        <button id="picker-prev" class="btn btn-sm">Prev</button>
                        <span id="picker-page-info">Page 1</span>
                        <button id="picker-next" class="btn btn-sm">Next</button>
                     </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        
        // Add Styles
        const style = document.createElement('style');
        style.textContent = `
            .media-picker-modal {
                position: fixed; top: 0; left: 0; width: 100%; height: 100%;
                background: rgba(0,0,0,0.5); z-index: 9999; display: none;
                justify-content: center; align-items: center;
                backdrop-filter: blur(5px);
            }
            .media-picker-modal.active { display: flex; animation: fadeIn 0.2s; }
            .media-picker-content {
                background: white; width: 90%; max-width: 1000px; height: 80vh;
                border-radius: 12px; display: flex; flex-direction: column;
                box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            }
            .media-picker-header {
                padding: 15px 20px; border-bottom: 1px solid #e2e8f0;
                display: flex; justify-content: space-between; align-items: center;
            }
            .media-picker-header h2 { margin: 0; font-size: 1.25rem; color: #1e293b; }
            .close-picker { background: none; border: none; font-size: 24px; cursor: pointer; color: #64748b; }
            .media-picker-body { flex: 1; display: flex; flex-direction: column; overflow: hidden; padding: 20px; }
            .picker-sidebar { display: flex; gap: 10px; margin-bottom: 20px; }
            .picker-grid { 
                display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); 
                gap: 15px; overflow-y: auto; flex: 1; padding: 5px;
            }
            .picker-item {
                position: relative; aspect-ratio: 1; border-radius: 8px; overflow: hidden;
                cursor: pointer; border: 2px solid transparent; background: #f1f5f9;
                transition: all 0.2s;
            }
            .picker-item:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
            .picker-item.selected { border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59,130,246,0.1); }
            .picker-item img { width: 100%; height: 100%; object-fit: cover; }
            .media-picker-footer {
                padding: 15px 20px; border-top: 1px solid #e2e8f0;
                display: flex; justify-content: flex-end;
            }
            @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        `;
        document.head.appendChild(style);

        // Bind events
        modal.querySelector('.close-picker').onclick = () => this.close();
        modal.onclick = (e) => { if (e.target === modal) this.close(); };
        
        this.page = 1;
        document.getElementById('picker-prev').onclick = () => this.loadPage(this.page - 1);
        document.getElementById('picker-next').onclick = () => this.loadPage(this.page + 1);
        
        document.getElementById('picker-filter').onchange = () => { this.page = 1; this.loadMedia(); };
        document.getElementById('picker-search').oninput = this.debounce(() => { this.page = 1; this.loadMedia(); }, 500);
    }

    open() {
        document.getElementById(this.modalId).classList.add('active');
        this.loadMedia();
        this.isOpen = true;
    }

    close() {
        document.getElementById(this.modalId).classList.remove('active');
        this.isOpen = false;
    }

    loadPage(pageNum) {
        if (pageNum < 1) return;
        this.page = pageNum;
        this.loadMedia();
    }

    loadMedia() {
        const type = document.getElementById('picker-filter').value;
        const search = document.getElementById('picker-search').value;
        const grid = document.getElementById('picker-grid');
        
        grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 20px;">Loading...</div>';

        fetch(`${this.apiUrl}?action=list&page=${this.page}&type=${type}&q=${encodeURIComponent(search)}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.render(data.items);
                    this.updatePagination(data.pagination);
                }
            })
            .catch(err => {
                grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: red;">Error loading media</div>';
            });
    }

    render(items) {
        const grid = document.getElementById('picker-grid');
        grid.innerHTML = '';
        
        if (items.length === 0) {
            grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #64748b;">No media found</div>';
            return;
        }

        items.forEach(item => {
            const div = document.createElement('div');
            div.className = 'picker-item';
            
            if (item.is_image) {
                div.innerHTML = `<img src="${item.url}" alt="${item.title}" loading="lazy">`;
            } else {
                div.innerHTML = `<div style="display:flex;justify-content:center;align-items:center;height:100%;color:#64748b;"><i class="fas fa-file"></i></div>`;
            }

            div.onclick = () => {
                this.onSelect(item);
                this.close();
            };
            
            grid.appendChild(div);
        });
    }

    updatePagination(pagination) {
        document.getElementById('picker-page-info').textContent = `Page ${pagination.page} of ${pagination.pages || 1}`;
        document.getElementById('picker-prev').disabled = pagination.page <= 1;
        document.getElementById('picker-next').disabled = pagination.page >= pagination.pages;
    }

    debounce(func, wait) {
        let timeout;
        return (...args) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => func(...args), wait);
        };
    }
    
    attachGlobalEvents() {
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-media-picker]')) {
                e.preventDefault();
                const targetInput = document.getElementById(e.target.dataset.targetInput);
                const targetPreview = document.getElementById(e.target.dataset.targetPreview);
                
                this.onSelect = (item) => {
                    if (targetInput) targetInput.value = item.file_path; // Store relative path
                    if (targetPreview) {
                        targetPreview.src = item.url;
                        targetPreview.style.display = 'block';
                    }
                };
                
                this.open();
            }
        });
    }
}

// Auto-init
document.addEventListener('DOMContentLoaded', () => {
    window.mediaPicker = new MediaPicker();
});
