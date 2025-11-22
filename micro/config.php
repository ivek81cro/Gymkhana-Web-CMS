<?php
/**
 * Micro Leaderboard Configuration
 * 
 * Note: This module now uses main CMS authentication.
 * Session management and CSRF functions are handled by includes/config.php
 */

// Data storage configuration
define('DATA_DIR', __DIR__ . '/data');
define('DATA_FILE', DATA_DIR . '/data.json');

// Initialize data directory and file
if (!is_dir(DATA_DIR)) {
    @mkdir(DATA_DIR, 0755, true);
}

if (!file_exists(DATA_FILE)) {
    @file_put_contents(DATA_FILE, json_encode([]));
}
?>