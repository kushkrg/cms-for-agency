<?php
/**
 * Evolvcode CMS - Contact Form API
 * 
 * Handles AJAX form submissions for contact/inquiry forms.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../includes/config.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get JSON input or form data
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (strpos($contentType, 'application/json') !== false) {
    $input = json_decode(file_get_contents('php://input'), true);
} else {
    $input = $_POST;
}

// Validate CSRF token
$csrfToken = $input['csrf_token'] ?? '';
if (!Security::validateCSRFToken($csrfToken)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid security token. Please refresh the page and try again.']);
    exit;
}

// Validate reCAPTCHA
$recaptchaToken = $input['recaptcha_token'] ?? '';
$recaptchaResult = Recaptcha::verify($recaptchaToken);
if ($recaptchaResult !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => $recaptchaResult]);
    exit;
}

// Rate limiting by IP
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$rateLimitKey = 'contact_form_' . md5($ip);

// Simple file-based rate limiting (5 submissions per hour)
$rateLimitFile = sys_get_temp_dir() . '/' . $rateLimitKey . '.txt';
$submissions = [];
if (file_exists($rateLimitFile)) {
    $submissions = json_decode(file_get_contents($rateLimitFile), true) ?: [];
    // Remove old entries (older than 1 hour)
    $submissions = array_filter($submissions, fn($time) => $time > time() - 3600);
}

if (count($submissions) >= 5) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Too many submissions. Please try again later.']);
    exit;
}

// Validate required fields
$name = Security::clean($input['name'] ?? '');
$email = Security::clean($input['email'] ?? '');
$phone = Security::clean($input['phone'] ?? '');
$subject = Security::clean($input['subject'] ?? '');
$message = Security::clean($input['message'] ?? '');
$source = Security::clean($input['source'] ?? 'Popup Form');

$errors = [];

if (empty($name)) {
    $errors[] = 'Name is required';
}

if (empty($email)) {
    $errors[] = 'Email is required';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email address';
}

if (empty($message)) {
    $errors[] = 'Message is required';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode('. ', $errors)]);
    exit;
}

// Prepare subject line
if (empty($subject)) {
    $subject = 'General Inquiry';
}
$subject = 'Inquiry: ' . $subject . ' (via ' . $source . ')';

// Save to database
try {
    $db = Database::getInstance();
    
    $db->insert('contacts', [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'subject' => $subject,
        'message' => $message,
        'ip_address' => $ip,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'status' => 'unread',
        'created_at' => date('Y-m-d H:i:s')
    ]);
    
    // Update rate limiting
    $submissions[] = time();
    file_put_contents($rateLimitFile, json_encode($submissions));
    
    // Optionally send email notification (if mail is configured)
    $adminEmail = getSetting('contact_email', 'sales@evolvcode.com');
    $siteName = getSetting('site_name', 'Evolvcode Solutions');
    
    // Try to send email notification (silent fail if mail is not configured)
    @mail(
        $adminEmail,
        "New Contact from $siteName: $subject",
        "Name: $name\nEmail: $email\nPhone: $phone\n\nMessage:\n$message\n\nSource: $source\nIP: $ip",
        "From: noreply@" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "\r\n" .
        "Reply-To: $email\r\n" .
        "Content-Type: text/plain; charset=UTF-8"
    );
    
    echo json_encode([
        'success' => true,
        'message' => 'Thank you! Your message has been sent successfully. We\'ll get back to you within 24 hours.'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Something went wrong. Please try again later.']);
    
    // Log error
    error_log('Contact form error: ' . $e->getMessage());
}
