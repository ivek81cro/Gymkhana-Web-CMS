<?php
define('IN_APP', true);
require __DIR__ . '/../includes/config.php';

// Log logout
if (is_admin()) {
    $username = $_SESSION['admin_username'] ?? 'unknown';
    log_activity('logout', "User: {$username}", 'success');
}

$_SESSION = [];
session_destroy();

// Preusmjeri na početnu stranicu
header('Location: index.php'); // ili 'Location: /' ako ti je site u rootu domene
exit;