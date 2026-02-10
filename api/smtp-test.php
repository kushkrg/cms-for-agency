<?php
/**
 * Evolvcode CMS - SMTP Test API
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/Mailer.php';

header('Content-Type: application/json');

// Require admin login
if (!Auth::isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$result = Mailer::getInstance()->testConnection();

echo json_encode($result);
