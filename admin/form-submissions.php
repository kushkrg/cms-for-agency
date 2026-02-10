<?php
/**
 * Evolvcode CMS - Admin Form Submissions
 * 
 * View and manage form submissions.
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Auth.php';

Auth::requireLogin();
Auth::requirePermission('submissions');

$db = Database::getInstance();
$error = '';
$success = '';

// Get form ID (optional filter)
$formId = (int)($_GET['form_id'] ?? 0);
$statusFilter = $_GET['status'] ?? '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $action = $_POST['action'] ?? '';
        $id = (int)($_POST['id'] ?? 0);
        
        switch ($action) {
            case 'mark_read':
                $db->update('form_submissions', ['status' => 'read'], ['id' => $id]);
                $success = 'Marked as read.';
                break;
            case 'mark_replied':
                $db->update('form_submissions', ['status' => 'replied'], ['id' => $id]);
                $success = 'Marked as replied.';
                break;
            case 'archive':
                $db->update('form_submissions', ['status' => 'archived'], ['id' => $id]);
                $success = 'Archived.';
                break;
            case 'delete':
                $db->delete('form_submissions', ['id' => $id]);
                $success = 'Deleted.';
                break;
            case 'bulk_delete':
                $ids = $_POST['ids'] ?? [];
                if (!empty($ids)) {
                    $placeholders = implode(',', array_fill(0, count($ids), '?'));
                    $db->query("DELETE FROM form_submissions WHERE id IN ($placeholders)", array_map('intval', $ids));
                    $success = 'Deleted ' . count($ids) . ' submissions.';
                }
                break;
        }
    }
}

// Build query
$where = [];
$params = [];

if ($formId > 0) {
    $where[] = 'fs.form_id = ?';
    $params[] = $formId;
}

if ($statusFilter && in_array($statusFilter, ['unread', 'read', 'replied', 'archived'])) {
    $where[] = 'fs.status = ?';
    $params[] = $statusFilter;
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Get submissions with pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

$totalCount = $db->fetch("SELECT COUNT(*) as count FROM form_submissions fs $whereClause", $params)['count'];
$totalPages = ceil($totalCount / $perPage);

$submissions = $db->fetchAll("
    SELECT fs.*, f.name as form_name 
    FROM form_submissions fs 
    LEFT JOIN forms f ON fs.form_id = f.id 
    $whereClause 
    ORDER BY fs.created_at DESC 
    LIMIT $perPage OFFSET $offset
", $params);

// Get forms for filter dropdown
$forms = $db->fetchAll("SELECT id, name FROM forms ORDER BY name");

$pageTitle = 'Form Submissions';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Form Submissions</h1>
        <p class="page-subtitle">View and manage contact requests and form data</p>
    </div>
    <div class="header-actions">
        <form method="GET" class="filter-form" style="display: flex; gap: 10px;">
            <select name="form_id" onchange="this.form.submit()" class="form-control" style="width: auto;">
                <option value="">All Forms</option>
                <?php foreach ($forms as $form): ?>
                <option value="<?= $form['id'] ?>" <?= $formId == $form['id'] ? 'selected' : '' ?>>
                    <?= e($form['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <select name="status" onchange="this.form.submit()" class="form-control" style="width: auto;">
                <option value="">All Status</option>
                <option value="unread" <?= $statusFilter === 'unread' ? 'selected' : '' ?>>Unread</option>
                <option value="read" <?= $statusFilter === 'read' ? 'selected' : '' ?>>Read</option>
                <option value="replied" <?= $statusFilter === 'replied' ? 'selected' : '' ?>>Replied</option>
                <option value="archived" <?= $statusFilter === 'archived' ? 'selected' : '' ?>>Archived</option>
            </select>
        </form>
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
        <form method="POST" id="bulkForm">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="action" value="bulk_delete">
        
        <div class="table-actions" style="margin-bottom: 15px; display: none;" id="bulkActions">
            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete selected submissions?')">
                <i class="fas fa-trash"></i> Delete Selected
            </button>
        </div>
        
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                <tr>
                    <th width="30">
                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                    </th>
                    <th>Form</th>
                    <th>Contact Info</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($submissions)): ?>
                <tr>
                    <td colspan="7" class="text-center">No submissions found.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($submissions as $sub): ?>
                <?php $data = json_decode($sub['data'], true) ?: []; ?>
                <tr class="<?= $sub['status'] === 'unread' ? 'unread-row' : '' ?>">
                    <td>
                        <input type="checkbox" name="ids[]" value="<?= $sub['id'] ?>" class="row-checkbox" onchange="updateBulkActions()">
                    </td>
                    <td>
                        <span class="form-badge"><?= e($sub['form_name'] ?? 'Unknown') ?></span>
                    </td>
                    <td>
                        <strong><?= e($data['name'] ?? 'N/A') ?></strong><br>
                        <a href="mailto:<?= e($data['email'] ?? '') ?>"><?= e($data['email'] ?? 'N/A') ?></a>
                        <?php if (!empty($data['phone'])): ?>
                        <br><small><?= e($data['phone']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($data['subject'])): ?>
                        <strong><?= e($data['subject']) ?></strong><br>
                        <?php endif; ?>
                        <span class="message-preview"><?= e(substr($data['message'] ?? '', 0, 100)) ?>...</span>
                    </td>
                    <td>
                        <span class="date-display"><?= date('M j, Y', strtotime($sub['created_at'])) ?></span><br>
                        <small><?= date('g:i A', strtotime($sub['created_at'])) ?></small>
                    </td>
                    <td>
                        <span class="status-badge status-<?= $sub['status'] ?>">
                            <?= ucfirst($sub['status']) ?>
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button type="button" class="btn btn-sm btn-secondary" onclick="viewSubmission(<?= e(json_encode($sub)) ?>, <?= e(json_encode($data)) ?>)" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm btn-secondary dropdown-toggle">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <button type="button" onclick="quickAction(<?= $sub['id'] ?>, 'mark_read')">Mark as Read</button>
                                    <button type="button" onclick="quickAction(<?= $sub['id'] ?>, 'mark_replied')">Mark as Replied</button>
                                    <button type="button" onclick="quickAction(<?= $sub['id'] ?>, 'archive')">Archive</button>
                                    <button type="button" onclick="quickAction(<?= $sub['id'] ?>, 'delete')" class="text-danger">Delete</button>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </form>
    
    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?>&form_id=<?= $formId ?>&status=<?= e($statusFilter) ?>" 
           class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
    </div>
</div>

<!-- View Modal -->
<div id="viewModal" class="modal">
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <h3>Submission Details</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body" id="submissionDetails"></div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal()">Close</button>
        </div>
    </div>
</div>

<!-- Quick Action Form -->
<form id="actionForm" method="POST" style="display: none;">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
    <input type="hidden" name="action" id="actionType">
    <input type="hidden" name="id" id="actionId">
</form>

<style>
.filter-form {
    display: flex;
    gap: 10px;
}
.filter-form select {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
}
.form-badge {
    background: #e0e0e0;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
}
.unread-row {
    background: #f8f9fa;
    font-weight: 500;
}
.message-preview {
    color: #666;
    font-size: 13px;
}
.status-unread { background: #fff3cd; color: #856404; }
.status-read { background: #d1ecf1; color: #0c5460; }
.status-replied { background: #d4edda; color: #155724; }
.status-archived { background: #e2e3e5; color: #383d41; }
.dropdown {
    position: relative;
    display: inline-block;
}
.dropdown-menu {
    display: none;
    position: absolute;
    right: 0;
    top: 100%;
    background: white;
    border: 1px solid #ddd;
    border-radius: 6px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 100;
    min-width: 150px;
}
.dropdown:hover .dropdown-menu {
    display: block;
}
.dropdown-menu button {
    display: block;
    width: 100%;
    padding: 10px 15px;
    border: none;
    background: none;
    text-align: left;
    cursor: pointer;
}
.dropdown-menu button:hover {
    background: #f5f5f5;
}
.text-danger { color: #dc3545; }
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
    max-height: 90vh;
    overflow-y: auto;
}
.modal-lg {
    width: 100%;
    max-width: 700px;
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
    text-align: right;
}
.detail-row {
    display: grid;
    grid-template-columns: 120px 1fr;
    gap: 10px;
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}
.detail-label {
    font-weight: 600;
    color: #666;
}
.pagination {
    display: flex;
    gap: 5px;
    justify-content: center;
    padding: 20px;
}
.pagination a {
    padding: 8px 12px;
    border: 1px solid #ddd;
    text-decoration: none;
    color: #333;
    border-radius: 4px;
}
.pagination a.active {
    background: #000;
    color: #fff;
    border-color: #000;
}
</style>

<script>
function toggleSelectAll() {
    const checked = document.getElementById('selectAll').checked;
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = checked);
    updateBulkActions();
}

function updateBulkActions() {
    const anyChecked = document.querySelectorAll('.row-checkbox:checked').length > 0;
    document.getElementById('bulkActions').style.display = anyChecked ? 'block' : 'none';
}

function viewSubmission(sub, data) {
    let html = '<div class="submission-details">';
    html += '<div class="detail-row"><span class="detail-label">Form:</span><span>' + (sub.form_name || 'Unknown') + '</span></div>';
    html += '<div class="detail-row"><span class="detail-label">Status:</span><span class="status-badge status-' + sub.status + '">' + sub.status.charAt(0).toUpperCase() + sub.status.slice(1) + '</span></div>';
    html += '<div class="detail-row"><span class="detail-label">Date:</span><span>' + new Date(sub.created_at).toLocaleString() + '</span></div>';
    html += '<div class="detail-row"><span class="detail-label">IP Address:</span><span>' + (sub.ip_address || 'N/A') + '</span></div>';
    html += '<hr style="margin: 15px 0;">';
    
    for (const [key, value] of Object.entries(data)) {
        const label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        let displayValue = value;
        
        if (typeof value === 'string') {
            if (value.startsWith('http')) {
                displayValue = `<a href="${value}" target="_blank" class="text-primary">View File <i class="fas fa-external-link-alt small"></i></a>`;
            } else {
                displayValue = value.replace(/\n/g, '<br>');
            }
        }
        
        html += '<div class="detail-row"><span class="detail-label">' + label + ':</span><span>' + (displayValue || '-') + '</span></div>';
    }
    html += '</div>';
    
    document.getElementById('submissionDetails').innerHTML = html;
    document.getElementById('viewModal').classList.add('active');
}

function closeModal() {
    document.getElementById('viewModal').classList.remove('active');
}

function quickAction(id, action) {
    if (action === 'delete' && !confirm('Delete this submission?')) return;
    document.getElementById('actionId').value = id;
    document.getElementById('actionType').value = action;
    document.getElementById('actionForm').submit();
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
