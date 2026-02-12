<?php
// ajax/subscribe.php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

if (!Security::isPost()) {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Validate CSRF?
// Note: Footer is public. Strict CSRF might be tricky if cached, but we should try.
// For now, let's rely on basic validation.

$email = Security::sanitize($_POST['email'] ?? '');

if (!Security::isValidEmail($email)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

$db = Database::getInstance();

// Check if already subscribed
$exists = $db->fetchOne("SELECT id FROM subscribers WHERE email = ?", [$email]);
if ($exists) {
    echo json_encode(['success' => false, 'message' => 'You are already subscribed!']);
    exit;
}

try {
    $db->insert('subscribers', [
        'email' => $email,
        'status' => 'active'
    ]);
    echo json_encode(['success' => true, 'message' => 'Thank you for subscribing!']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
}
?>
