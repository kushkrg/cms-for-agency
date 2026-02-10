<?php
require_once __DIR__ . '/includes/config.php';
$services = getServices(6);
echo "Count: " . count($services) . "\n";
print_r($services);
