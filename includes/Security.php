<?php
/**
 * Evolvcode CMS - Security Class
 * 
 * Handles CSRF protection, XSS sanitization, and input validation.
 */

defined('ROOT_PATH') OR exit('Access Denied');

class Security
{
    /**
     * Generate CSRF token and store in session
     */
    public static function generateCSRFToken(): string
    {
        if (empty($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }
    
    /**
     * Get CSRF token hidden input field
     */
    public static function csrfField(): string
    {
        $token = self::generateCSRFToken();
        return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . htmlspecialchars($token) . '">';
    }
    
    /**
     * Validate CSRF token
     */
    public static function validateCSRFToken(?string $token): bool
    {
        if (empty($token) || empty($_SESSION[CSRF_TOKEN_NAME])) {
            return false;
        }
        
        $valid = hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
        
        // Only regenerate if explicitly requested (removed auto-regeneration for async support)
        // if ($valid) {
        //    self::regenerateCSRFToken();
        // }
        
        return $valid;
    }
    
    /**
     * Regenerate CSRF token
     */
    public static function regenerateCSRFToken(): void
    {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    
    /**
     * Sanitize string for HTML output (XSS prevention)
     */
    public static function escape(mixed $value): string
    {
        if ($value === null) {
            return '';
        }
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
    /**
     * Alias for escape()
     */
    public static function e(mixed $value): string
    {
        return self::escape($value);
    }
    
    /**
     * Sanitize string for use in URLs
     */
    public static function escapeUrl(string $url): string
    {
        return filter_var($url, FILTER_SANITIZE_URL);
    }
    
    /**
     * Clean user input - removes HTML tags
     */
    public static function clean(mixed $value): string
    {
        if ($value === null) {
            return '';
        }
        return trim(strip_tags((string) $value));
    }
    
    /**
     * Sanitize input for database (use with prepared statements)
     */
    public static function sanitize(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map([self::class, 'sanitize'], $value);
        }
        
        if (is_string($value)) {
            return trim($value);
        }
        
        return $value;
    }
    
    /**
     * Validate email address
     */
    public static function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate URL
     */
    public static function isValidUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
    
    /**
     * Validate integer
     */
    public static function isValidInt(mixed $value, int $min = PHP_INT_MIN, int $max = PHP_INT_MAX): bool
    {
        $options = [
            'options' => [
                'min_range' => $min,
                'max_range' => $max
            ]
        ];
        return filter_var($value, FILTER_VALIDATE_INT, $options) !== false;
    }
    
    /**
     * Generate a random secure string
     */
    public static function generateRandomString(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }
    
    /**
     * Generate URL-safe slug from string
     */
    public static function slugify(string $text): string
    {
        // Convert to lowercase
        $text = strtolower($text);
        
        // Remove accents/diacritics
        $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        
        // Replace non-alphanumeric with hyphens
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        
        // Remove leading/trailing hyphens
        $text = trim($text, '-');
        
        // Remove duplicate hyphens
        $text = preg_replace('/-+/', '-', $text);
        
        return $text;
    }
    
    /**
     * Check if request is POST
     */
    public static function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    /**
     * Check if request is AJAX
     */
    public static function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * Get client IP address
     */
    public static function getClientIP(): string
    {
        $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                // Handle comma-separated IPs (from proxies)
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        
        return '0.0.0.0';
    }
    
    /**
     * Prevent clickjacking by sending X-Frame-Options header
     */
    public static function setSecurityHeaders(): void
    {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }
    
    /**
     * Redirect to another URL
     */
    public static function redirect(string $url, int $statusCode = 302): void
    {
        header("Location: {$url}", true, $statusCode);
        exit;
    }
    
    /**
     * Check rate limiting for login attempts
     */
    public static function checkLoginAttempts(string $username): bool
    {
        $key = 'login_attempts_' . md5($username);
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 0, 'time' => time()];
        }
        
        $attempts = $_SESSION[$key];
        
        // Reset if lockout time has passed
        if (time() - $attempts['time'] > LOGIN_LOCKOUT_TIME) {
            $_SESSION[$key] = ['count' => 0, 'time' => time()];
            return true;
        }
        
        return $attempts['count'] < LOGIN_ATTEMPTS_LIMIT;
    }
    
    /**
     * Increment login attempts
     */
    public static function incrementLoginAttempts(string $username): void
    {
        $key = 'login_attempts_' . md5($username);
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 0, 'time' => time()];
        }
        
        $_SESSION[$key]['count']++;
        $_SESSION[$key]['time'] = time();
    }
    
    /**
     * Reset login attempts
     */
    public static function resetLoginAttempts(string $username): void
    {
        $key = 'login_attempts_' . md5($username);
        unset($_SESSION[$key]);
    }
}
