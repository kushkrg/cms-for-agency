<?php
/**
 * Evolvcode CMS - Menu Management
 */

$pageTitle = 'Menu Management';
require_once __DIR__ . '/includes/header.php';
Auth::requirePermission('menus');

$db = Database::getInstance();
$message = '';
$error = '';

// Get current menu location
$location = $_GET['location'] ?? 'header';
$menu = $db->fetchOne("SELECT * FROM menus WHERE location = ?", [$location]);

if (!$menu) {
    // Fallback or create if missing (though seed should handle it)
    $menu = ['id' => 0, 'name' => 'Unknown Menu', 'location' => $location];
}

// Handle AJAX Request for Updates
if (isset($_POST['action']) && $_POST['action'] === 'update_order') {
    header('Content-Type: application/json');
    $order = json_decode($_POST['order'], true);
    
    if (is_array($order)) {
        foreach ($order as $index => $itemId) {
            $db->update('menu_items', ['sort_order' => $index], 'id = ?', [$itemId]);
        }
        echo json_encode(['success' => true, 'message' => 'Order updated']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
    }
    exit;
}

// Handle Form Submissions
if (Security::isPost()) {
    $token = $_POST[CSRF_TOKEN_NAME] ?? '';
    
    if (isset($_POST['add_item'])) {
        if (!Security::validateCSRFToken($token)) {
            $error = 'Invalid token.';
        } else {
            $title = Security::clean($_POST['title']);
            $url = Security::clean($_POST['url']);
            $target = Security::clean($_POST['target'] ?? '_self');
            $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
            
            if ($title && $url) {
                // Get next sort order
                $maxSort = $db->fetchOne("SELECT MAX(sort_order) as max_sort FROM menu_items WHERE menu_id = ? AND parent_id " . ($parentId ? "= ?" : "IS NULL"), 
                    $parentId ? [$menu['id'], $parentId] : [$menu['id']]
                );
                $sort = ($maxSort['max_sort'] ?? -1) + 1;
                
                $db->insert('menu_items', [
                    'menu_id' => $menu['id'],
                    'parent_id' => $parentId,
                    'title' => $title,
                    'url' => $url,
                    'target' => $target,
                    'sort_order' => $sort,
                    'status' => 'active'
                ]);
                $message = 'Menu item added.';
                $success = true;
            } else {
                $error = 'Title and URL are required.';
            }
        }
    } elseif (isset($_POST['delete_item'])) {
         if (!Security::validateCSRFToken($token)) {
            $error = 'Invalid token.';
        } else {
            $itemId = (int) $_POST['item_id'];
            
            // Recursive delete function (Move to top or define here)
            // Using a lambda or just logic here since it's simple
            // But PHP functions inside if blocks are tricky if not careful
            // Better to fetch all descendants first then delete
            
            // Get all descendants
            $idsToDelete = [$itemId];
            $toProcess = [$itemId];
            
            while (!empty($toProcess)) {
                $currentId = array_shift($toProcess);
                $children = $db->fetchAll("SELECT id FROM menu_items WHERE parent_id = ?", [$currentId]);
                foreach ($children as $child) {
                    $toProcess[] = $child['id'];
                    $idsToDelete[] = $child['id'];
                }
            }
            
            // Delete all (children first to avoid constraints if any)
            // If using IN clause, MySQL usually handles it but to be safe reverse
            if (!empty($idsToDelete)) {
                $idsToDelete = array_reverse($idsToDelete);
                $placeholders = implode(',', array_fill(0, count($idsToDelete), '?'));
                $db->query("DELETE FROM menu_items WHERE id IN ($placeholders)", $idsToDelete);
            }
            
            $message = 'Menu item deleted successfully.';
            $success = true; // Flag for SweetAlert
        }
    } elseif (isset($_POST['edit_item'])) {
         if (!Security::validateCSRFToken($token)) {
            $error = 'Invalid token.';
        } else {
            $itemId = (int) $_POST['item_id'];
            $title = Security::clean($_POST['title']);
            $url = Security::clean($_POST['url']);
            $target = Security::clean($_POST['target']);
            $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
            
            // Prevent self-parenting
            if ($parentId === $itemId) {
                $parentId = null;
            }
            
            $db->update('menu_items', [
                'parent_id' => $parentId,
                'title' => $title,
                'url' => $url,
                'target' => $target
            ], 'id = ?', [$itemId]);
            $message = 'Menu item updated.';
            $success = true;
        }
    }
}

// Fetch All Items
$rawItems = $db->fetchAll("SELECT * FROM menu_items WHERE menu_id = ? ORDER BY sort_order ASC", [$menu['id']]);

// Build Tree
$items = [];
$children = [];
foreach ($rawItems as $item) {
    if ($item['parent_id']) {
        $children[$item['parent_id']][] = $item;
    } else {
        $items[] = $item;
    }
}
// Attach children to parents
foreach ($items as &$item) {
    if (isset($children[$item['id']])) {
        $item['children'] = $children[$item['id']];
    }
}
unset($item);
?>

<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
        <div>
            <h1 class="page-title">Menu Management</h1>
            <p class="page-subtitle">Manage your website navigation menus.</p>
        </div>
        <div>
            <div class="btn-group">
                <a href="?location=header" class="btn <?= $location === 'header' ? 'btn-primary' : 'btn-white' ?>">Header Menu</a>
                <a href="?location=footer" class="btn <?= $location === 'footer' ? 'btn-primary' : 'btn-white' ?>">Footer Menu</a>
            </div>
        </div>
    </div>
</div>

<?php if ($message && !isset($success)): ?>
<div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= e($message) ?></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= e($error) ?></div>
<?php endif; ?>

<div class="grid grid-3" style="grid-template-columns: 2fr 1fr; gap: 24px;">
    <!-- Menu Items List -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Menu Structure: <?= e($menu['name']) ?></h3>
             <small class="text-gray-500">Drag and drop to reorder items.</small>
        </div>
        <div class="card-body p-0">
            <?php if (empty($items)): ?>
            <div class="p-4 text-center text-gray-500">
                No items in this menu. Add one securely.
            </div>
            <?php else: ?>
            <ul class="menu-list" id="sortable-menu">
                <?php foreach ($items as $item): ?>
                <li class="menu-item" data-id="<?= $item['id'] ?>">
                    <div class="menu-item-handle">
                        <i class="fas fa-grip-vertical"></i>
                    </div>
                    <div class="menu-item-content">
                        <strong><?= e($item['title']) ?></strong>
                        <small><?= e($item['url']) ?></small>
                    </div>
                    <div class="menu-item-actions">
                        <button type="button" class="btn-icon edit-item-btn" 
                                data-item='<?= htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8') ?>'>
                            <i class="fas fa-edit"></i>
                        </button>
                        <form method="POST" action="" onsubmit="return confirmDelete(event, this);" style="display: inline;">
                            <?= Security::csrfField() ?>
                            <input type="hidden" name="delete_item" value="1">
                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                            <button type="submit" class="btn-icon text-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </li>
                <!-- Render Children -->
                <?php if (!empty($item['children'])): ?>
                    <?php foreach ($item['children'] as $child): ?>
                    <li class="menu-item child-item" data-id="<?= $child['id'] ?>">
                        <div class="menu-item-handle">
                            <i class="fas fa-grip-vertical"></i>
                        </div>
                        <div style="color: var(--color-gray-400); margin-right: 10px;">
                            <i class="fas fa-level-up-alt fa-rotate-90"></i>
                        </div>
                        <div class="menu-item-content">
                            <strong><?= e($child['title']) ?></strong>
                            <small><?= e($child['url']) ?></small>
                        </div>
                        <div class="menu-item-actions">
                            <button type="button" class="btn-icon edit-item-btn" 
                                    data-item='<?= htmlspecialchars(json_encode($child), ENT_QUOTES, 'UTF-8') ?>'>
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" action="" onsubmit="return confirmDelete(event, this);" style="display: inline;">
                                <?= Security::csrfField() ?>
                                <input type="hidden" name="delete_item" value="1">
                                <input type="hidden" name="item_id" value="<?= $child['id'] ?>">
                                <button type="submit" class="btn-icon text-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </li>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Item Form -->
    <div class="card h-fit" id="menu-form-card">
        <div class="card-header">
            <h3 class="card-title" id="form-title">Add Menu Item</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="" id="menu-form">
                <?= Security::csrfField() ?>
                <input type="hidden" name="add_item" value="1" id="action-input">
                <input type="hidden" name="item_id" value="" id="item-id-input">
                
                <div class="form-group">
                    <label class="form-label">Link Text</label>
                    <input type="text" name="title" id="title-input" class="form-control" placeholder="e.g. Home" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">URL</label>
                    <input type="text" name="url" id="url-input" class="form-control" placeholder="e.g. /about" required>
                    <small class="form-text">Use relative paths (e.g. /about) for internal links.</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Parent Item (Optional)</label>
                    <select name="parent_id" id="parent-id-input" class="form-control">
                        <option value="">-- No Parent (Top Level) --</option>
                        <?php foreach ($items as $pItem): ?>
                        <option value="<?= $pItem['id'] ?>"><?= e($pItem['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Open In</label>
                    <select name="target" id="target-input" class="form-control">
                        <option value="_self">Same Tab</option>
                        <option value="_blank">New Tab</option>
                    </select>
                </div>
                
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary w-100" id="submit-btn">
                        <i class="fas fa-plus"></i> Add to Menu
                    </button>
                    <button type="button" class="btn btn-white w-100 mt-2" id="cancel-btn" style="display: none;" onclick="resetForm()">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SortableJS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>

<style>
.menu-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.menu-item {
    display: flex;
    align-items: center;
    padding: 16px;
    border: 1px solid var(--color-gray-200);
    margin-bottom: 8px;
    border-radius: 6px;
    background: white;
    transition: all 0.2s;
}
.menu-item:hover {
    border-color: var(--color-gray-300);
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}
.menu-item-handle {
    color: var(--color-gray-400);
    cursor: grab;
    padding: 8px;
    margin-right: 12px;
    border-radius: 4px;
}
.menu-item-handle:hover {
    background: var(--color-gray-100);
    color: var(--color-gray-600);
}
.menu-item-content {
    flex: 1;
}
.menu-item-content strong {
    display: block;
    font-size: 0.95rem;
    color: var(--color-gray-900);
    margin-bottom: 2px;
}
.menu-item-content small {
    color: var(--color-gray-500);
    font-size: 0.85rem;
    font-family: monospace;
    background: var(--color-gray-50);
    padding: 2px 6px;
    border-radius: 4px;
}
.menu-item-actions {
    display: flex;
    gap: 8px;
    opacity: 0.6;
    transition: opacity 0.2s;
}
.menu-item:hover .menu-item-actions {
    opacity: 1;
}
.btn-icon {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    color: var(--color-gray-500);
    border: 1px solid var(--color-gray-200);
    background: white;
    transition: all 0.2s;
    cursor: pointer;
}
.btn-icon:hover {
    background: var(--color-gray-50);
    color: var(--color-primary);
    border-color: var(--color-primary);
}
.btn-icon.text-danger:hover {
    background: #fee2e2;
    color: #ef4444;
    border-color: #fca5a5;
}
.btn-group {
    display: inline-flex;
    background: var(--color-gray-100);
    padding: 4px;
    border-radius: 8px;
}
.btn-group .btn {
    border: none;
    margin: 0;
    padding: 8px 20px; 
    font-size: 0.9rem;
    border-radius: 6px;
}
.btn-group .btn-white {
    background: transparent;
    color: var(--color-gray-600);
    box-shadow: none;
}
.btn-group .btn-white:hover {
    background: rgba(255,255,255,0.5);
    color: var(--color-gray-900);
}
.btn-group .btn-primary {
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}
.sortable-ghost {
    opacity: 0.4;
    background: var(--color-gray-100);
    border: 1px dashed var(--color-gray-400);
}
.h-fit {
    height: fit-content;
    position: sticky;
    top: 24px;
}
.child-item {
    margin-left: 32px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // SortableJS Inspection
    const el = document.getElementById('sortable-menu');
    if (el) {
        new Sortable(el, {
            handle: '.menu-item-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function (evt) {
                // Get new order
                const items = el.querySelectorAll('.menu-item');
                const order = [];
                items.forEach((item, index) => {
                    order.push(item.dataset.id);
                });
                
                // Send to server
                const formData = new FormData();
                formData.append('action', 'update_order');
                formData.append('order', JSON.stringify(order));
                // Add CSRF token
                const csrfToken = document.querySelector('input[name="csrf_token"]').value;
                formData.append('csrf_token', csrfToken);
                
                fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Optional: Show a toast notification
                        console.log('Order updated');
                    } else {
                        alert('Failed to save order: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(err => console.error(err));
            }
        });
    }

    // Edit Item Handler (Event Delegation)
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.edit-item-btn');
        if (btn) {
            const itemData = btn.dataset.item;
            try {
                const item = JSON.parse(itemData);
                editItem(item);
                // Scroll to form
                document.getElementById('menu-form-card').scrollIntoView({ behavior: 'smooth' });
            } catch (err) {
                console.error('Error parsing item data', err);
            }
        }
    });
});

function editItem(item) {
    document.getElementById('form-title').innerText = 'Edit Menu Item';
    document.getElementById('action-input').name = 'edit_item';
    document.getElementById('item-id-input').value = item.id;
    document.getElementById('title-input').value = item.title;
    document.getElementById('url-input').value = item.url;
    document.getElementById('target-input').value = item.target;
    
    // Set parent ID if exists
    const parentInput = document.getElementById('parent-id-input');
    if (parentInput) {
        parentInput.value = item.parent_id || "";
        // Disable self-selection (simplistic approach)
        Array.from(parentInput.options).forEach(opt => {
            opt.disabled = (opt.value == item.id);
        });
    }
    
    const submitBtn = document.getElementById('submit-btn');
    submitBtn.innerHTML = '<i class="fas fa-save"></i> Save Changes';
    submitBtn.classList.remove('btn-primary');
    submitBtn.classList.add('btn-success'); // Visual cue for edit mode
    
    document.getElementById('cancel-btn').style.display = 'block';
}

function resetForm() {
    document.getElementById('form-title').innerText = 'Add Menu Item';
    document.getElementById('menu-form').reset();
    document.getElementById('action-input').name = 'add_item';
    document.getElementById('item-id-input').value = '';
    
    // Reset parent input
    const parentInput = document.getElementById('parent-id-input');
    if (parentInput) {
        parentInput.value = "";
        Array.from(parentInput.options).forEach(opt => {
            opt.disabled = false;
        });
    }
    
    const submitBtn = document.getElementById('submit-btn');
    submitBtn.innerHTML = '<i class="fas fa-plus"></i> Add to Menu';
    submitBtn.classList.add('btn-primary');
    submitBtn.classList.remove('btn-success');
    
    document.getElementById('cancel-btn').style.display = 'none';
}

function confirmDelete(e, form) {
    e.preventDefault();
    
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
    return false;
}
</script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (isset($success) && $success): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'success',
        title: 'Deleted!',
        text: '<?= addslashes($message) ?>',
        timer: 2000,
        showConfirmButton: false
    });
});
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
