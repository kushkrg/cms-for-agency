<?php
// Mock HTTP_HOST for CLI
$_SERVER['HTTP_HOST'] = 'localhost';
require_once __DIR__ . '/includes/config.php';

$db = Database::getInstance();

try {
    // Attempt to add column directly for MySQL
    $db->query("ALTER TABLE pages ADD COLUMN custom_script TEXT");
    echo "Column 'custom_script' added successfully.\n";

} catch (Exception $e) {
    if (strpos($e->getMessage(), "Duplicate column name") !== false) {
        echo "Column 'custom_script' already exists.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
