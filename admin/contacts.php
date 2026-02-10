<?php
/**
 * Evolvcode CMS - Admin Contact Submissions
 */

$pageTitle = 'Contact Messages';
require_once __DIR__ . '/includes/header.php';
Auth::requirePermission('contacts');

$db = Database::getInstance();
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$message = '';
$error = '';

// Handle delete
if ($action === 'delete' && $id > 0) {
    $token = $_GET['token'] ?? '';
    if (Security::validateCSRFToken($token)) {
        $db->delete('contact_submissions', 'id = ?', [$id]);
        $message = 'Message deleted successfully.';
    }
    $action = 'list';
}

// Handle mark as read
if ($action === 'read' && $id > 0) {
    $db->update('contact_submissions', ['is_read' => 1], 'id = ?', [$id]);
    $action = 'view';
}

// Handle bulk action
if (Security::isPost() && isset($_POST['bulk_action'])) {
    $token = $_POST[CSRF_TOKEN_NAME] ?? '';
    if (Security::validateCSRFToken($token) && !empty($_POST['selected'])) {
        $ids = array_map('intval', $_POST['selected']);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        
        if ($_POST['bulk_action'] === 'delete') {
            $db->query("DELETE FROM contact_submissions WHERE id IN ({$placeholders})", $ids);
            $message = 'Selected messages deleted.';
        } elseif ($_POST['bulk_action'] === 'read') {
            $db->query("UPDATE contact_submissions SET is_read = 1 WHERE id IN ({$placeholders})", $ids);
            $message = 'Selected messages marked as read.';
        }
    }
}

// Pagination
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$page = max(1, $page);
$limit = 20;
$offset = ($page - 1) * $limit;

// Get contacts
$total = $db->count('contact_submissions');
$contacts = $db->fetchAll(
    "SELECT * FROM contact_submissions ORDER BY created_at DESC LIMIT ? OFFSET ?",
    [$limit, $offset]
);

// Get single contact for view
$contact = null;
if ($action === 'view' && $id > 0) {
    $contact = $db->fetchOne("SELECT * FROM contact_submissions WHERE id = ?", [$id]);
    if (!$contact) {
        $action = 'list';
        $error = 'Message not found.';
    } else {
        // Mark as read
        if (!$contact['is_read']) {
            $db->update('contact_submissions', ['is_read' => 1], 'id = ?', [$id]);
            $contact['is_read'] = 1;
        }
    }
}
?>

<?php if ($action === 'list'): ?>
<!-- List View -->
<div class="page-header">
    <div>
        <h1 class="page-title">Contact Messages</h1>
        <p class="page-subtitle"><?= $total ?> total messages</p>
    </div>
</div>

<?php if ($message): ?>
<div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= e($message) ?></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= e($error) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <?php if (!empty($contacts)): ?>
        <form method="POST" action="">
            <?= Security::csrfField() ?>
            
            <div style="padding: 16px; border-bottom: 1px solid var(--color-gray-200); display: flex; gap: 12px; align-items: center;">
                <select name="bulk_action" class="form-control" style="width: auto;">
                    <option value="">Bulk Actions</option>
                    <option value="read">Mark as Read</option>
                    <option value="delete">Delete</option>
                </select>
                <button type="submit" class="btn btn-secondary btn-sm">Apply</button>
            </div>
            
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" id="select-all">
                            </th>
                            <th>From</th>
                            <th>Subject</th>
                            <th>Date</th>
                            <th style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contacts as $item): ?>
                        <tr style="<?= !$item['is_read'] ? 'background: #FFF8E1;' : '' ?>">
                            <td>
                                <input type="checkbox" name="selected[]" value="<?= $item['id'] ?>">
                            </td>
                            <td>
                                <strong <?= !$item['is_read'] ? 'style="font-weight: 700;"' : '' ?>>
                                    <?= e($item['name']) ?>
                                </strong>
                                <div style="font-size: 12px; color: var(--color-gray-500);">
                                    <?= e($item['email']) ?>
                                    <?php if ($item['phone']): ?>
                                    | <?= e($item['phone']) ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <a href="?action=view&id=<?= $item['id'] ?>" style="color: inherit;">
                                    <?= e($item['subject'] ?: 'No subject') ?>
                                </a>
                                <div style="font-size: 12px; color: var(--color-gray-500);">
                                    <?= e(createExcerpt($item['message'], 60)) ?>
                                </div>
                            </td>
                            <td style="font-size: 12px; color: var(--color-gray-500);">
                                <?= formatDate($item['created_at'], 'M j, Y g:i A') ?>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="?action=view&id=<?= $item['id'] ?>" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="mailto:<?= e($item['email']) ?>" title="Reply">
                                        <i class="fas fa-reply"></i>
                                    </a>
                                    <a href="?action=delete&id=<?= $item['id'] ?>&token=<?= e(Security::generateCSRFToken()) ?>" 
                                       class="delete" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </form>
        
        <!-- Pagination -->
        <?php if ($total > $limit): ?>
        <div style="padding: 20px;">
            <?= paginate($total, $page, $limit, ADMIN_URL . '/contacts.php?') ?>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-envelope-open"></i>
            <h3>No Messages Yet</h3>
            <p>When visitors submit the contact form, their messages will appear here.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Select all checkbox
document.getElementById('select-all')?.addEventListener('change', function() {
    document.querySelectorAll('input[name="selected[]"]').forEach(cb => {
        cb.checked = this.checked;
    });
});
</script>

<?php elseif ($action === 'view'): ?>
<!-- View Message -->
<div class="page-header">
    <div>
        <h1 class="page-title">View Message</h1>
        <p class="page-subtitle">
            <a href="?action=list"><i class="fas fa-arrow-left"></i> Back to Messages</a>
        </p>
    </div>
    <div style="display: flex; gap: 12px;">
        <a href="mailto:<?= e($contact['email']) ?>" class="btn btn-primary">
            <i class="fas fa-reply"></i> Reply
        </a>
        <a href="?action=delete&id=<?= $contact['id'] ?>&token=<?= e(Security::generateCSRFToken()) ?>" 
           class="btn btn-danger delete">
            <i class="fas fa-trash"></i> Delete
        </a>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
    <!-- Message Content -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><?= e($contact['subject'] ?: 'No Subject') ?></h3>
        </div>
        <div class="card-body">
            <div style="white-space: pre-wrap; line-height: 1.7;">
<?= e($contact['message']) ?>
            </div>
        </div>
    </div>
    
    <!-- Contact Info -->
    <div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Contact Details</h3>
            </div>
            <div class="card-body">
                <dl style="display: flex; flex-direction: column; gap: 16px;">
                    <div>
                        <dt style="font-size: 12px; color: var(--color-gray-500); margin-bottom: 4px;">Name</dt>
                        <dd style="font-weight: 500;"><?= e($contact['name']) ?></dd>
                    </div>
                    <div>
                        <dt style="font-size: 12px; color: var(--color-gray-500); margin-bottom: 4px;">Email</dt>
                        <dd>
                            <a href="mailto:<?= e($contact['email']) ?>"><?= e($contact['email']) ?></a>
                        </dd>
                    </div>
                    <?php if ($contact['phone']): ?>
                    <div>
                        <dt style="font-size: 12px; color: var(--color-gray-500); margin-bottom: 4px;">Phone</dt>
                        <dd>
                            <a href="tel:<?= e($contact['phone']) ?>"><?= e($contact['phone']) ?></a>
                        </dd>
                    </div>
                    <?php endif; ?>
                    <div>
                        <dt style="font-size: 12px; color: var(--color-gray-500); margin-bottom: 4px;">Received</dt>
                        <dd><?= formatDate($contact['created_at'], 'F j, Y \a\t g:i A') ?></dd>
                    </div>
                    <div>
                        <dt style="font-size: 12px; color: var(--color-gray-500); margin-bottom: 4px;">IP Address</dt>
                        <dd style="font-family: monospace; font-size: 13px;">
                            <?= e($contact['ip_address'] ?: 'Unknown') ?>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>

<style>
@media (max-width: 1024px) {
    div[style*="grid-template-columns: 2fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}
</style>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
