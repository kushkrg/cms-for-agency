<?php
/**
 * Evolvcode CMS - Form Renderer Helper
 */

class FormRenderer {
    
    /**
     * Render form HTML
     */
    public static function render($slugOrId, $options = []) {
        $db = Database::getInstance();
        
        $where = is_numeric($slugOrId) ? 'id = ?' : 'slug = ?';
        $form = $db->fetch("SELECT * FROM forms WHERE $where AND status = 'active'", [$slugOrId]);
        
        if (!$form) {
            return '<!-- Form not found -->';
        }
        
        $fields = $db->fetchAll("SELECT * FROM form_fields WHERE form_id = ? ORDER BY sort_order ASC", [$form['id']]);
        
        $formId = 'form_' . $form['slug'];
        $action = '/api/form-submit.php';
        
        $html = '<form id="' . $formId . '" class="popup-form dynamic-form" method="POST" action="' . $action . '">';
        $html .= '<input type="hidden" name="csrf_token" value="' . Security::generateCSRFToken() . '">';
        $html .= '<input type="hidden" name="form_id" value="' . $form['id'] . '">';
        
        foreach ($fields as $field) {
            $html .= self::renderField($field);
        }
        
        $loadingText = $options['loading_text'] ?? 'Sending...';
        $btnText = $form['submit_button_text'] ?: 'Submit';
        
        $html .= '<button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">';
        $html .= '<span class="btn-text">' . htmlspecialchars($btnText) . '</span>';
        $html .= '<span class="btn-loading" style="display: none;"><i class="fas fa-spinner fa-spin"></i> ' . $loadingText . '</span>';
        $html .= '</button>';
        
        $html .= '<div class="form-message" id="' . $formId . '_message"></div>';
        $html .= '</form>';
        
        // Add specific JS for this form
        $html .= self::renderScript($formId);
        
        return $html;
    }
    
    private static function renderField($field) {
        $html = '<div class="form-group">';
        
        $label = htmlspecialchars($field['label']);
        if ($field['is_required']) {
            $label .= ' *';
        }
        
        $html .= '<label for="field_' . $field['id'] . '">' . $label . '</label>';
        
        $name = htmlspecialchars($field['name']);
        $placeholder = htmlspecialchars($field['placeholder'] ?? '');
        $required = $field['is_required'] ? 'required' : '';
        $id = 'field_' . $field['id'];
        
        switch ($field['type']) {
            case 'textarea':
                $html .= '<textarea id="' . $id . '" name="' . $name . '" rows="4" placeholder="' . $placeholder . '" ' . $required . '></textarea>';
                break;
                
            case 'select':
                $html .= '<select id="' . $id . '" name="' . $name . '" ' . $required . '>';
                $html .= '<option value="">' . ($placeholder ?: 'Select...') . '</option>';
                
                $options = json_decode($field['options'] ?? '[]', true);
                if (is_array($options)) {
                    foreach ($options as $opt) {
                        $html .= '<option value="' . htmlspecialchars($opt) . '">' . htmlspecialchars($opt) . '</option>';
                    }
                }
                $html .= '</select>';
                break;
                
            case 'checkbox':
                $html .= '<div>';
                $options = json_decode($field['options'] ?? '[]', true);
                if (is_array($options) && !empty($options)) {
                    foreach ($options as $opt) {
                        $html .= '<label class="checkbox-inline">';
                        $html .= '<input type="checkbox" name="' . $name . '[]" value="' . htmlspecialchars($opt) . '"> ' . htmlspecialchars($opt);
                        $html .= '</label>';
                    }
                } else {
                    // Single checkbox (boolean)
                    $html .= '<label class="checkbox-inline">';
                    $html .= '<input type="checkbox" name="' . $name . '" value="1" ' . $required . '> ' . $placeholder;
                    $html .= '</label>';
                }
                $html .= '</div>';
                break;
                
            case 'radio':
                $html .= '<div>';
                $options = json_decode($field['options'] ?? '[]', true);
                if (is_array($options)) {
                    foreach ($options as $opt) {
                        $html .= '<label class="radio-inline">';
                        $html .= '<input type="radio" name="' . $name . '" value="' . htmlspecialchars($opt) . '" ' . $required . '> ' . htmlspecialchars($opt);
                        $html .= '</label>';
                    }
                }
                $html .= '</div>';
                break;
                
            default: // text, email, tel, etc.
                $type = in_array($field['type'], ['text', 'email', 'tel', 'url', 'number', 'date', 'hidden']) ? $field['type'] : 'text';
                $html .= '<input type="' . $type . '" id="' . $id . '" name="' . $name . '" placeholder="' . $placeholder . '" ' . $required . '>';
        }
        
        $html .= '</div>';
        return $html;
    }
    
    private static function renderScript($formId) {
        // We handle this via generic JS in main.js, 
        // but if we needed inline JS, it would go here.
        // For now return empty string.
        return '';
    }
}
