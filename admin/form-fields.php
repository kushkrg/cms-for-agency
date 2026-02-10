<?php
/**
 * Evolvcode CMS - Admin Form Fields Management
 * 
 * Manage fields for a specific form.
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Auth.php';

Auth::requireLogin();
Auth::requirePermission('forms');

$db = Database::getInstance();
$error = '';
$success = '';

// Get form ID
$formId = (int)($_GET['form_id'] ?? 0);
if (!$formId) {
    header('Location: forms.php');
    exit;
}

// Get form details
$form = $db->fetch("SELECT * FROM forms WHERE id = ?", [$formId]);
if (!$form) {
    header('Location: forms.php');
    exit;
}

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'create':
            case 'update':
                $id = (int)($_POST['id'] ?? 0);
                $label = Security::clean($_POST['label'] ?? '');
                $name = Security::clean($_POST['name'] ?? '');
                $type = Security::clean($_POST['type'] ?? 'text');
                $placeholder = Security::clean($_POST['placeholder'] ?? '');
                $default_value = Security::clean($_POST['default_value'] ?? '');
                $options = Security::clean($_POST['options'] ?? '');
                $is_required = isset($_POST['is_required']) ? 1 : 0;
                $sort_order = (int)($_POST['sort_order'] ?? 0);
                
                // Generate name from label if empty
                if (empty($name)) {
                    $name = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', $label));
                }
                
                if (empty($label)) {
                    $error = 'Field label is required.';
                    break;
                }
                
                $fieldData = [
                    'form_id' => $formId,
                    'label' => $label,
                    'name' => $name,
                    'type' => $type,
                    'placeholder' => $placeholder,
                    'default_value' => $default_value,
                    'options' => $options,
                    'is_required' => $is_required,
                    'sort_order' => $sort_order
                ];
                
                try {
                    if ($id > 0) {
                        $db->update('form_fields', $fieldData, 'id = ?', [$id]);
                        $success = 'Field updated.';
                    } else {
                        // Get max sort order
                        $maxOrder = $db->fetch("SELECT MAX(sort_order) as max_order FROM form_fields WHERE form_id = ?", [$formId]);
                        $fieldData['sort_order'] = ($maxOrder['max_order'] ?? 0) + 1;
                        $db->insert('form_fields', $fieldData);
                        $success = 'Field added.';
                    }
                } catch (Exception $e) {
                    $error = 'Error: ' . $e->getMessage();
                }
                break;
                
            case 'delete':
                $id = (int)($_POST['id'] ?? 0);
                try {
                    $db->delete('form_fields', 'id = ?', [$id]);
                    $success = 'Field deleted.';
                } catch (Exception $e) {
                    $error = 'Error: ' . $e->getMessage();
                }
                break;
                
            case 'reorder':
                $orders = json_decode($_POST['orders'] ?? '[]', true);
                foreach ($orders as $id => $order) {
                    $db->update('form_fields', ['sort_order' => (int)$order], 'id = ?', [(int)$id]);
                }
                $success = 'Order updated.';
                break;
        }
    }
}

// Get fields
$fields = $db->fetchAll("SELECT * FROM form_fields WHERE form_id = ? ORDER BY sort_order ASC", [$formId]);

$fieldTypes = [
    'text' => 'Text',
    'email' => 'Email',
    'tel' => 'Phone',
    'number' => 'Number',
    'textarea' => 'Textarea',
    'select' => 'Dropdown',
    'checkbox' => 'Checkbox',
    'radio' => 'Radio Buttons',
    'date' => 'Date',
    'url' => 'URL',
    'hidden' => 'Hidden'
];

$pageTitle = 'Form Fields - ' . $form['name'];
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">
            <a href="forms.php" style="text-decoration: none; color: inherit;">Forms</a> / <?= e($form['name']) ?>
        </h1>
        <p class="page-subtitle">Manage fields for this form</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="forms.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <button class="btn btn-primary" onclick="showFieldModal()">
            <i class="fas fa-plus"></i> Add Field
        </button>
    </div>
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
            <table class="data-table" id="fieldsTable">
                <thead>
                    <tr>
                        <th width="30"><i class="fas fa-grip-vertical"></i></th>
                        <th>Label</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Required</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="sortableFields">
                    <?php if (empty($fields)): ?>
                    <tr id="noFieldsRow">
                        <td colspan="6" class="text-center" style="padding: 30px;">
                            <div class="empty-state">
                                <i class="fas fa-list"></i>
                                <h3>No Fields Yet</h3>
                                <p>Add fields to build your form structure.</p>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($fields as $field): ?>
                    <tr data-id="<?= $field['id'] ?>">
                        <td class="drag-handle"><i class="fas fa-grip-vertical"></i></td>
                        <td><strong><?= e($field['label']) ?></strong></td>
                        <td><code><?= e($field['name']) ?></code></td>
                        <td><?= e($fieldTypes[$field['type']] ?? $field['type']) ?></td>
                        <td>
                            <?= $field['is_required'] ? '<span class="status-badge status-published">Yes</span>' : '<span class="status-badge" style="background: #f5f5f5; color: #666;">No</span>' ?>
                        </td>
                        <td>
                            <div class="table-actions">
                                <button type="button" onclick="editField(<?= e(json_encode($field)) ?>)" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="delete" onclick="deleteField(<?= $field['id'] ?>)" title="Delete">
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

<!-- Field Modal -->
<div id="fieldModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Add Field</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form method="POST" id="fieldForm">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <input type="hidden" name="action" id="fieldAction" value="create">
            <input type="hidden" name="id" id="fieldId" value="0">
            
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label for="label">Field Label *</label>
                        <input type="text" id="label" name="label" required placeholder="e.g., Your Name">
                    </div>
                    <div class="form-group">
                        <label for="name">Field Name</label>
                        <input type="text" id="name" name="name" placeholder="Auto-generated">
                        <small class="form-hint">Used in form data. Only letters, numbers, underscores.</small>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="type">Field Type</label>
                        <select id="type" name="type" onchange="toggleOptionsField()">
                            <?php foreach ($fieldTypes as $value => $label): ?>
                            <option value="<?= $value ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="placeholder">Placeholder</label>
                        <input type="text" id="placeholder" name="placeholder" placeholder="Placeholder text">
                    </div>
                </div>
                
                <div class="form-group" id="optionsGroup" style="display: none;">
                    <label for="options">Options (one per line)</label>
                    <textarea id="options" name="options" rows="4" placeholder="Option 1&#10;Option 2&#10;Option 3"></textarea>
                    <small class="form-hint">Enter each option on a new line.</small>
                </div>
                
                <div class="form-group">
                    <label for="default_value">Default Value</label>
                    <input type="text" id="default_value" name="default_value" placeholder="Optional default value">
                </div>
                
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="is_required" name="is_required">
                        This field is required
                    </label>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Field</button>
            </div>
        </form>
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" id="deleteId">
</form>

<form id="reorderForm" method="POST" style="display: none;">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
    <input type="hidden" name="action" value="reorder">
    <input type="hidden" name="orders" id="reorderData">
</form>

<style>
.back-link {
    color: #666;
    text-decoration: none;
    font-size: 14px;
    display: inline-block;
    margin-bottom: 8px;
}
.back-link:hover { color: #000; }
.drag-handle {
    cursor: grab;
    color: #aaa;
}
.drag-handle:hover { color: #333; }
code {
    background: #f5f5f5;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 13px;
}
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
.modal.active { display: flex; }
.modal-content {
    background: #fff;
    border-radius: 12px;
    width: 100%;
    max-width: 550px;
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
.modal-header h3 { margin: 0; }
.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
}
.modal-body { padding: 20px; }
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
.form-hint {
    display: block;
    margin-top: 4px;
    font-size: 12px;
    color: #888;
}
.checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}
.checkbox-label input { width: auto; }
.sortable-ghost {
    background: #f0f0f0;
    opacity: 0.5;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
// Initialize sortable
const sortable = new Sortable(document.getElementById('sortableFields'), {
    handle: '.drag-handle',
    animation: 150,
    ghostClass: 'sortable-ghost',
    onEnd: function() {
        const orders = {};
        document.querySelectorAll('#sortableFields tr[data-id]').forEach((row, index) => {
            orders[row.dataset.id] = index + 1;
        });
        document.getElementById('reorderData').value = JSON.stringify(orders);
        document.getElementById('reorderForm').submit();
    }
});

function showFieldModal() {
    document.getElementById('modalTitle').textContent = 'Add Field';
    document.getElementById('fieldAction').value = 'create';
    document.getElementById('fieldId').value = '0';
    document.getElementById('fieldForm').reset();
    toggleOptionsField();
    document.getElementById('fieldModal').classList.add('active');
}

function editField(field) {
    document.getElementById('modalTitle').textContent = 'Edit Field';
    document.getElementById('fieldAction').value = 'update';
    document.getElementById('fieldId').value = field.id;
    document.getElementById('label').value = field.label;
    document.getElementById('name').value = field.name;
    document.getElementById('type').value = field.type;
    document.getElementById('placeholder').value = field.placeholder || '';
    document.getElementById('default_value').value = field.default_value || '';
    document.getElementById('options').value = field.options || '';
    document.getElementById('is_required').checked = field.is_required == 1;
    toggleOptionsField();
    document.getElementById('fieldModal').classList.add('active');
}

function closeModal() {
    document.getElementById('fieldModal').classList.remove('active');
}

function deleteField(id) {
    if (confirm('Delete this field?')) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteForm').submit();
    }
}

function toggleOptionsField() {
    const type = document.getElementById('type').value;
    const optionsGroup = document.getElementById('optionsGroup');
    optionsGroup.style.display = ['select', 'radio', 'checkbox'].includes(type) ? 'block' : 'none';
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
document.getElementById('fieldModal').addEventListener('click', e => { if (e.target.classList.contains('modal')) closeModal(); });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
