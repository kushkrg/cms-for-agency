<?php
$pageTitle = 'Subscribers';
require_once __DIR__ . '/includes/header.php';

$db = Database::getInstance();

// Handle Delete
if (isset($_POST['delete_id'])) {
    if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
         $error = 'Invalid token.';
    } else {
        $db->delete('subscribers', 'id = ?', [$_POST['delete_id']]);
        $message = 'Subscriber deleted successfully.';
    }
}

// Handle Export
if (isset($_GET['export'])) {
    if (ob_get_level()) ob_end_clean();
    
    $rows = $db->fetchAll("SELECT email, status, created_at FROM subscribers ORDER BY created_at DESC");
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="subscribers_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Email', 'Status', 'Date']);
    
    foreach ($rows as $row) {
        fputcsv($output, [
            $row['email'],
            ucfirst($row['status']),
            date('F j, Y, g:i a', strtotime($row['created_at']))
        ]);
    }
    
    fclose($output);
    exit;
}

// Pagination and Search
$search = $_GET['search'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

$params = [];
$whereClause = "";

if ($search) {
    $whereClause = "WHERE email LIKE ?";
    $params[] = "%$search%";
}

// Get total count
$countSql = "SELECT COUNT(*) as count FROM subscribers $whereClause";
$totalRow = $db->fetchOne($countSql, $params);
$total = $totalRow['count'] ?? 0;
$totalPages = max(1, ceil($total / $limit));

// Get records
$sql = "SELECT * FROM subscribers $whereClause ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$subscribers = $db->fetchAll($sql, $params);

?>

<style>
/* Subscriber Module Styles */
.sub-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}
.sub-header h1 {
    font-size: 24px;
    font-weight: 700;
    margin: 0;
    color: var(--color-gray-900);
}
.sub-header-actions {
    display: flex;
    gap: 10px;
}

/* Stats Row */
.sub-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}
.sub-stat-card {
    background: var(--color-white);
    border-radius: var(--radius-lg);
    padding: 20px 24px;
    border: 1px solid var(--color-gray-200);
}
.sub-stat-card .stat-label {
    font-size: 12px;
    font-weight: 500;
    color: var(--color-gray-500);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
}
.sub-stat-card .stat-value {
    font-size: 28px;
    font-weight: 700;
    color: var(--color-gray-900);
}
.sub-stat-card .stat-value.active { color: #16a34a; }
.sub-stat-card .stat-value.inactive { color: var(--color-gray-400); }

/* Search Bar */
.sub-search {
    background: var(--color-white);
    border-radius: var(--radius-lg);
    border: 1px solid var(--color-gray-200);
    padding: 16px 20px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 12px;
}
.sub-search .search-icon {
    color: var(--color-gray-400);
    font-size: 16px;
}
.sub-search input {
    flex: 1;
    border: none;
    outline: none;
    font-size: 14px;
    font-family: inherit;
    color: var(--color-gray-800);
    background: transparent;
}
.sub-search input::placeholder {
    color: var(--color-gray-400);
}
.sub-search .search-actions {
    display: flex;
    gap: 8px;
}

/* Table Card */
.sub-table-card {
    background: var(--color-white);
    border-radius: var(--radius-lg);
    border: 1px solid var(--color-gray-200);
    overflow: hidden;
}
.sub-table {
    width: 100%;
    border-collapse: collapse;
}
.sub-table thead th {
    padding: 14px 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--color-gray-500);
    background: var(--color-gray-50);
    border-bottom: 1px solid var(--color-gray-200);
    text-align: left;
}
.sub-table thead th:last-child {
    text-align: right;
}
.sub-table tbody tr {
    border-bottom: 1px solid var(--color-gray-100);
    transition: background 0.15s ease;
}
.sub-table tbody tr:last-child {
    border-bottom: none;
}
.sub-table tbody tr:hover {
    background: var(--color-gray-50);
}
.sub-table td {
    padding: 16px 20px;
    font-size: 14px;
    color: var(--color-gray-700);
    vertical-align: middle;
}
.sub-table td:last-child {
    text-align: right;
}
.sub-table .email-cell {
    font-weight: 500;
    color: var(--color-gray-900);
}
.sub-table .email-cell a {
    color: inherit;
    text-decoration: none;
}
.sub-table .email-cell a:hover {
    color: var(--color-black);
    text-decoration: underline;
}
.sub-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}
.sub-badge.active {
    background: #dcfce7;
    color: #166534;
}
.sub-badge.active::before {
    content: '';
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #16a34a;
}
.sub-badge.inactive {
    background: var(--color-gray-100);
    color: var(--color-gray-600);
}
.sub-date {
    color: var(--color-gray-500);
    font-size: 13px;
}
.sub-delete-btn {
    width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: var(--color-gray-100);
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-md);
    color: var(--color-gray-500);
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 13px;
}
.sub-delete-btn:hover {
    background: #fee2e2;
    border-color: #fecaca;
    color: #dc2626;
}

/* Empty State */
.sub-empty {
    text-align: center;
    padding: 60px 20px;
}
.sub-empty i {
    font-size: 48px;
    color: var(--color-gray-300);
    margin-bottom: 16px;
}
.sub-empty h3 {
    font-size: 16px;
    font-weight: 600;
    color: var(--color-gray-600);
    margin: 0 0 6px;
}
.sub-empty p {
    font-size: 13px;
    color: var(--color-gray-400);
    margin: 0;
}

/* Pagination */
.sub-pagination {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 20px;
    border-top: 1px solid var(--color-gray-100);
    font-size: 13px;
    color: var(--color-gray-500);
}
.sub-pagination .page-links {
    display: flex;
    gap: 4px;
}
.sub-pagination .page-link {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: var(--radius-md);
    text-decoration: none;
    color: var(--color-gray-700);
    font-weight: 500;
    transition: all 0.15s ease;
    border: 1px solid transparent;
}
.sub-pagination .page-link:hover {
    background: var(--color-gray-100);
}
.sub-pagination .page-link.current {
    background: var(--color-black);
    color: var(--color-white);
}
.sub-pagination .page-link.disabled {
    opacity: 0.3;
    pointer-events: none;
}
</style>

<!-- Header -->
<div class="sub-header">
    <h1><i class="fas fa-users" style="margin-right: 10px; opacity: 0.5;"></i> Subscribers</h1>
    <div class="sub-header-actions">
        <a href="?export=1" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-download"></i> Export CSV
        </a>
    </div>
</div>

<?php if (isset($message)): ?>
<div class="alert alert-success" style="margin-bottom: 16px;">
    <i class="fas fa-check-circle"></i> <?= e($message) ?>
</div>
<?php elseif (isset($error)): ?>
<div class="alert alert-error" style="margin-bottom: 16px;">
    <i class="fas fa-exclamation-circle"></i> <?= e($error) ?>
</div>
<?php endif; ?>

<!-- Stats -->
<?php
$totalAll = $db->fetchOne("SELECT COUNT(*) as c FROM subscribers")['c'] ?? 0;
$totalActive = $db->fetchOne("SELECT COUNT(*) as c FROM subscribers WHERE status = 'active'")['c'] ?? 0;
$totalInactive = $totalAll - $totalActive;
?>
<div class="sub-stats">
    <div class="sub-stat-card">
        <div class="stat-label">Total Subscribers</div>
        <div class="stat-value"><?= $totalAll ?></div>
    </div>
    <div class="sub-stat-card">
        <div class="stat-label">Active</div>
        <div class="stat-value active"><?= $totalActive ?></div>
    </div>
    <div class="sub-stat-card">
        <div class="stat-label">Unsubscribed</div>
        <div class="stat-value inactive"><?= $totalInactive ?></div>
    </div>
</div>

<!-- Search -->
<form method="GET" class="sub-search">
    <i class="fas fa-search search-icon"></i>
    <input type="text" name="search" value="<?= e($search) ?>" placeholder="Search by email address...">
    <div class="search-actions">
        <button type="submit" class="btn btn-primary" style="padding: 8px 16px; font-size: 13px;">Search</button>
        <?php if ($search): ?>
            <a href="subscribers.php" class="btn btn-secondary" style="padding: 8px 16px; font-size: 13px;">Clear</a>
        <?php endif; ?>
    </div>
</form>

<!-- Table -->
<div class="sub-table-card">
    <table class="sub-table">
        <thead>
            <tr>
                <th>Email Address</th>
                <th>Status</th>
                <th>Subscribed</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($subscribers)): ?>
            <tr>
                <td colspan="4">
                    <div class="sub-empty">
                        <i class="fas fa-inbox"></i>
                        <h3>No subscribers yet</h3>
                        <p>Subscribers will appear here when users sign up via the newsletter form.</p>
                    </div>
                </td>
            </tr>
            <?php else: ?>
                <?php foreach ($subscribers as $sub): ?>
                <tr>
                    <td class="email-cell">
                        <a href="mailto:<?= e($sub['email']) ?>"><?= e($sub['email']) ?></a>
                    </td>
                    <td>
                        <span class="sub-badge <?= $sub['status'] === 'active' ? 'active' : 'inactive' ?>">
                            <?= ucfirst($sub['status']) ?>
                        </span>
                    </td>
                    <td class="sub-date"><?= date('M j, Y \a\t g:i A', strtotime($sub['created_at'])) ?></td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Delete this subscriber?');" style="display:inline;">
                            <?= Security::csrfField() ?>
                            <input type="hidden" name="delete_id" value="<?= $sub['id'] ?>">
                            <button type="submit" class="sub-delete-btn" title="Delete subscriber">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <?php if ($totalPages > 1): ?>
    <div class="sub-pagination">
        <span>Showing <?= $offset + 1 ?>–<?= min($offset + $limit, $total) ?> of <?= $total ?> subscribers</span>
        <div class="page-links">
            <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>" class="page-link <?= $page <= 1 ? 'disabled' : '' ?>">
                <i class="fas fa-chevron-left"></i>
            </a>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" class="page-link <?= $page == $i ? 'current' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>" class="page-link <?= $page >= $totalPages ? 'disabled' : '' ?>">
                <i class="fas fa-chevron-right"></i>
            </a>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
