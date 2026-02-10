<?php
require_once __DIR__ . '/includes/config.php';

$db = Database::getInstance();

echo "Updating database schema for Security Module...\n";

try {
    // 1. Add columns to admins table
    echo "Checking 'admins' table...\n";
    $columns = $db->fetchAll("SHOW COLUMNS FROM admins LIKE 'otp_code'");
    if (empty($columns)) {
        $db->query("ALTER TABLE admins ADD COLUMN otp_code VARCHAR(6) NULL");
        echo "Added 'otp_code' column.\n";
    } else {
        echo "'otp_code' column already exists.\n";
    }

    $columns = $db->fetchAll("SHOW COLUMNS FROM admins LIKE 'otp_expires_at'");
    if (empty($columns)) {
        $db->query("ALTER TABLE admins ADD COLUMN otp_expires_at DATETIME NULL");
        echo "Added 'otp_expires_at' column.\n";
    } else {
        echo "'otp_expires_at' column already exists.\n";
    }

    // 2. Add setting for 2FA
    echo "Checking 'settings' table...\n";
    $setting = $db->fetchOne("SELECT * FROM settings WHERE setting_key = 'security_2fa_enabled'");
    if (!$setting) {
        $db->insert('settings', [
            'setting_key' => 'security_2fa_enabled',
            'setting_value' => '0' // Disabled by default
        ]);
        echo "Added 'security_2fa_enabled' setting.\n";
    } else {
        echo "'security_2fa_enabled' setting already exists.\n";
    }

    echo "Schema update completed successfully!\n";

} catch (Exception $e) {
    echo "Error updating schema: " . $e->getMessage() . "\n";
    exit(1);
}
