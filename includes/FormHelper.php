<?php
/**
 * Evolvcode CMS - Form Helper Class
 */

class FormHelper {
    
    /**
     * Ensure form tables exist
     */
    public static function ensureTables(): void {
        $db = Database::getInstance();
        
        // Forms table
        $db->query("CREATE TABLE IF NOT EXISTS forms (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // Form fields table
        $db->query("CREATE TABLE IF NOT EXISTS form_fields (
            id INT PRIMARY KEY AUTO_INCREMENT,
            form_id INT NOT NULL,
            label VARCHAR(255) NOT NULL,
            name VARCHAR(100) NOT NULL,
            type ENUM('text', 'email', 'tel', 'textarea', 'select', 'checkbox', 'radio', 'hidden', 'number', 'date', 'url') DEFAULT 'text',
            placeholder VARCHAR(255),
            default_value VARCHAR(255),
            options TEXT COMMENT 'JSON array',
            is_required BOOLEAN DEFAULT FALSE,
            validation_rules VARCHAR(255),
            css_class VARCHAR(100),
            sort_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (form_id) REFERENCES forms(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // Form submissions table
        $db->query("CREATE TABLE IF NOT EXISTS form_submissions (
            id INT PRIMARY KEY AUTO_INCREMENT,
            form_id INT NOT NULL,
            data JSON NOT NULL,
            ip_address VARCHAR(45),
            user_agent TEXT,
            referrer VARCHAR(255),
            status ENUM('unread', 'read', 'replied', 'archived') DEFAULT 'unread',
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (form_id) REFERENCES forms(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }
    
    /**
     * Render a form by slug
     */
    public static function render(string $slug, array $options = []): string {
        $db = Database::getInstance();
        
        // Fetch form
        $form = $db->fetchOne("SELECT * FROM forms WHERE slug = ? AND status = 'active'", [$slug]);
        if (!$form) {
            return "<!-- Form '{$slug}' not found or inactive -->";
        }
        
        // Fetch fields
        $fields = $db->fetchAll("SELECT * FROM form_fields WHERE form_id = ? ORDER BY sort_order ASC", [$form['id']]);
        
        $formId = 'evolv-form-' . $form['id'];
        $isPopup = $options['isPopup'] ?? ($form['type'] === 'popup');
        
        ob_start();
        ?>
        <div class="evolv-form-container <?= $isPopup ? 'evolv-form-popup' : '' ?>" id="<?= $formId ?>-container">
            <?php if ($isPopup): ?>
            <div class="evolv-form-overlay" onclick="EvolvForm.closePopup('<?= $slug ?>')"></div>
            <div class="evolv-form-content">
                <button type="button" class="evolv-form-close" onclick="EvolvForm.closePopup('<?= $slug ?>')">&times;</button>
            <?php endif; ?>
            
            <form class="evolv-form" id="<?= $formId ?>" onsubmit="return EvolvForm.submit(this, event)" data-slug="<?= $slug ?>">
                <?php if (!empty($form['title'])): ?>
                <h3 class="evolv-form-title"><?= htmlspecialchars($form['title']) ?></h3>
                <?php endif; ?>
                
                <?php if (!empty($form['description'])): ?>
                <p class="evolv-form-description"><?= nl2br(htmlspecialchars($form['description'])) ?></p>
                <?php endif; ?>
                
                <!-- Security Fields -->
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                <div style="display:none;visibility:hidden;">
                    <label>Don't fill this out if you're human: <input type="text" name="_gotcha" value=""></label>
                </div>
                
                <div class="evolv-form-response"></div>
                
                <div class="evolv-form-fields">
                    <?php foreach ($fields as $field): ?>
                        <?php self::renderField($field); ?>
                    <?php endforeach; ?>
                </div>
                
                <div class="evolv-form-actions">
                    <button type="submit" class="evolv-form-submit">
                        <?= htmlspecialchars($form['submit_button_text']) ?>
                    </button>
                </div>
            </form>
            
            <?php if ($isPopup): ?>
            </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Render a single field
     */
    private static function renderField(array $field): void {
        $id = 'field-' . $field['id'];
        $required = $field['is_required'] ? 'required' : '';
        $reqMark = $field['is_required'] ? '<span class="required">*</span>' : '';
        $placeholder = $field['placeholder'] ? 'placeholder="' . htmlspecialchars($field['placeholder']) . '"' : '';
        $value = $field['default_value'] ?? '';
        
        echo '<div class="evolv-field-group">';
        
        if ($field['type'] !== 'hidden') {
            echo '<label for="' . $id . '" class="evolv-field-label">' . htmlspecialchars($field['label']) . ' ' . $reqMark . '</label>';
        }
        
        switch ($field['type']) {
            case 'textarea':
                echo '<textarea name="' . $field['name'] . '" id="' . $id . '" class="evolv-field-input" rows="4" ' . $required . ' ' . $placeholder . '>' . htmlspecialchars($value) . '</textarea>';
                break;
                
            case 'select':
                echo '<select name="' . $field['name'] . '" id="' . $id . '" class="evolv-field-select" ' . $required . '>';
                echo '<option value="">' . ($field['placeholder'] ?: 'Select...') . '</option>';
                if (!empty($field['options'])) {
                    // Try to decode JSON, fallback to splitting by newline
                    $options = json_decode($field['options'], true);
                    if (!$options) {
                        $options = explode("\n", $field['options']);
                    }
                    foreach ($options as $opt) {
                        $opt = trim($opt);
                        if ($opt) {
                            $selected = ($opt === $value) ? 'selected' : '';
                            echo '<option value="' . htmlspecialchars($opt) . '" ' . $selected . '>' . htmlspecialchars($opt) . '</option>';
                        }
                    }
                }
                echo '</select>';
                break;
                
            case 'radio':
            case 'checkbox':
                if (!empty($field['options'])) {
                    $options = json_decode($field['options'], true) ?: explode("\n", $field['options']);
                    echo '<div class="evolv-field-options">';
                    foreach ($options as $opt) {
                        $opt = trim($opt);
                        if ($opt) {
                            $checked = ($opt === $value) ? 'checked' : '';
                            echo '<label class="evolv-field-option">';
                            echo '<input type="' . $field['type'] . '" name="' . $field['name'] . ($field['type'] === 'checkbox' ? '[]' : '') . '" value="' . htmlspecialchars($opt) . '" ' . $checked . '>';
                            echo '<span>' . htmlspecialchars($opt) . '</span>';
                            echo '</label>';
                        }
                    }
                    echo '</div>';
                }
                break;
                
            case 'hidden':
                echo '<input type="hidden" name="' . $field['name'] . '" id="' . $id . '" value="' . htmlspecialchars($value) . '">';
                break;
                
            default: // text, email, tel, number, url, date
                echo '<input type="' . $field['type'] . '" name="' . $field['name'] . '" id="' . $id . '" class="evolv-field-input" value="' . htmlspecialchars($value) . '" ' . $required . ' ' . $placeholder . '>';
                break;
        }
        
        echo '</div>';
    }
}
