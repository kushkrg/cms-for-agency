<?php
/**
 * Evolvcode CMS - Configuration File
 * 
 * This file contains all configuration settings for the application.
 * IMPORTANT: Update these values for your environment.
 */

// Error reporting (disable in production)
// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);

// Detect environment
$is_https = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || 
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

ini_set('session.cookie_secure', $is_https ? 1 : 0);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Site Configuration
// Dynamic URL detection
$is_local = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1', 'localhost:8000']);
$protocol = $is_https ? 'https://' : 'http://';

if ($is_local) {
    define('SITE_URL', $protocol . $_SERVER['HTTP_HOST']);
    // Local DB settings (keep as is)
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'evolvcode_cms');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_CHARSET', 'utf8mb4');
} else {
    // Production settings
    define('SITE_URL', 'https://evolvcode.in');
    
    // PRODUCTION DB CREDENTIALS - UPDATE THESE
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'u123456789_evolvcode'); // Update this
    define('DB_USER', 'u123456789_admin');     // Update this
    define('DB_PASS', 'YOUR_STRONG_PASSWORD'); // Update this
    define('DB_CHARSET', 'utf8mb4');
}

define('ADMIN_URL', SITE_URL . '/admin');
define('SITE_NAME', 'Evolvcode Solutions');
define('ADMIN_EMAIL', 'sales@evolvcode.com');

// Path Configuration
define('ROOT_PATH', dirname(__DIR__));
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('ADMIN_PATH', ROOT_PATH . '/admin');
define('UPLOADS_PATH', ROOT_PATH . '/assets/uploads');
define('ASSETS_PATH', ROOT_PATH . '/assets');

// URL paths for assets
define('ASSETS_URL', SITE_URL . '/assets');
define('UPLOADS_URL', SITE_URL . '/assets/uploads');

// Upload Configuration
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Security Configuration
define('CSRF_TOKEN_NAME', 'csrf_token');
define('LOGIN_ATTEMPTS_LIMIT', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// Pagination
define('ITEMS_PER_PAGE', 10);
define('ADMIN_ITEMS_PER_PAGE', 20);

// Include core files
require_once INCLUDES_PATH . '/Database.php';
require_once INCLUDES_PATH . '/Security.php';
require_once INCLUDES_PATH . '/Auth.php';
require_once INCLUDES_PATH . '/FileUpload.php';
require_once INCLUDES_PATH . '/helpers.php';
require_once INCLUDES_PATH . '/Recaptcha.php';
require_once INCLUDES_PATH . '/Backup.php';

// Initialize database connection
$db = Database::getInstance();
