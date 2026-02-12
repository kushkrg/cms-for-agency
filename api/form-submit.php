<?php
/**
 * Evolvcode CMS - Generic Form Submission API
 * 
 * Handles submissions for dynamic forms created in the admin panel.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Mailer.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get form data
$input = $_POST;
$db = Database::getInstance();

// Validate CSRF token
$csrfToken = $input['csrf_token'] ?? '';
if (!Security::validateCSRFToken($csrfToken)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid security token. Please refresh the page.']);
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

// Get form ID
$formId = (int)($input['form_id'] ?? 0);
if (!$formId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Form ID is required']);
    exit;
}

// Fetch form configuration
$form = $db->fetch("SELECT * FROM forms WHERE id = ? AND status = 'active'", [$formId]);
if (!$form) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Form not found or inactive']);
    exit;
}

// Fetch form fields
$fields = $db->fetchAll("SELECT * FROM form_fields WHERE form_id = ? ORDER BY sort_order ASC", [$formId]);

// Validate fields
$errors = [];
$submissionData = [];

foreach ($fields as $field) {
    $fieldName = $field['name'];
    $value = isset($input[$fieldName]) ? trim($input[$fieldName]) : '';
    
    // Check required
    if ($field['is_required'] && $value === '') {
        $errors[] = $field['label'] . ' is required.';
    }
    
    // Validate email
    if ($field['type'] === 'email' && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
        $errors[] = $field['label'] . ' must be a valid email address.';
    }
    
    $submissionData[$fieldName] = Security::clean($value);
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

// Rate limiting (simple IP based)
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$hourAgo = date('Y-m-d H:i:s', strtotime('-1 hour'));
$recentCount = $db->fetchOne(
    "SELECT COUNT(*) as count FROM form_submissions WHERE ip_address = ? AND created_at > ?", 
    [$ip, $hourAgo]
)['count'] ?? 0;

if ($recentCount >= 10) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Too many submissions. Please try again later.']);
    exit;
}

// Save submission
try {
    $db->insert('form_submissions', [
        'form_id' => $formId,
        'data' => json_encode($submissionData),
        'ip_address' => $ip,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'referrer' => $_SERVER['HTTP_REFERER'] ?? '',
        'source_page' => Security::clean($input['source_page'] ?? ''),
        'status' => 'unread'
    ]);
    
    // Send email notification
    if ($form['email_notification']) {
        $to = $form['email_to'] ?: getSetting('contact_email');
        if ($to) {
            $subject = 'New Submission: ' . $form['name'];
            $body = "<h2>New Form Submission</h2>";
            $body .= "<p><strong>Form:</strong> {$form['name']}</p>";
            $body .= "<p><strong>Date:</strong> " . date('Y-m-d H:i:s') . "</p>";
            $body .= "<hr>";
            $body .= "<table style='width: 100%; border-collapse: collapse;'>";
            
            foreach ($fields as $field) {
                $val = $submissionData[$field['name']] ?? '';
                $body .= "<tr><td style='padding: 8px; border-bottom: 1px solid #eee; width: 150px;'><strong>{$field['label']}:</strong></td>";
                $body .= "<td style='padding: 8px; border-bottom: 1px solid #eee;'> " . nl2br(htmlspecialchars($val)) . "</td></tr>";
            }
            $body .= "</table>";
            
            // Send in background or essentially fail silently to not block user response
            try {
                Mailer::sendMail($to, $subject, $body, ['html' => true]);
            } catch (Exception $e) {
                error_log('Mail error: ' . $e->getMessage());
            }
        }
    }
    
    // Return success
    echo json_encode([
        'success' => true, 
        'message' => $form['success_message'] ?: 'Thank you! Your submission has been received.',
        'redirect' => $form['redirect_url']
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error. Please try again later.']);
    error_log('Form submission error: ' . $e->getMessage());
}
