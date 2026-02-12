<?php
// Create subscribers table
$_SERVER['HTTP_HOST'] = 'localhost';
require_once __DIR__ . '/includes/config.php';

$db = Database::getInstance();

$sql = "CREATE TABLE IF NOT EXISTS subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    status ENUM('active', 'unsubscribed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

try {
    $db->query($sql);
    echo "Subscribers table created successfully.\n";
} catch (Exception $e) {
    echo "Error creating table: " . $e->getMessage() . "\n";
}
?>
