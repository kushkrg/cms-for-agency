<?php
/**
 * Evolvcode - Career Page
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/includes/helpers.php'; // Ensure helpers are loaded

// Fetch Page Content
$page = $db->fetchOne("SELECT * FROM pages WHERE slug = 'career' AND status = 'published'");

// If page exists in DB, use its title and content
if ($page) {
    $pageTitle = $page['title'];
    // We'll use the DB content for hero and values section
    // If content is empty (e.g. just created), we might want a fallback, but seeding should handle it.
} else {
    // Fallback if not verified in DB yet
    $pageTitle = 'Career - Join Our Team';
}

require_once __DIR__ . '/includes/header.php';

if ($page && !empty($page['content'])) {
    echo $page['content'];
} else {
    // Fallback Hardcoded Content (matching seed)
    ?>
    <section class="section page-header" style="padding-top: 150px; background: var(--color-gray-50);">
        <div class="container">
            <div class="section-header">
                <span class="section-label">Join Our Team</span>
                <h1 class="section-title">Build the Future With Us</h1>
                <p class="section-description lead">
                    We are looking for passionate individuals to join our mission.
                </p>
            </div>
        </div>
    </section>

    <section class="values-section" style="padding: 80px 0;">
        <div class="container">
            <div class="grid grid-3" style="gap: 40px;">
                <div class="value-card text-center">
                    <div class="icon-box" style="font-size: 2.5rem; color: var(--color-primary); margin-bottom: 20px;">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h3>Innovation First</h3>
                    <p class="text-gray-600">We push boundaries and embrace new technologies to deliver cutting-edge solutions.</p>
                </div>
                <div class="value-card text-center">
                    <div class="icon-box" style="font-size: 2.5rem; color: var(--color-primary); margin-bottom: 20px;">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Collaborative Culture</h3>
                    <p class="text-gray-600">We believe in the power of teamwork and open communication to achieve great results.</p>
                </div>
                <div class="value-card text-center">
                    <div class="icon-box" style="font-size: 2.5rem; color: var(--color-primary); margin-bottom: 20px;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Growth & Learning</h3>
                    <p class="text-gray-600">We invest in our people, providing opportunities for continuous learning and career advancement.</p>
                </div>
            </div>
        </div>
    </section>
    <?php
}
?>


<?php
// Handle Application Submission
$message = '';
$error = '';

if (Security::isPost() && isset($_POST['submit_application'])) {
    // Verify CSRF
    if (!Security::validateCSRFToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
        $error = 'Invalid form submission. Please refresh and try again.';
    } 
    // Verify reCAPTCHA
    elseif (($recaptchaResult = Recaptcha::verify($_POST['recaptcha_token'] ?? '')) !== true) {
        $error = $recaptchaResult;
    }
    else {
        // Collect Data
        $name = Security::clean($_POST['name'] ?? '');
        $email = Security::clean($_POST['email'] ?? '');
        $phone = Security::clean($_POST['phone'] ?? '');
        $position = Security::clean($_POST['position'] ?? '');
        $msg = Security::clean($_POST['message'] ?? '');
        
        // Handle File Upload
        $resumeUrl = '';
        if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = UPLOADS_PATH . '/resumes/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileExt = strtolower(pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION));
            $allowed = ['pdf', 'doc', 'docx'];
            
            if (!in_array($fileExt, $allowed)) {
                $error = 'Invalid file type. Only PDF and Word documents are allowed.';
            } elseif ($_FILES['resume']['size'] > 5 * 1024 * 1024) {
                $error = 'File is too large. Max size is 5MB.';
            } else {
                $fileName = createSlug($name) . '-' . time() . '.' . $fileExt;
                if (move_uploaded_file($_FILES['resume']['tmp_name'], $uploadDir . $fileName)) {
                    $resumeUrl = UPLOADS_URL . '/resumes/' . $fileName;
                } else {
                    $error = 'Failed to upload resume. Please try again.';
                }
            }
        } elseif (isset($_FILES['resume']) && $_FILES['resume']['error'] !== UPLOAD_ERR_NO_FILE) {
            $error = 'File upload error code: ' . $_FILES['resume']['error'];
        } else {
            $error = 'Resume file is required.';
        }
        
        if (empty($error)) {
            // Save to Contact Submissions (optional, for record keeping)
            $db->insert('contact_submissions', [
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'subject' => "Application: $position",
                'message' => $msg . "\n\nResume: " . $resumeUrl,
                'ip_address' => Security::getClientIP()
            ]);
            
            // Send Email
            require_once __DIR__ . '/includes/Mailer.php';
            $adminEmail = getSetting('contact_email', 'sales@evolvcode.com');
            $subject = "New Job Application: $position - $name";
            $body = "<h2>New Job Application</h2>";
            $body .= "<p><strong>Name:</strong> $name</p>";
            $body .= "<p><strong>Email:</strong> $email</p>";
            $body .= "<p><strong>Phone:</strong> $phone</p>";
            $body .= "<p><strong>Position:</strong> $position</p>";
            $body .= "<p><strong>Message:</strong><br>" . nl2br($msg) . "</p>";
            $body .= "<p><strong>Resume:</strong> <a href='$resumeUrl'>Download Resume</a></p>";
            
            if (Mailer::sendMail($adminEmail, $subject, $body, ['html' => true, 'reply_to' => $email])) {
                $message = 'Application submitted successfully! We will review it and get back to you.';
                // Clear form
                $_POST = [];
            } else {
                $error = 'Failed to send application email. Please try again later.';
            }
        }
    }
}
?>

<!-- Application Form Section -->
<section class="application-section" style="padding: 80px 0; background-color: #f8fafc;">
    <div class="container">
        <div class="card" style="max-width: 800px; margin: 0 auto; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: none; border-radius: 12px; overflow: hidden;">
            <div class="card-header" style="background: white; padding: 40px 40px 20px 40px; border-bottom: none;">
                <h2 class="text-center" style="margin-bottom: 10px;">Apply Now</h2>
                <p class="text-center text-gray-500">Ready to make an impact? Fill out the form below.</p>
            </div>
            <div class="card-body" style="padding: 0 40px 40px 40px;">
                
                <?php if ($message): ?>
                <div class="alert alert-success" style="margin-bottom: 20px; background: #dcfce7; color: #166534; padding: 15px; border-radius: 6px;">
                    <i class="fas fa-check-circle"></i> <?= e($message) ?>
                </div>
                <?php endif; ?>

                <?php if ($error): ?>
                <div class="alert alert-error" style="margin-bottom: 20px; background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 6px;">
                    <i class="fas fa-exclamation-circle"></i> <?= e($error) ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="" enctype="multipart/form-data" class="career-form">
                    <?= Security::csrfField() ?>
                    <input type="hidden" name="submit_application" value="1">
                    
                    <div class="grid grid-2" style="gap: 20px;">
                        <div class="form-group">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" placeholder="John Doe" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 6px;" value="<?= e($_POST['name'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="john@example.com" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 6px;" value="<?= e($_POST['email'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="grid grid-2" style="gap: 20px; margin-top: 20px;">
                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone" class="form-control" placeholder="+1 (555) 000-0000" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 6px;" value="<?= e($_POST['phone'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Position Applied For</label>
                            <select name="position" class="form-control" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 6px;">
                                <option value="">Select a position...</option>
                                <option value="Web Developer" <?= (isset($_POST['position']) && $_POST['position'] == 'Web Developer') ? 'selected' : '' ?>>Web Developer</option>
                                <option value="Digital Marketer" <?= (isset($_POST['position']) && $_POST['position'] == 'Digital Marketer') ? 'selected' : '' ?>>Digital Marketer</option>
                                <option value="UI/UX Designer" <?= (isset($_POST['position']) && $_POST['position'] == 'UI/UX Designer') ? 'selected' : '' ?>>UI/UX Designer</option>
                                <option value="Content Writer" <?= (isset($_POST['position']) && $_POST['position'] == 'Content Writer') ? 'selected' : '' ?>>Content Writer</option>
                                <option value="Project Manager" <?= (isset($_POST['position']) && $_POST['position'] == 'Project Manager') ? 'selected' : '' ?>>Project Manager</option>
                                <option value="Other" <?= (isset($_POST['position']) && $_POST['position'] == 'Other') ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group" style="margin-top: 20px;">
                        <label class="form-label">Resume / CV (PDF, DOC, DOCX)</label>
                        <input type="file" name="resume" class="form-control" accept=".pdf,.doc,.docx" required style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; background: #f8fafc;">
                        <small class="text-gray-500">Max file size: 5MB</small>
                    </div>
                    
                    <div class="form-group" style="margin-top: 20px;">
                        <label class="form-label">Cover Letter / Message</label>
                        <textarea name="message" class="form-control" rows="5" placeholder="Tell us why you're a good fit..." required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-family: inherit;"><?= e($_POST['message'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-footer text-center" style="margin-top: 30px;">
                        <button type="submit" class="btn btn-primary btn-lg" style="padding: 12px 40px; font-size: 1.1rem; border-radius: 50px;">
                            Submit Application <i class="fas fa-paper-plane" style="margin-left: 8px;"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
