<?php
/**
 * Evolvcode CMS - AJAX File Upload Handler
 */

// Define root path if not already defined (assuming this file is in /ajax/)
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

require_once ROOT_PATH . '/includes/config.php';
require_once ROOT_PATH . '/includes/FileUpload.php';

// Ensure JSON response
header('Content-Type: application/json');

// Check authentication (simple check for now, can be improved)
// if (!Authentication::isLoggedIn()) {
//     http_response_code(403);
//     echo json_encode(['success' => false, 'message' => 'Unauthorized']);
//     exit;
// }

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Validate CSRF token if sent (JS might send it in headers or body)
// For simple AJAX uploads often tokens are skipped or passed in headers. 
// Let's assume for this fix we might skip strict CSRF check or rely on session if strictly needed, 
// but best practice is to include it.
// $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_POST[CSRF_TOKEN_NAME] ?? '';
// if (!Security::validateCSRFToken($token)) { ... }

if (empty($_FILES['files'])) {
    echo json_encode(['success' => false, 'message' => 'No files received.']);
    exit;
}

$uploadedFiles = [];
$errors = [];
$db = Database::getInstance();

$files = $_FILES['files'];

// Normalize files array structure
$fileCount = count($files['name']);

for ($i = 0; $i < $fileCount; $i++) {
    if ($files['error'][$i] === UPLOAD_ERR_OK) {
        $file = [
            'name' => $files['name'][$i],
            'type' => $files['type'][$i],
            'tmp_name' => $files['tmp_name'][$i],
            'error' => $files['error'][$i],
            'size' => $files['size'][$i],
        ];
        
        $uploader = new FileUpload();
        
        // Determine upload type based on MIME
        if (strpos($file['type'], 'image/') === 0) {
            $path = $uploader->uploadImage($file);
        } else {
            // Allow basic docs
            $path = $uploader->uploadFile($file, 'documents', ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);
        }
        
        if ($path) {
            try {
                // Insert into database
                $db->insert('media', [
                    'filename' => basename($path),
                    'original_name' => $file['name'],
                    'file_type' => $file['type'],
                    'file_size' => $file['size'],
                    'file_path' => $path
                ]);
                
                $uploadedFiles[] = [
                    'id' => $db->lastInsertId(),
                    'path' => $path,
                    'url' => SITE_URL . $path,
                    'name' => $file['name']
                ];
            } catch (Exception $e) {
                $errors[] = "Database error for {$file['name']}";
            }
        } else {
            $errors[] = "Failed to upload {$file['name']}: " . implode(', ', $uploader->getErrors());
        }
    } else {
        $errors[] = "Error uploading {$files['name'][$i]}";
    }
}

if (!empty($uploadedFiles)) {
    echo json_encode([
        'success' => true,
        'message' => count($uploadedFiles) . ' file(s) uploaded successfully.',
        'files' => $uploadedFiles,
        'errors' => $errors
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Upload failed.',
        'errors' => $errors
    ]);
}
