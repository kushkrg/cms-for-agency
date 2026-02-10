<?php
// Simulate what happens if index.php handles the request for /about
$_SERVER['REQUEST_URI'] = '/about';
$_SERVER['REQUEST_METHOD'] = 'GET';

// If index.php is run directly for /about, it just shows homepage
ob_start();
require 'index.php';
$output = ob_get_clean();

if (strpos($output, 'Transform Your Digital Presence') !== false) {
    echo "CONFIRMED: Accessing /about via index.php renders Homepage content.\n";
    echo "This confirms that if the server logic directs all traffic to index.php without routing logic, you get the homepage.\n";
} else {
    echo "UNEXPECTED: Requesting index.php did not render homepage content?\n";
}
?>
