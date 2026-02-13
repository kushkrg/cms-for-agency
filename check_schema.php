<?php
$_SERVER['HTTP_HOST'] = 'localhost';
require_once __DIR__ . '/includes/config.php';
$db = Database::getInstance();
$columns = $db->fetchAll("DESCRIBE pages"); // Using DESCRIBE for MySQL
if (!$columns) {
    // Fallback if DESCRIBE fails or returns empty (e.g. if using SQLite wrapper but connected to MySQL)
    // The previous error message "Access denied for user... @'localhost'" suggests MySQL.
    // But let's try a standard query just in case.
    $stmt = $db->query("SELECT * FROM pages LIMIT 1");
    $meta = array();
    // PDO check for columns
    // actually let's just use the query "SHOW COLUMNS FROM pages"
    $columns = $db->fetchAll("SHOW COLUMNS FROM pages");
}

foreach ($columns as $col) {
    echo $col['Field'] . " (" . $col['Type'] . ")\n";
}
