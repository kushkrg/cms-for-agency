<?php
require_once 'includes/config.php';
require_once 'includes/Database.php';

$db = Database::getInstance();
try {
    $columns = $db->fetchAll("DESCRIBE projects");
    echo "Columns in projects table:\n";
    foreach ($columns as $col) {
        echo $col['Field'] . " (" . $col['Type'] . ")\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
