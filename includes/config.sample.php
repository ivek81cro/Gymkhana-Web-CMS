<?php
/**
 * Configuration Template for Moto Gymkhana Croatia CMS
 * 
 * INSTRUCTIONS:
 * 1. Copy this file to config.php: cp config.sample.php config.php
 * 2. Edit config.php with your actual database credentials
 * 3. Change admin username and generate new password hash
 * 4. NEVER commit config.php to git (it's in .gitignore)
 */

if (!defined('IN_APP')) {
    http_response_code(403);
    exit('Forbidden');
}

// =============================================================================
// DATABASE CONFIGURATION
// =============================================================================

$dbHost = 'localhost';              // Database host (usually 'localhost')
$dbName = 'your_database_name';     // Your database name
$dbUser = 'your_database_user';     // Your database username
$dbPass = 'your_database_password'; // Your database password

$dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (PDOException $e) {
    die('Greška spajanja na bazu: ' . $e->getMessage());
}

// =============================================================================
// HELPER FUNCTIONS
// =============================================================================

/**
 * Convert text to URL-friendly slug
 * 
 * @param string $text Input text (e.g., article title)
 * @return string URL-safe slug (lowercase, no spaces, no special chars)
 * 
 * @example
 * slugify("Europsko Prvenstvo 2025") => "europsko-prvenstvo-2025"
 */
function slugify(string $text): string
{
    $text = trim($text);
    // Remove diacritics (č->c, š->s, etc.)
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    // Replace non-alphanumeric chars with hyphens
    $text = preg_replace('~[^a-zA-Z0-9]+~', '-', $text);
    $text = trim($text, '-');
    $text = strtolower($text);

    // Fallback if empty
    if ($text === '') {
        $text = 'clanak-' . time();
    }

    return $text;
}

/**
 * Shorten text and add "...more" link
 * 
 * @param string $text Full text content (HTML tags will be stripped)
 * @param int $limit Maximum character length
 * @param string $url URL for the "more" link
 * @return string Shortened text with "...više" link (if needed)
 * 
 * @example
 * shorten_with_more_link($longText, 150, '/article/slug')
 * // Returns: "Short excerpt... <a href='/article/slug'>...više</a>"
 */
function shorten_with_more_link(string $text, int $limit, string $url): string
{
    // Strip HTML tags and trim whitespace
    $plain = trim(strip_tags($text));

    // If shorter than limit, return as-is (no "more" link needed)
    if (mb_strlen($plain, 'UTF-8') <= $limit) {
        return nl2br(htmlspecialchars($plain, ENT_QUOTES, 'UTF-8'));
    }

    // Cut to limit
    $short = mb_substr($plain, 0, $limit, 'UTF-8');

    // Try to cut at last word boundary (don't break mid-word)
    $short = preg_replace('/\s+\S*$/u', '', $short);

    $shortHtml = nl2br(htmlspecialchars($short, ENT_QUOTES, 'UTF-8'));

    $more = ' <a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" class="mg-read-more">...više</a>';

    return $shortHtml . $more;
}

// =============================================================================
// SESSION MANAGEMENT
// =============================================================================

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =============================================================================
// ADMIN AUTHENTICATION
// =============================================================================

// Admin credentials - CHANGE THESE IN PRODUCTION!
const ADMIN_USERNAME = 'admin';

// Password hash - Use includes/generate-password.php to generate a new hash
// Current password: change_this_password
// To generate hash: php includes/generate-password.php
const ADMIN_PASSWORD_HASH = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

/**
 * Check if current user is authenticated admin
 * 
 * @return bool True if logged in and verified
 */
function is_admin(): bool
{
    return !empty($_SESSION['is_admin']) && !empty($_SESSION['admin_verified']);
}

/**
 * Require admin authentication - redirect to login if not authenticated
 * 
 * @return void Exits with redirect if not authenticated
 */
function require_admin(): void
{
    if (!is_admin()) {
        // Detect if already in admin folder
        $inAdmin = strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false || 
                   strpos($_SERVER['SCRIPT_NAME'], '\\admin\\') !== false;
        $loginPath = $inAdmin ? 'login.php' : 'admin/login.php';
        header('Location: ' . $loginPath);
        exit;
    }
}

// =============================================================================
// CSRF PROTECTION
// =============================================================================

/**
 * Generate CSRF token for form protection
 * 
 * @return string 64-character hexadecimal token
 * 
 * @example
 * <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
 */
function generate_csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token from form submission
 * 
 * @param string $token Token from $_POST['csrf_token']
 * @return bool True if token is valid
 * 
 * @example
 * if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
 *     die('Invalid CSRF token');
 * }
 */
function verify_csrf_token(string $token): bool
{
    return !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Regenerate session ID (call after login to prevent session fixation)
 * 
 * @return void
 */
function regenerate_session(): void
{
    session_regenerate_id(true);
}
