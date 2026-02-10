<?php
/**
 * Evolvcode CMS - Admin Forms Management
 * 
 * CRUD operations for managing forms.
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Auth.php';

// Check authentication
Auth::requireLogin();
Auth::requirePermission('forms');

require_once __DIR__ . '/../includes/FormHelper.php';
// Auto-migrate tables
FormHelper::ensureTables();

$db = Database::getInstance();
$error = '';
$success = '';

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'create':
            case 'update':
                $id = (int)($_POST['id'] ?? 0);
                $name = Security::clean($_POST['name'] ?? '');
                $slug = Security::clean($_POST['slug'] ?? '');
                $type = Security::clean($_POST['type'] ?? 'popup');
                $title = Security::clean($_POST['title'] ?? '');
                $description = Security::clean($_POST['description'] ?? '');
                $submit_button_text = Security::clean($_POST['submit_button_text'] ?? 'Submit');
                $success_message = Security::clean($_POST['success_message'] ?? '');
                $email_notification = isset($_POST['email_notification']) ? 1 : 0;
                $email_to = Security::clean($_POST['email_to'] ?? '');
                $status = Security::clean($_POST['status'] ?? 'active');
                
                // Generate slug if empty
                if (empty($slug)) {
                    $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
                }
                
                // Validate
                if (empty($name)) {
                    $error = 'Form name is required.';
                    break;
                }
                
                $formData = [
                    'name' => $name,
                    'slug' => $slug,
                    'type' => $type,
                    'title' => $title,
                    'description' => $description,
                    'submit_button_text' => $submit_button_text,
                    'success_message' => $success_message,
                    'email_notification' => $email_notification,
                    'email_to' => $email_to,
                    'status' => $status
                ];
                
                try {
                    if ($id > 0) {
                        $db->update('forms', $formData, 'id = ?', [$id]);
                        $success = 'Form updated successfully.';
                    } else {
                        $db->insert('forms', $formData);
                        $success = 'Form created successfully.';
                    }
                } catch (Exception $e) {
                    $error = 'Error saving form: ' . $e->getMessage();
                }
                break;
                
            case 'delete':
                $id = (int)($_POST['id'] ?? 0);
                if ($id > 0) {
                    try {
                        $db->delete('forms', 'id = ?', [$id]);
                        $success = 'Form deleted successfully.';
                    } catch (Throwable $e) {
                        $error = 'Error deleting form: ' . $e->getMessage();
                    }
                }
                break;
        }
    }
}

// Get all forms
$forms = $db->fetchAll("SELECT f.*, 
    (SELECT COUNT(*) FROM form_fields WHERE form_id = f.id) as field_count,
    (SELECT COUNT(*) FROM form_submissions WHERE form_id = f.id) as submission_count
    FROM forms f ORDER BY f.created_at DESC");

// Get form for editing
$editForm = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $editForm = $db->fetch("SELECT * FROM forms WHERE id = ?", [$editId]);
}

$pageTitle = 'Forms';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Forms</h1>
        <p class="page-subtitle">Manage generic, popup, and contact forms</p>
    </div>
    <button class="btn btn-primary" onclick="showFormModal()">
        <i class="fas fa-plus"></i> Create Form
    </button>
</div>

<?php if ($error): ?>
<div class="alert alert-error"><?= e($error) ?></div>
<?php endif; ?>

<?php if ($success): ?>
<div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Form Name</th>
                        <th>Type</th>
                        <th>Fields</th>
                        <th>Submissions</th>
                        <th>Status</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($forms)): ?>
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 30px;">
                            <div class="empty-state">
                                <i class="fas fa-wpforms"></i>
                                <h3>No Forms Found</h3>
                                <p>Create your first form to get started.</p>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($forms as $form): ?>
                    <tr>
                        <td>
                            <strong><?= e($form['name']) ?></strong>
                            <div style="font-size: 12px; color: var(--color-gray-500);">/<?= e($form['slug']) ?></div>
                        </td>
                        <td>
                            <span class="status-badge" style="background: #f5f5f5; color: #666;">
                                <?= ucfirst($form['type']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="form-fields.php?form_id=<?= $form['id'] ?>" class="btn btn-sm btn-secondary">
                                <i class="fas fa-list"></i> <?= $form['field_count'] ?> Fields
                            </a>
                        </td>
                        <td>
                            <a href="form-submissions.php?form_id=<?= $form['id'] ?>" class="btn btn-sm btn-secondary">
                                <i class="fas fa-inbox"></i> <?= $form['submission_count'] ?> Submissions
                            </a>
                        </td>
                        <td>
                            <span class="status-badge status-<?= $form['status'] ?>">
                                <?= ucfirst($form['status']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="form-fields.php?form_id=<?= $form['id'] ?>" title="Manage Fields">
                                    <i class="fas fa-list"></i>
                                </a>
                                <button type="button" onclick="showEmbedCode('<?= e($form['slug']) ?>', '<?= e($form['type']) ?>')" title="Get Embed Code">
                                    <i class="fas fa-code"></i>
                                </button>
                                <button type="button" onclick="editForm(<?= e(json_encode($form)) ?>)" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="delete" onclick="deleteForm(<?= $form['id'] ?>, '<?= e($form['name']) ?>')" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Form Modal -->
<div id="formModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Create Form</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form method="POST" id="formForm">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <input type="hidden" name="action" id="formAction" value="create">
            <input type="hidden" name="id" id="formId" value="0">
            
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Form Name *</label>
                        <input type="text" id="name" name="name" required placeholder="e.g., Contact Form">
                    </div>
                    <div class="form-group">
                        <label for="slug">Slug</label>
                        <input type="text" id="slug" name="slug" placeholder="Auto-generated if empty">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="type">Form Type</label>
                        <select id="type" name="type">
                            <option value="popup">Popup</option>
                            <option value="embedded">Embedded</option>
                            <option value="contact">Contact Page</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="title">Form Title</label>
                    <input type="text" id="title" name="title" placeholder="e.g., Get a Free Consultation">
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="2" placeholder="Brief description shown below the title"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="submit_button_text">Submit Button Text</label>
                        <input type="text" id="submit_button_text" name="submit_button_text" value="Submit">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="success_message">Success Message</label>
                    <textarea id="success_message" name="success_message" rows="2" placeholder="Thank you! Your submission has been received."></textarea>
                </div>
                
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="email_notification" name="email_notification" checked>
                        Send email notification on submission
                    </label>
                </div>
                
                <div class="form-group" id="emailToGroup">
                    <label for="email_to">Notification Email</label>
                    <input type="email" id="email_to" name="email_to" placeholder="Leave empty to use default contact email">
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Form</button>
            </div>
        </form>
    </div>
</div>

<!-- Embed Code Modal -->
<div id="embedModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Embed Form</h3>
            <button class="modal-close" onclick="closeEmbedModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="embed-tabs">
                <button class="embed-tab active" onclick="switchEmbedTab('popup')">Popup Trigger</button>
                <button class="embed-tab" onclick="switchEmbedTab('embedded')">Embedded Form</button>
                <button class="embed-tab" onclick="switchEmbedTab('php')">PHP Include</button>
            </div>
            
            <div id="embed-content-popup" class="embed-content active">
                <p>Use this code to trigger the popup form on button click.</p>
                <div class="code-block">
                    <pre><code id="code-popup"></code></pre>
                    <button class="copy-btn" onclick="copyCode('code-popup')">Copy</button>
                </div>
            </div>
            
            <div id="embed-content-embedded" class="embed-content">
                <p>Use this code to embed the form directly in your page.</p>
                <div class="code-block">
                    <pre><code id="code-embedded"></code></pre>
                    <button class="copy-btn" onclick="copyCode('code-embedded')">Copy</button>
                </div>
            </div>
            
            <div id="embed-content-php" class="embed-content">
                <p>Use this code in your PHP template files.</p>
                <div class="code-block">
                    <pre><code id="code-php"></code></pre>
                    <button class="copy-btn" onclick="copyCode('code-php')">Copy</button>
                </div>
            </div>
            
            <div class="alert alert-info" style="margin-top: 15px; margin-bottom: 0;">
                <i class="fas fa-info-circle"></i> 
                Make sure to include <strong>/assets/js/forms.js</strong> and <strong>/assets/css/forms.css</strong> in your site header/footer.
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" style="display: none;">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" id="deleteId">
</form>

<style>
.modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.6);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.modal.active {
    display: flex;
}
.modal-content {
    background: #fff;
    border-radius: 12px;
    width: 100%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
}
.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #eee;
}
.modal-header h3 {
    margin: 0;
}
.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #666;
}
.modal-body {
    padding: 20px;
}
.modal-footer {
    padding: 20px;
    border-top: 1px solid #eee;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}
.badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
}
.badge-primary {
    background: #000;
    color: #fff;
}
.badge-secondary {
    background: #e0e0e0;
    color: #333;
}
.text-link {
    color: #0066cc;
    text-decoration: none;
}
.text-link:hover {
    text-decoration: underline;
}
.checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}
.checkbox-label input {
    width: auto;
}
.embed-tabs {
    display: flex;
    border-bottom: 1px solid #ddd;
    margin-bottom: 20px;
}
.embed-tab {
    padding: 10px 20px;
    background: none;
    border: none;
    border-bottom: 2px solid transparent;
    cursor: pointer;
    font-weight: 500;
    color: #666;
}
.embed-tab.active {
    color: #000;
    border-bottom-color: #000;
}
.embed-content {
    display: none;
}
.embed-content.active {
    display: block;
}
.code-block {
    background: #f5f5f5;
    padding: 15px;
    border-radius: 6px;
    position: relative;
    border: 1px solid #ddd;
}
.code-block pre {
    margin: 0;
    white-space: pre-wrap;
    word-break: break-all;
    font-family: monospace;
    font-size: 13px;
    color: #333;
}
.copy-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    background: #fff;
    border: 1px solid #ddd;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    cursor: pointer;
}
.copy-btn:hover {
    background: #f0f0f0;
}
</style>

<script>
function showFormModal() {
    document.getElementById('modalTitle').textContent = 'Create Form';
    document.getElementById('formAction').value = 'create';
    document.getElementById('formId').value = '0';
    document.getElementById('formForm').reset();
    document.getElementById('email_notification').checked = true;
    document.getElementById('formModal').classList.add('active');
}

function editForm(form) {
    document.getElementById('modalTitle').textContent = 'Edit Form';
    document.getElementById('formAction').value = 'update';
    document.getElementById('formId').value = form.id;
    document.getElementById('name').value = form.name;
    document.getElementById('slug').value = form.slug;
    document.getElementById('type').value = form.type;
    document.getElementById('title').value = form.title || '';
    document.getElementById('description').value = form.description || '';
    document.getElementById('submit_button_text').value = form.submit_button_text || 'Submit';
    document.getElementById('success_message').value = form.success_message || '';
    document.getElementById('email_notification').checked = form.email_notification == 1;
    document.getElementById('email_to').value = form.email_to || '';
    document.getElementById('status').value = form.status;
    document.getElementById('formModal').classList.add('active');
}

function closeModal() {
    document.getElementById('formModal').classList.remove('active');
}

function deleteForm(id, name) {
    if (confirm('Are you sure you want to delete "' + name + '"? This will also delete all fields and submissions.')) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteForm').submit();
    }
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});

// Close modal on background click
document.getElementById('formModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Embed Modal Logic
function showEmbedCode(slug, type) {
    const siteUrl = '<?= SITE_URL ?>'; // Ensure SITE_URL is defined in JS
    
    // Popup Code
    const popupCode = `<!-- Button Trigger -->\n<button onclick="EvolvForm.openPopup('${slug}')">Open Form</button>\n\n<!-- Scripts (Header) -->\n<link rel="stylesheet" href="/assets/css/forms.css">\n<script src="/assets/js/forms.js"><\/script>`;
    document.getElementById('code-popup').textContent = popupCode;
    
    // Embedded Code
    const embedCode = `<!-- Container -->\n<div id="form-${slug}"></div>\n\n<!-- Script -->\n<link rel="stylesheet" href="/assets/css/forms.css">\n<script src="/assets/js/forms.js"><\/script>\n<script>\n  document.addEventListener('DOMContentLoaded', () => {\n    EvolvForm.init('${slug}', 'form-${slug}');\n  });\n<\/script>`;
    document.getElementById('code-embedded').textContent = embedCode;
    
    // PHP Code
    const phpCode = `<?php\nrequire_once 'includes/FormHelper.php';\necho FormHelper::render('${slug}');\n?>`;
    document.getElementById('code-php').textContent = phpCode;
    
    // Select appropriate tab
    if (type === 'popup') {
        switchEmbedTab('popup');
    } else {
        switchEmbedTab('embedded');
    }
    
    document.getElementById('embedModal').classList.add('active');
}

function closeEmbedModal() {
    document.getElementById('embedModal').classList.remove('active');
}

function switchEmbedTab(tabName) {
    document.querySelectorAll('.embed-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.embed-content').forEach(c => c.classList.remove('active'));
    
    // Find button by text content is hard, so index logic or add data-tab
    // Simpler: use onclick to pass element or find by text
    const buttons = document.querySelectorAll('.embed-tab');
    if (tabName === 'popup') buttons[0].classList.add('active');
    if (tabName === 'embedded') buttons[1].classList.add('active');
    if (tabName === 'php') buttons[2].classList.add('active');
    
    document.getElementById('embed-content-' + tabName).classList.add('active');
}

function copyCode(elementId) {
    const text = document.getElementById(elementId).textContent;
    navigator.clipboard.writeText(text).then(() => {
        alert('Copied to clipboard!');
    });
}

document.getElementById('embedModal').addEventListener('click', function(e) {
    if (e.target === this) closeEmbedModal();
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
