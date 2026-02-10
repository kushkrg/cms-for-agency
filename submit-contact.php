<?php
require_once __DIR__ . '/includes/config.php';

// Ensure JSON response
header('Content-Type: application/json');

// Only allow POST requests
if (!Security::isPost()) {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Validate CSRF token
$token = $_POST[CSRF_TOKEN_NAME] ?? '';
if (!Security::validateCSRFToken($token)) {
    echo json_encode(['success' => false, 'message' => 'Invalid form submission. Please refresh and try again.']);
    exit;
}

// Validate reCAPTCHA
$recaptchaToken = $_POST['recaptcha_token'] ?? '';
$recaptchaResult = Recaptcha::verify($recaptchaToken);
if ($recaptchaResult !== true) {
    echo json_encode(['success' => false, 'message' => $recaptchaResult]);
    exit;
}

// Get and sanitize form data
$name = Security::clean($_POST['name'] ?? '');
$email = Security::clean($_POST['email'] ?? '');
$phone = Security::clean($_POST['phone'] ?? '');
$subject = Security::clean($_POST['subject'] ?? '');
$message = Security::clean($_POST['message'] ?? '');

// Validate required fields
if (empty($name) || empty($email) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit;
}

// Validate email
if (!Security::isValidEmail($email)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

// Save to database
$db = Database::getInstance();
try {
    $db->insert('contact_submissions', [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'subject' => $subject,
        'message' => $message,
        'ip_address' => Security::getClientIP()
    ]);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Thank you for your message! We\'ll get back to you soon.'
    ]);
    
} catch (Exception $e) {
    // Log actual error for debugging
    error_log('Contact form error: ' . $e->getMessage());
    
    // return generic error to user
    echo json_encode([
        'success' => false, 
        'message' => 'An error occurred while sending your message. Please try again later.'
    ]);
}
