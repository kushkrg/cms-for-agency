<?php
/**
 * Evolvcode CMS - Admin Logout
 */

require_once __DIR__ . '/../includes/config.php';

Auth::logout();

header('Location: ' . ADMIN_URL . '/login.php');
exit;
