-- ============================================
-- Form Management System Schema
-- ============================================

-- Forms table - stores form configurations
CREATE TABLE IF NOT EXISTS forms (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    type ENUM('popup', 'embedded', 'contact') DEFAULT 'popup',
    title VARCHAR(255),
    description TEXT,
    submit_button_text VARCHAR(100) DEFAULT 'Submit',
    success_message TEXT,
    email_notification BOOLEAN DEFAULT TRUE,
    email_to VARCHAR(255),
    redirect_url VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Form fields table - stores field definitions for each form
CREATE TABLE IF NOT EXISTS form_fields (
    id INT PRIMARY KEY AUTO_INCREMENT,
    form_id INT NOT NULL,
    label VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    type ENUM('text', 'email', 'tel', 'textarea', 'select', 'checkbox', 'radio', 'hidden', 'number', 'date', 'url') DEFAULT 'text',
    placeholder VARCHAR(255),
    default_value VARCHAR(255),
    options TEXT COMMENT 'JSON array for select/radio/checkbox options',
    is_required BOOLEAN DEFAULT FALSE,
    validation_rules VARCHAR(255) COMMENT 'Comma-separated rules like: min:3,max:100',
    css_class VARCHAR(100),
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (form_id) REFERENCES forms(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Form submissions table - stores all form submissions
CREATE TABLE IF NOT EXISTS form_submissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    form_id INT NOT NULL,
    data JSON NOT NULL COMMENT 'JSON object with field name => value pairs',
    ip_address VARCHAR(45),
    user_agent TEXT,
    referrer VARCHAR(255),
    status ENUM('unread', 'read', 'replied', 'archived') DEFAULT 'unread',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (form_id) REFERENCES forms(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Indexes for performance
CREATE INDEX idx_forms_status ON forms(status);
CREATE INDEX idx_form_fields_form ON form_fields(form_id, sort_order);
CREATE INDEX idx_form_submissions_form ON form_submissions(form_id, status, created_at);

-- Insert default contact form
INSERT INTO forms (name, slug, type, title, description, submit_button_text, success_message, email_notification) VALUES
('Contact Form', 'contact', 'popup', 'Get a Free Consultation', 'Fill out the form below and we''ll get back to you within 24 hours.', 'Send Message', 'Thank you! Your message has been sent successfully. We''ll get back to you within 24 hours.', TRUE);

-- Insert default fields for contact form
SET @form_id = LAST_INSERT_ID();

INSERT INTO form_fields (form_id, label, name, type, placeholder, is_required, sort_order) VALUES
(@form_id, 'Your Name', 'name', 'text', 'John Doe', TRUE, 1),
(@form_id, 'Email Address', 'email', 'email', 'john@example.com', TRUE, 2),
(@form_id, 'Phone Number', 'phone', 'tel', '+91 98765 43210', FALSE, 3),
(@form_id, 'Interested In', 'subject', 'select', '', FALSE, 4),
(@form_id, 'Your Message', 'message', 'textarea', 'Tell us about your project...', TRUE, 5);

-- Update subject field with service options (will be populated dynamically)
UPDATE form_fields SET options = '["General Inquiry", "Web Development", "Digital Marketing", "SEO Services", "Other"]' WHERE form_id = @form_id AND name = 'subject';
