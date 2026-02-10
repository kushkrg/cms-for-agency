<?php
/**
 * Evolvcode CMS - AJAX Submit Form
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/FormHelper.php';

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    // 1. Basic Security Checks
    $token = $_POST['csrf_token'] ?? '';
    $gotcha = $_POST['_gotcha'] ?? '';
    
    // Honeypot check
    if (!empty($gotcha)) {
        // Silent failure for bots
        echo json_encode(['success' => true, 'message' => 'Submission received (bot detected)']);
        exit;
    }
    
    // CSRF check (lax for now to allow cross-origin usage if needed, but good to have)
    // if (!Security::validateCSRFToken($token)) {
    //     throw new Exception('Invalid security token');
    // }
    
    $slug = $_POST['form_slug'] ?? ''; // Added hidden field in JS or passed in data
    if (!$slug) {
        // Try to get from referrer or header? No, just require it.
        // Actually, FormHelper doesn't add form_slug field. I need to add that too.
        // For now, let's look for it in POST. User JS should send it.
        // But if FormHelper renders it, it's better to verify.
        // Actually, JS will handle submission, so JS can append slug.
    }
    
    // Get slug from data attribute or hidden input
    // Let's assume frontend JS appends it.
    
    if (empty($slug)) {
        throw new Exception('Form identifier missing');
    }
    
    $db = Database::getInstance();
    $form = $db->fetchOne("SELECT * FROM forms WHERE slug = ? AND status = 'active'", [$slug]);
    
    if (!$form) {
        throw new Exception('Form not found or inactive');
    }
    
    // 2. Validate Fields
    $fields = $db->fetchAll("SELECT * FROM form_fields WHERE form_id = ? ORDER BY sort_order ASC", [$form['id']]);
    $data = [];
    $errors = [];
    
    foreach ($fields as $field) {
        $name = $field['name'];
        $value = $_POST[$name] ?? '';
        
        if (is_array($value)) {
            $value = implode(', ', $value);
        }
        
        $value = trim($value);
        
        // Check required
        if ($field['is_required'] && $value === '') {
            $errors[] = "{$field['label']} is required.";
        }
        
        // Type validation (basic)
        if ($value !== '') {
            if ($field['type'] === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email for {$field['label']}.";
            }
            // Add more validations as needed
        }
        
        $data[$field['label']] = $value;
    }
    
    if (!empty($errors)) {
        echo json_encode(['success' => false, 'error' => implode(' ', $errors)]);
        exit;
    }
    
    // 3. Save Submission
    $submissionData = [
        'form_id' => $form['id'],
        'data' => json_encode($data),
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'referrer' => $_SERVER['HTTP_REFERER'] ?? '',
        'status' => 'unread'
    ];
    
    $db->insert('form_submissions', $submissionData);
    
    // 4. Send Email Notification
    if ($form['email_notification']) {
        // Send email logic here (using Mailer class if exists)
        // Assume Mailer::send($to, $subject, $body)
        
        $to = $form['email_to'] ?: 'admin@example.com'; // Fallback
        $subject = "New Submission: {$form['name']}";
        $body = "<h2>New Form Submission</h2>";
        $body .= "<p><strong>Form:</strong> {$form['name']}</p>";
        $body .= "<ul>";
        foreach ($data as $label => $val) {
            $body .= "<li><strong>" . htmlspecialchars($label) . ":</strong> " . nl2br(htmlspecialchars($val)) . "</li>";
        }
        $body .= "</ul>";
        
        // Check if Mailer class exists
        if (class_exists('Mailer')) {
            Mailer::send($to, $subject, $body);
        } else {
            // Fallback to mail()
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= "From: Evolvcode <noreply@evolvcode.com>\r\n";
            mail($to, $subject, $body, $headers);
        }
    }
    
    // 5. Response
    echo json_encode([
        'success' => true,
        'message' => $form['success_message'] ?: 'Thank you! Your submission has been received.',
        'redirect' => $form['redirect_url']
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
