<?php
/**
 * Evolvcode CMS - Authentication Class
 * 
 * Handles admin user authentication with secure password hashing.
 * Includes role-based access control (RBAC).
 */

defined('ROOT_PATH') OR exit('Access Denied');

class Auth
{
    private static ?array $currentUser = null;
    
    /**
     * Attempt to log in a user
     * @return array{success: bool, message: string}
     */
    public static function login(string $username, string $password): array
    {
        // Check rate limiting
        if (!Security::checkLoginAttempts($username)) {
            return [
                'success' => false,
                'message' => 'Too many login attempts. Please try again later.'
            ];
        }
        
        $db = Database::getInstance();
        
        // Find user by username or email (backward-compatible if roles table doesn't exist)
        try {
            $user = $db->fetchOne(
                "SELECT a.*, r.slug as role_slug, r.permissions as role_permissions, r.name as role_name
                 FROM admins a 
                 LEFT JOIN roles r ON a.role_id = r.id 
                 WHERE (a.username = ? OR a.email = ?)",
                [$username, $username]
            );
        } catch (Exception $e) {
            $user = $db->fetchOne(
                "SELECT * FROM admins WHERE username = ? OR email = ?",
                [$username, $username]
            );
        }
        
        if (!$user) {
            Security::incrementLoginAttempts($username);
            return [
                'success' => false,
                'message' => 'Invalid username or password.'
            ];
        }

        // Check if user is active
        if (isset($user['status']) && $user['status'] === 'inactive') {
            return [
                'success' => false,
                'message' => 'Your account has been deactivated. Please contact an administrator.'
            ];
        }
        
        // Verify password
        if (!password_verify($password, $user['password_hash'])) {
            Security::incrementLoginAttempts($username);
            return [
                'success' => false,
                'message' => 'Invalid username or password.'
            ];
        }
        
        // Check if 2FA is enabled (skip on localhost where email won't work)
        $isLocal = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1', 'localhost:8000']);
        $is2faEnabled = !$isLocal && getSetting('security_2fa_enabled', '0') === '1';
        
        if ($is2faEnabled) {
            // Generate OTP
            $otp = sprintf("%06d", mt_rand(100000, 999999));
            $expiresAt = date('Y-m-d H:i:s', time() + 900); // 15 minutes
            
            // Save to database
            $db->update('admins', [
                'otp_code' => $otp,
                'otp_expires_at' => $expiresAt
            ], 'id = ?', [$user['id']]);
            
            // Send Email
            require_once __DIR__ . '/Mailer.php';
            $subject = 'Your Login OTP code - ' . getSetting('site_name', 'Evolvcode');
            $body = "<h2>Login OTP Verification</h2>";
            $body .= "<p>Hello " . htmlspecialchars($user['username']) . ",</p>";
            $body .= "<p>Your One-Time Password (OTP) for login is:</p>";
            $body .= "<h1 style='background: #f4f4f4; padding: 10px; display: inline-block; letter-spacing: 5px;'>" . $otp . "</h1>";
            $body .= "<p>This code is valid for 15 minutes.</p>";
            $body .= "<p>If you did not request this code, please contact support immediately.</p>";
            
            Mailer::sendMail($user['email'], $subject, $body, ['html' => true]);
            
            return [
                'success' => true,
                'status' => 'REQUIRE_OTP',
                'username' => $username, // Pass back to form
                'message' => 'OTP sent to your email.'
            ];
        }

        // Reset login attempts on successful login
        Security::resetLoginAttempts($username);
        
        // Update last login time
        $db->update('admins', ['last_login' => date('Y-m-d H:i:s')], 'id = ?', [$user['id']]);
        
        // Set session
        self::setSession($user);
        
        return [
            'success' => true,
            'status' => 'LOGGED_IN',
            'message' => 'Login successful.'
        ];
    }

    /**
     * Verify OTP and log user in
     */
    public static function verifyOtp(string $username, string $otp): array
    {
        $db = Database::getInstance();
        try {
            $user = $db->fetchOne(
                "SELECT a.*, r.slug as role_slug, r.permissions as role_permissions, r.name as role_name
                 FROM admins a 
                 LEFT JOIN roles r ON a.role_id = r.id 
                 WHERE a.username = ?",
                [$username]
            );
        } catch (Exception $e) {
            $user = $db->fetchOne("SELECT * FROM admins WHERE username = ?", [$username]);
        }
        
        if (!$user) {
            return ['success' => false, 'message' => 'User not found.'];
        }
        
        // Check OTP
        if ($user['otp_code'] !== $otp) {
            return ['success' => false, 'message' => 'Invalid OTP code.'];
        }
        
        // Check Expiration
        if (strtotime($user['otp_expires_at']) < time()) {
            return ['success' => false, 'message' => 'OTP has expired. Please request a new one.'];
        }
        
        // Success - clear OTP and login
        $db->update('admins', [
            'otp_code' => null,
            'otp_expires_at' => null,
            'last_login' => date('Y-m-d H:i:s')
        ], 'id = ?', [$user['id']]);
        
        Security::resetLoginAttempts($username);
        self::setSession($user);
        
        return ['success' => true, 'message' => 'Login successful.'];
    }

    /**
     * Resend OTP
     */
    public static function resendOtp(string $username): array
    {
        $db = Database::getInstance();
        $user = $db->fetchOne("SELECT * FROM admins WHERE username = ?", [$username]);
        
        if (!$user) {
            return ['success' => false, 'message' => 'User not found.'];
        }
        
        // Generate NEW OTP
        $otp = sprintf("%06d", mt_rand(100000, 999999));
        $expiresAt = date('Y-m-d H:i:s', time() + 900); // 15 minutes
        
        // Save to database
        $db->update('admins', [
            'otp_code' => $otp,
            'otp_expires_at' => $expiresAt
        ], 'id = ?', [$user['id']]);
        
        // Send Email
        require_once __DIR__ . '/Mailer.php';
        $subject = 'Resent Login OTP code - ' . getSetting('site_name', 'Evolvcode');
        $body = "<h2>Login OTP Verification</h2>";
        $body .= "<p>Hello " . htmlspecialchars($user['username']) . ",</p>";
        $body .= "<p>Your new One-Time Password (OTP) for login is:</p>";
        $body .= "<h1 style='background: #f4f4f4; padding: 10px; display: inline-block; letter-spacing: 5px;'>" . $otp . "</h1>";
        $body .= "<p>This code is valid for 15 minutes.</p>";
        
        Mailer::sendMail($user['email'], $subject, $body, ['html' => true]);
        
        return ['success' => true, 'message' => 'New OTP has been sent to your email.'];
    }
    
    /**
     * Set user session (includes role data)
     */
    private static function setSession(array $user): void
    {
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);
        
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_email'] = $user['email'];
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_login_time'] = time();
        
        // RBAC data
        $_SESSION['admin_role_slug'] = $user['role_slug'] ?? 'super_admin';
        $_SESSION['admin_role_name'] = $user['role_name'] ?? 'Super Admin';
        $_SESSION['admin_permissions'] = json_decode($user['role_permissions'] ?? '["*"]', true) ?: ['*'];
    }
    
    /**
     * Check if user is logged in
     */
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['admin_logged_in']) && 
               $_SESSION['admin_logged_in'] === true &&
               isset($_SESSION['admin_id']);
    }
    
    /**
     * Get current logged-in user (with role info)
     */
    public static function user(): ?array
    {
        if (!self::isLoggedIn()) {
            return null;
        }
        
        if (self::$currentUser === null) {
            $db = Database::getInstance();
            try {
                self::$currentUser = $db->fetchOne(
                    "SELECT a.id, a.username, a.email, a.role_id, a.status, a.created_at, a.last_login,
                            r.name as role_name, r.slug as role_slug, r.permissions as role_permissions
                     FROM admins a 
                     LEFT JOIN roles r ON a.role_id = r.id 
                     WHERE a.id = ?",
                    [$_SESSION['admin_id']]
                );
            } catch (Exception $e) {
                self::$currentUser = $db->fetchOne(
                    "SELECT id, username, email, created_at FROM admins WHERE id = ?",
                    [$_SESSION['admin_id']]
                );
            }
        }
        
        return self::$currentUser;
    }
    
    /**
     * Get current admin user (alias for user())
     */
    public static function getCurrentAdmin(): ?array
    {
        return self::user();
    }
    
    /**
     * Get current user ID
     */
    public static function userId(): ?int
    {
        return $_SESSION['admin_id'] ?? null;
    }
    
    /**
     * Get current username
     */
    public static function username(): ?string
    {
        return $_SESSION['admin_username'] ?? null;
    }

    /**
     * Get current user's role slug
     */
    public static function roleSlug(): string
    {
        return $_SESSION['admin_role_slug'] ?? 'viewer';
    }

    /**
     * Get current user's role name
     */
    public static function roleName(): string
    {
        return $_SESSION['admin_role_name'] ?? 'Viewer';
    }

    /**
     * Check if current user is a Super Admin
     */
    public static function isSuper(): bool
    {
        return self::roleSlug() === 'super_admin';
    }

    /**
     * Check if current user has permission for a given module.
     * Supports dot-notation like "contacts.view".
     * A wildcard "*" grants access to everything.
     */
    public static function hasPermission(string $module): bool
    {
        if (!self::isLoggedIn()) {
            return false;
        }

        $permissions = $_SESSION['admin_permissions'] ?? ['*'];

        // Wildcard = full access
        if (in_array('*', $permissions, true)) {
            return true;
        }

        // Exact match (e.g., "pages", "contacts.view")
        if (in_array($module, $permissions, true)) {
            return true;
        }

        // Base module match: if user has "contacts", they can also access "contacts.view"
        $baseParts = explode('.', $module);
        if (count($baseParts) > 1 && in_array($baseParts[0], $permissions, true)) {
            return true;
        }

        return false;
    }

    /**
     * Require a specific permission. Redirects with error if denied.
     */
    public static function requirePermission(string $module): void
    {
        if (!self::hasPermission($module)) {
            $_SESSION['flash_error'] = 'Access denied. You do not have permission to view this page.';
            Security::redirect(SITE_URL . '/admin/');
        }
    }
    
    /**
     * Log out user
     */
    public static function logout(): void
    {
        self::$currentUser = null;
        
        // Unset all session variables
        $_SESSION = [];
        
        // Delete session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        // Destroy session
        session_destroy();
    }
    
    /**
     * Require authentication - redirect to login if not logged in
     */
    public static function requireAuth(): void
    {
        if (!self::isLoggedIn()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            Security::redirect(SITE_URL . '/admin/login.php');
        }
    }

    /**
     * Alias for requireAuth
     */
    public static function requireLogin(): void
    {
        self::requireAuth();
    }
    
    /**
     * Redirect to intended page after login
     */
    public static function redirectToIntended(string $default = '/admin/'): void
    {
        $redirect = $_SESSION['redirect_after_login'] ?? $default;
        unset($_SESSION['redirect_after_login']);
        Security::redirect($redirect);
    }
    
    /**
     * Hash a password
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
    }
    
    /**
     * Verify a password against its hash
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
    
    /**
     * Check if password needs rehashing
     */
    public static function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, PASSWORD_DEFAULT, ['cost' => 12]);
    }
    
    /**
     * Update user password
     */
    public static function updatePassword(int $userId, string $newPassword): bool
    {
        $db = Database::getInstance();
        $hash = self::hashPassword($newPassword);
        
        return $db->update(
            'admins',
            ['password_hash' => $hash],
            'id = ?',
            [$userId]
        ) > 0;
    }
    
    /**
     * Create a new admin user
     */
    public static function createUser(string $username, string $email, string $password, int $roleId = 1, string $status = 'active'): int
    {
        $db = Database::getInstance();
        
        return $db->insert('admins', [
            'username' => $username,
            'email' => $email,
            'password_hash' => self::hashPassword($password),
            'role_id' => $roleId,
            'status' => $status
        ]);
    }

    /**
     * Auto-create roles table + update admins table if needed
     */
    public static function ensureRolesTable(): void
    {
        $db = Database::getInstance();

        // Create roles table if it doesn't exist
        $db->query("CREATE TABLE IF NOT EXISTS roles (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(50) UNIQUE NOT NULL,
            slug VARCHAR(50) UNIQUE NOT NULL,
            description VARCHAR(255),
            permissions TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        // Seed default roles if table is empty
        $count = $db->fetchOne("SELECT COUNT(*) as cnt FROM roles");
        if ($count && (int)$count['cnt'] === 0) {
            $db->query("INSERT INTO roles (name, slug, description, permissions) VALUES
                ('Super Admin', 'super_admin', 'Full access to everything', '[\"*\"]'),
                ('Editor', 'editor', 'Can manage content modules', '[\"dashboard\",\"pages\",\"services\",\"projects\",\"posts\",\"media\",\"contacts\",\"forms\",\"submissions\"]'),
                ('Viewer', 'viewer', 'Read-only access to dashboard and messages', '[\"dashboard\",\"contacts\",\"submissions\"]')
            ");
        }

        // Add role_id column to admins if missing
        try {
            $db->fetchOne("SELECT role_id FROM admins LIMIT 1");
        } catch (Exception $e) {
            $db->query("ALTER TABLE admins ADD COLUMN role_id INT DEFAULT NULL AFTER password_hash");
            $db->query("ALTER TABLE admins ADD COLUMN status ENUM('active','inactive') DEFAULT 'active' AFTER role_id");
            // Assign all existing admins as Super Admin
            $db->query("UPDATE admins SET role_id = 1 WHERE role_id IS NULL");
        }
    }

    /**
     * Get all roles (auto-creates table if needed)
     */
    public static function getRoles(): array
    {
        $db = Database::getInstance();
        try {
            return $db->fetchAll("SELECT * FROM roles ORDER BY id ASC");
        } catch (Exception $e) {
            // Table missing — auto-create it
            try {
                self::ensureRolesTable();
                return $db->fetchAll("SELECT * FROM roles ORDER BY id ASC");
            } catch (Exception $e2) {
                return [];
            }
        }
    }
}
