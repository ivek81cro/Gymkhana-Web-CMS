<?php
/**
 * Error Handler - Centralized Error and Exception Management
 * 
 * Features:
 * - Custom error and exception handling
 * - Production/development mode detection
 * - Error logging to file
 * - User-friendly error pages
 * - Security: No sensitive information exposure
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

// Detect environment (check if on localhost/development)
define('IS_DEVELOPMENT', in_array($_SERVER['HTTP_HOST'] ?? '', [
    'localhost',
    '127.0.0.1',
    '::1',
    'gymkhana.local'
]));

// Error log file path
define('ERROR_LOG_FILE', __DIR__ . '/../logs/error.log');

// =============================================================================
// ERROR DISPLAY SETTINGS
// =============================================================================

if (IS_DEVELOPMENT) {
    // Development: Show all errors
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
} else {
    // Production: Hide errors from users, log them instead
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    ini_set('log_errors', '1');
}

// =============================================================================
// CUSTOM ERROR HANDLER
// =============================================================================

/**
 * Custom error handler - logs errors and displays user-friendly messages
 * 
 * @param int $errno Error level (E_WARNING, E_NOTICE, etc.)
 * @param string $errstr Error message
 * @param string $errfile File where error occurred
 * @param int $errline Line number where error occurred
 * @return bool True if error was handled
 */
function customErrorHandler($errno, $errstr, $errfile, $errline)
{
    // Don't handle errors suppressed with @ operator
    if (!(error_reporting() & $errno)) {
        return false;
    }

    // Map error level to string
    $errorTypes = [
        E_ERROR             => 'ERROR',
        E_WARNING           => 'WARNING',
        E_PARSE             => 'PARSE ERROR',
        E_NOTICE            => 'NOTICE',
        E_CORE_ERROR        => 'CORE ERROR',
        E_CORE_WARNING      => 'CORE WARNING',
        E_COMPILE_ERROR     => 'COMPILE ERROR',
        E_COMPILE_WARNING   => 'COMPILE WARNING',
        E_USER_ERROR        => 'USER ERROR',
        E_USER_WARNING      => 'USER WARNING',
        E_USER_NOTICE       => 'USER NOTICE',
        E_STRICT            => 'STRICT',
        E_RECOVERABLE_ERROR => 'RECOVERABLE ERROR',
        E_DEPRECATED        => 'DEPRECATED',
        E_USER_DEPRECATED   => 'USER DEPRECATED',
    ];

    $errorType = $errorTypes[$errno] ?? 'UNKNOWN';

    // Format error message for log
    $logMessage = sprintf(
        "[%s] %s: %s in %s on line %d\n",
        date('Y-m-d H:i:s'),
        $errorType,
        $errstr,
        $errfile,
        $errline
    );

    // Log error to file
    logError($logMessage);

    // In production, show user-friendly error page
    if (!IS_DEVELOPMENT) {
        // Only show error page for fatal errors
        if (in_array($errno, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
            showErrorPage(500, 'Dogodila se greška. Molimo pokušajte kasnije.');
            exit;
        }
    }

    // Return false to let PHP's default handler run as well (in dev mode)
    return false;
}

// =============================================================================
// CUSTOM EXCEPTION HANDLER
// =============================================================================

/**
 * Custom exception handler - logs exceptions and displays user-friendly messages
 * 
 * @param Throwable $exception The uncaught exception
 * @return void
 */
function customExceptionHandler($exception)
{
    // Format exception message for log
    $logMessage = sprintf(
        "[%s] EXCEPTION: %s in %s on line %d\nStack trace:\n%s\n",
        date('Y-m-d H:i:s'),
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
        $exception->getTraceAsString()
    );

    // Log exception to file
    logError($logMessage);

    // Show user-friendly error page
    if (IS_DEVELOPMENT) {
        // Development: Show detailed exception
        echo '<div style="background: #ffebee; border: 2px solid #c62828; padding: 20px; margin: 20px; font-family: monospace;">';
        echo '<h2 style="color: #c62828;">Uncaught Exception</h2>';
        echo '<p><strong>Message:</strong> ' . htmlspecialchars($exception->getMessage()) . '</p>';
        echo '<p><strong>File:</strong> ' . htmlspecialchars($exception->getFile()) . '</p>';
        echo '<p><strong>Line:</strong> ' . $exception->getLine() . '</p>';
        echo '<pre style="background: #fff; padding: 10px; overflow: auto;">' . htmlspecialchars($exception->getTraceAsString()) . '</pre>';
        echo '</div>';
    } else {
        // Production: Show generic error page
        showErrorPage(500, 'Dogodila se greška. Molimo pokušajte kasnije.');
    }

    exit;
}

// =============================================================================
// SHUTDOWN HANDLER (Catch Fatal Errors)
// =============================================================================

/**
 * Shutdown handler - catches fatal errors that can't be caught by error handler
 * 
 * @return void
 */
function shutdownHandler()
{
    $error = error_get_last();

    // Check if it's a fatal error
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        // Format error message for log
        $logMessage = sprintf(
            "[%s] FATAL ERROR: %s in %s on line %d\n",
            date('Y-m-d H:i:s'),
            $error['message'],
            $error['file'],
            $error['line']
        );

        // Log error
        logError($logMessage);

        // Show user-friendly error page
        if (!IS_DEVELOPMENT) {
            showErrorPage(500, 'Dogodila se kritična greška. Molimo pokušajte kasnije.');
        }
    }
}

// =============================================================================
// ERROR LOGGING
// =============================================================================

/**
 * Log error message to file
 * 
 * @param string $message Error message to log
 * @return void
 */
function logError($message)
{
    // Ensure logs directory exists
    $logDir = dirname(ERROR_LOG_FILE);
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }

    // Append to log file
    @file_put_contents(ERROR_LOG_FILE, $message, FILE_APPEND | LOCK_EX);
}

// =============================================================================
// ERROR PAGE DISPLAY
// =============================================================================

/**
 * Display user-friendly error page
 * 
 * @param int $code HTTP status code (404, 500, etc.)
 * @param string $message User-friendly error message
 * @return void
 */
function showErrorPage($code, $message)
{
    // Set HTTP response code
    http_response_code($code);

    // Check if custom error page exists
    $errorPagePath = __DIR__ . "/../errors/{$code}.php";
    
    if (file_exists($errorPagePath)) {
        // Include custom error page
        require $errorPagePath;
    } else {
        // Fallback: Display generic error page
        ?>
        <!DOCTYPE html>
        <html lang="hr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Greška <?= $code ?> - Moto Gymkhana Croatia</title>
            <style>
                body {
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: #333;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    min-height: 100vh;
                    margin: 0;
                    padding: 20px;
                }
                .error-container {
                    background: white;
                    border-radius: 10px;
                    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
                    padding: 40px;
                    max-width: 500px;
                    text-align: center;
                }
                .error-code {
                    font-size: 72px;
                    font-weight: bold;
                    color: #667eea;
                    margin: 0;
                }
                .error-message {
                    font-size: 20px;
                    color: #555;
                    margin: 20px 0;
                }
                .error-link {
                    display: inline-block;
                    margin-top: 20px;
                    padding: 12px 30px;
                    background: #667eea;
                    color: white;
                    text-decoration: none;
                    border-radius: 5px;
                    transition: background 0.3s;
                }
                .error-link:hover {
                    background: #764ba2;
                }
            </style>
        </head>
        <body>
            <div class="error-container">
                <h1 class="error-code"><?= $code ?></h1>
                <p class="error-message"><?= htmlspecialchars($message) ?></p>
                <a href="/" class="error-link">Natrag na početnu</a>
            </div>
        </body>
        </html>
        <?php
    }
}

// =============================================================================
// REGISTER HANDLERS
// =============================================================================

// Set custom error handler
set_error_handler('customErrorHandler');

// Set custom exception handler
set_exception_handler('customExceptionHandler');

// Set shutdown handler for fatal errors
register_shutdown_function('shutdownHandler');
