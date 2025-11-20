<?php
/**
 * Activity Logger - Log Admin Actions and System Events
 * 
 * Features:
 * - Log admin actions (login, logout, CRUD operations)
 * - Structured log format (JSON for easy parsing)
 * - IP address and user agent tracking
 * - Log rotation support
 * - Query logs by date, action, user
 * 
 * @package MotoGymkhana
 * @since 1.0.0
 */

if (!defined('IN_APP')) {
    http_response_code(403);
    exit('Forbidden');
}

// =============================================================================
// CONFIGURATION
// =============================================================================

// Log file paths
define('ACTIVITY_LOG_FILE', __DIR__ . '/../logs/activity.log');
define('SECURITY_LOG_FILE', __DIR__ . '/../logs/security.log');
define('MAX_LOG_SIZE', 10 * 1024 * 1024); // 10 MB

// =============================================================================
// ACTIVITY LOGGING FUNCTIONS
// =============================================================================

/**
 * Log admin activity to file
 * 
 * @param string $action Action performed (login, create_article, delete_gallery, etc.)
 * @param string $details Additional details about the action
 * @param string $status Success or failure status
 * @return bool True if logged successfully
 * 
 * @example
 * log_activity('create_article', 'Article ID: 42, Title: "Europsko prvenstvo"', 'success');
 * log_activity('login_attempt', 'Username: admin', 'failed');
 */
function log_activity($action, $details = '', $status = 'success')
{
    // Prepare log entry
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'action' => $action,
        'details' => $details,
        'status' => $status,
        'user' => $_SESSION['admin_username'] ?? 'guest',
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ];

    // Convert to JSON (one line per entry)
    $logLine = json_encode($logEntry, JSON_UNESCAPED_UNICODE) . "\n";

    // Write to log file
    return writeLog(ACTIVITY_LOG_FILE, $logLine);
}

/**
 * Log security events (failed login, suspicious activity)
 * 
 * @param string $event Security event type
 * @param string $details Event details
 * @param string $severity Severity level (low, medium, high, critical)
 * @return bool True if logged successfully
 * 
 * @example
 * log_security('failed_login', 'Username: admin, Attempts: 5', 'high');
 * log_security('rate_limit_exceeded', 'IP: 192.168.1.100', 'medium');
 */
function log_security($event, $details = '', $severity = 'medium')
{
    // Prepare log entry
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'event' => $event,
        'details' => $details,
        'severity' => $severity,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ];

    // Convert to JSON
    $logLine = json_encode($logEntry, JSON_UNESCAPED_UNICODE) . "\n";

    // Write to log file
    return writeLog(SECURITY_LOG_FILE, $logLine);
}

/**
 * Write log entry to file with rotation support
 * 
 * @param string $logFile Path to log file
 * @param string $logLine Log entry to write
 * @return bool True if written successfully
 */
function writeLog($logFile, $logLine)
{
    // Ensure logs directory exists
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }

    // Check if log rotation is needed
    if (file_exists($logFile) && filesize($logFile) > MAX_LOG_SIZE) {
        rotateLog($logFile);
    }

    // Append to log file
    return @file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX) !== false;
}

/**
 * Rotate log file when it exceeds max size
 * 
 * @param string $logFile Path to log file
 * @return void
 */
function rotateLog($logFile)
{
    // Generate backup filename with timestamp
    $backupFile = $logFile . '.' . date('Y-m-d-His') . '.backup';
    
    // Rename current log to backup
    @rename($logFile, $backupFile);
    
    // Optionally: Compress old log files
    // if (function_exists('gzencode')) {
    //     $content = file_get_contents($backupFile);
    //     file_put_contents($backupFile . '.gz', gzencode($content));
    //     unlink($backupFile);
    // }
}

// =============================================================================
// LOG READING FUNCTIONS (For Admin Panel)
// =============================================================================

/**
 * Read recent activity logs
 * 
 * @param int $limit Maximum number of entries to return
 * @param string $action Filter by specific action (optional)
 * @return array Array of log entries
 * 
 * @example
 * $recentLogs = read_activity_logs(50);
 * $loginAttempts = read_activity_logs(100, 'login_attempt');
 */
function read_activity_logs($limit = 100, $action = null)
{
    if (!file_exists(ACTIVITY_LOG_FILE)) {
        return [];
    }

    // Read log file
    $lines = file(ACTIVITY_LOG_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return [];
    }

    // Reverse to get newest first
    $lines = array_reverse($lines);

    // Parse JSON entries
    $logs = [];
    foreach ($lines as $line) {
        $entry = json_decode($line, true);
        if ($entry === null) {
            continue; // Skip invalid JSON
        }

        // Filter by action if specified
        if ($action !== null && $entry['action'] !== $action) {
            continue;
        }

        $logs[] = $entry;

        // Stop when limit reached
        if (count($logs) >= $limit) {
            break;
        }
    }

    return $logs;
}

/**
 * Read recent security logs
 * 
 * @param int $limit Maximum number of entries to return
 * @param string $severity Filter by severity level (optional)
 * @return array Array of log entries
 */
function read_security_logs($limit = 100, $severity = null)
{
    if (!file_exists(SECURITY_LOG_FILE)) {
        return [];
    }

    // Read log file
    $lines = file(SECURITY_LOG_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return [];
    }

    // Reverse to get newest first
    $lines = array_reverse($lines);

    // Parse JSON entries
    $logs = [];
    foreach ($lines as $line) {
        $entry = json_decode($line, true);
        if ($entry === null) {
            continue;
        }

        // Filter by severity if specified
        if ($severity !== null && $entry['severity'] !== $severity) {
            continue;
        }

        $logs[] = $entry;

        // Stop when limit reached
        if (count($logs) >= $limit) {
            break;
        }
    }

    return $logs;
}

/**
 * Get activity statistics
 * 
 * @param int $days Number of days to analyze (default: 7)
 * @return array Statistics array with counts by action
 * 
 * @example
 * $stats = get_activity_stats(30);
 * // Returns: ['login' => 45, 'create_article' => 12, 'delete_gallery' => 3]
 */
function get_activity_stats($days = 7)
{
    if (!file_exists(ACTIVITY_LOG_FILE)) {
        return [];
    }

    $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));
    $stats = [];

    // Read log file
    $lines = file(ACTIVITY_LOG_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return [];
    }

    foreach ($lines as $line) {
        $entry = json_decode($line, true);
        if ($entry === null || $entry['timestamp'] < $cutoffDate) {
            continue;
        }

        $action = $entry['action'];
        if (!isset($stats[$action])) {
            $stats[$action] = 0;
        }
        $stats[$action]++;
    }

    // Sort by count descending
    arsort($stats);

    return $stats;
}
