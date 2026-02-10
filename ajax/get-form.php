<?php
/**
 * Evolvcode CMS - AJAX Get Form
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Disable error reporting for cleaner JSON output
error_reporting(0);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/FormHelper.php';

$slug = $_GET['slug'] ?? '';
$type = $_GET['type'] ?? 'popup';

if (empty($slug)) {
    echo json_encode(['error' => 'Form slug is required']);
    exit;
}

try {
    // Ensure tables exist (auto-migration)
    FormHelper::ensureTables();
    
    $html = FormHelper::render($slug, ['isPopup' => ($type === 'popup')]);
    
    echo json_encode([
        'success' => true,
        'html' => $html
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
