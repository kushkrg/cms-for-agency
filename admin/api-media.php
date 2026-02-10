<?php
/**
 * Evolvcode CMS - Media API
 * Handles AJAX requests for the media library.
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/FileUpload.php';

// Ensure authentication
Auth::requireLogin();

// Prevent any output before JSON
error_reporting(0);
ini_set('display_errors', 0);
while (ob_get_level()) ob_end_clean();

header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? '';
$db = Database::getInstance();
$response = ['success' => false, 'message' => 'Invalid action'];

try {
    switch ($action) {
        case 'list':
            $page = max(1, (int)($_GET['page'] ?? 1));
            $limit = 24;
            $offset = ($page - 1) * $limit;
            $search = $_GET['q'] ?? '';
            $type = $_GET['type'] ?? '';
            
            $where = [];
            $params = [];
            
            if ($search) {
                $where[] = "(original_name LIKE ? OR title LIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }
            
            if ($type === 'image') {
                $where[] = "file_type LIKE 'image/%'";
            } elseif ($type === 'document') {
                $where[] = "file_type NOT LIKE 'image/%'";
            }
            
            $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
            
            $total = $db->fetchOne("SELECT COUNT(*) as count FROM media $whereClause", $params)['count'];
            $items = $db->fetchAll("SELECT * FROM media $whereClause ORDER BY uploaded_at DESC LIMIT $limit OFFSET $offset", $params);
            
            // Format items for frontend
            foreach ($items as &$item) {
                $item['url'] = SITE_URL . $item['file_path'];
                $item['thumbnail'] = $item['url']; // Use full image for now, or implement thumbs
                $item['is_image'] = strpos($item['file_type'], 'image/') === 0;
            }
            
            $response = [
                'success' => true,
                'items' => $items,
                'pagination' => [
                    'page' => $page,
                    'pages' => ceil($total / $limit),
                    'total' => $total
                ]
            ];
            break;

        case 'upload':
            if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
                throw new Exception('Invalid security token');
            }
            
            if (empty($_FILES['file'])) {
                throw new Exception('No file uploaded');
            }
            
            $file = $_FILES['file'];
            $uploader = new FileUpload();
            $path = null;
            
            if (strpos($file['type'], 'image/') === 0) {
                $path = $uploader->uploadImage($file);
                // Calculate dimensions if image
                $dims = getimagesize($file['tmp_name']);
                $dimensions = $dims ? "{$dims[0]}x{$dims[1]}" : null;
            } else {
                $path = $uploader->uploadFile($file, 'files', [
                    'application/pdf', 'application/msword', 
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'text/plain', 'application/zip'
                ]);
                $dimensions = null;
            }
            
            if (!$path) {
                throw new Exception($uploader->getError() ?: 'Upload failed');
            }
            
            // Insert into DB
            $db->insert('media', [
                'filename' => basename($path),
                'original_name' => $file['name'],
                'title' => pathinfo($file['name'], PATHINFO_FILENAME),
                'file_type' => $file['type'],
                'file_size' => $file['size'],
                'file_path' => $path,
                'dimensions' => $dimensions
            ]);
            
            $id = $db->getConnection()->lastInsertId();
            $item = $db->fetchOne("SELECT * FROM media WHERE id = ?", [$id]);
            $item['url'] = SITE_URL . $item['file_path'];
            $item['is_image'] = strpos($item['file_type'], 'image/') === 0;
            
            $response = ['success' => true, 'item' => $item, 'message' => 'File uploaded successfully'];
            break;

        case 'update':
            if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
                throw new Exception('Invalid security token');
            }
            
            $id = (int)($_POST['id'] ?? 0);
            if (!$id) throw new Exception('Invalid media ID');
            
            $data = [
                'title' => Security::clean($_POST['title'] ?? ''),
                'alt_text' => Security::clean($_POST['alt_text'] ?? ''),
                'description' => Security::clean($_POST['description'] ?? '')
            ];
            
            $db->update('media', $data, "id = ?", [$id]);
            $response = ['success' => true, 'message' => 'Media updated'];
            break;

        case 'delete':
            if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
                throw new Exception('Invalid security token');
            }
            
            $id = (int)($_POST['id'] ?? 0);
            if (!$id) throw new Exception('Invalid media ID');
            
            $media = $db->fetchOne("SELECT * FROM media WHERE id = ?", [$id]);
            if ($media) {
                $filePath = ROOT_PATH . $media['file_path'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                $db->delete('media', "id = ?", [$id]);
                $response = ['success' => true, 'message' => 'File deleted'];
            } else {
                throw new Exception('File not found');
            }
            break;
    }
} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

echo json_encode($response);
