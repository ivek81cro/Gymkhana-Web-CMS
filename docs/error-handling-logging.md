# Error Handling & Logging System - Documentation

## Overview

The CMS now includes a comprehensive error handling and logging system to improve debugging, security monitoring, and system maintenance.

## Components

### 1. Error Handler (`includes/error-handler.php`)

**Purpose**: Centralized error and exception handling with user-friendly error pages.

**Features**:
- **Environment Detection**: Automatically detects development vs. production mode
- **Custom Error Handler**: Catches PHP errors (warnings, notices, fatal errors)
- **Custom Exception Handler**: Catches uncaught exceptions
- **Shutdown Handler**: Catches fatal errors that occur during script shutdown
- **Error Logging**: Logs all errors to `logs/error.log`
- **User-Friendly Error Pages**: Shows generic error pages in production, detailed errors in development

**Configuration**:
```php
// Detects development environment automatically
define('IS_DEVELOPMENT', in_array($_SERVER['HTTP_HOST'] ?? '', [
    'localhost',
    '127.0.0.1',
    '::1',
    'gymkhana.local'
]));
```

**Error Pages**:
- `errors/404.php` - Page not found
- `errors/500.php` - Internal server error

**Usage**:
Automatically loaded via `includes/config.php`. No manual initialization required.

**Development Mode**:
- Shows detailed error messages
- Displays file paths and line numbers
- Shows stack traces for exceptions

**Production Mode**:
- Hides sensitive error information
- Logs errors to file
- Shows user-friendly error pages
- Sets proper HTTP status codes

---

### 2. Activity Logger (`includes/logger.php`)

**Purpose**: Track admin actions and security events for audit and debugging.

**Features**:
- **Activity Logging**: Log all admin actions (login, CRUD operations)
- **Security Logging**: Track security events (failed logins, rate limiting)
- **JSON Format**: Structured logs for easy parsing
- **Log Rotation**: Automatically rotates logs when they exceed 10MB
- **IP Tracking**: Records IP address and user agent
- **Log Reading**: Functions to query and filter logs

**Log Files**:
- `logs/activity.log` - Admin activity logs
- `logs/security.log` - Security event logs

**Log Entry Format** (JSON):
```json
{
    "timestamp": "2025-11-20 14:30:45",
    "action": "create_article",
    "details": "Article ID: 42, Title: Europsko prvenstvo 2025",
    "status": "success",
    "user": "admin",
    "ip": "192.168.1.100",
    "user_agent": "Mozilla/5.0..."
}
```

**Functions**:

#### `log_activity($action, $details, $status)`
Log admin activity.

**Parameters**:
- `$action` (string): Action type (login, create_article, delete_gallery, etc.)
- `$details` (string): Additional details about the action
- `$status` (string): 'success' or 'failed'

**Example**:
```php
log_activity('create_article', 'Article ID: 42, Title: "Europsko prvenstvo"', 'success');
log_activity('delete_gallery', 'Gallery ID: 15, Name: "Zagreb 2024"', 'success');
log_activity('login', 'User: admin', 'success');
```

#### `log_security($event, $details, $severity)`
Log security events.

**Parameters**:
- `$event` (string): Security event type
- `$details` (string): Event details
- `$severity` (string): 'low', 'medium', 'high', 'critical'

**Example**:
```php
log_security('failed_login', 'Username: admin, Attempts: 5', 'high');
log_security('rate_limit_exceeded', 'IP: 192.168.1.100', 'medium');
```

#### `read_activity_logs($limit, $action)`
Read recent activity logs.

**Parameters**:
- `$limit` (int): Maximum number of entries (default: 100)
- `$action` (string|null): Filter by specific action (optional)

**Returns**: Array of log entries

**Example**:
```php
$recentLogs = read_activity_logs(50);
$loginAttempts = read_activity_logs(100, 'login');
```

#### `read_security_logs($limit, $severity)`
Read recent security logs.

**Parameters**:
- `$limit` (int): Maximum number of entries (default: 100)
- `$severity` (string|null): Filter by severity level (optional)

**Returns**: Array of log entries

#### `get_activity_stats($days)`
Get activity statistics for the last N days.

**Parameters**:
- `$days` (int): Number of days to analyze (default: 7)

**Returns**: Associative array with counts by action

**Example**:
```php
$stats = get_activity_stats(30);
// Returns: ['login' => 45, 'create_article' => 12, 'delete_gallery' => 3]
```

---

### 3. Logs Viewer (`admin/logs.php`)

**Purpose**: Admin interface for viewing and filtering logs.

**Features**:
- View activity logs and security logs
- Filter by action type or severity level
- Adjust number of displayed entries
- Statistics overview (last 7 days)
- Color-coded status and severity indicators

**Access**: Only available to authenticated admin users.

**URL**: `https://yoursite.com/admin/logs.php`

**Filters**:
- **Activity Logs**: Filter by action (login, create_article, delete_gallery, etc.)
- **Security Logs**: Filter by severity (low, medium, high, critical)
- **Limit**: Number of entries to display (50, 100, 200, 500)

---

## Integration

### Logged Actions

#### Authentication
- ✅ `login` - Successful login
- ✅ `logout` - User logout
- ✅ `failed_login` (security) - Failed login attempt

#### Articles
- ✅ `create_article` - New article created
- ✅ `update_article` - Article updated
- ✅ `delete_article` - Article deleted

#### Galleries
- ✅ `create_gallery` - New gallery created
- ✅ `update_gallery` - Gallery updated
- ✅ `delete_gallery` - Gallery deleted
- ✅ `upload_images` - Images uploaded to gallery

### Files Modified

1. **includes/config.sample.php** - Added error handler and logger includes
2. **admin/login.php** - Added login/failed login logging
3. **admin/logout.php** - Added logout logging
4. **admin/novosti.php** - Added article CRUD logging
5. **admin/galerije.php** - Added gallery deletion logging
6. **admin/galerija-uredi.php** - Added gallery create/update and image upload logging
7. **includes/nav.php** - Added logs link in admin navigation
8. **.htaccess** - Added protection for logs/ and errors/ directories

---

## Security

### Log File Protection

Log files are protected via `.htaccess`:
```apache
RewriteRule ^logs/ - [F,L]
```

This prevents direct access to log files via HTTP.

### Access Control

- Only authenticated admin users can view logs via `admin/logs.php`
- Log files are stored outside the web root (recommended) or protected via `.htaccess`
- Sensitive information is logged but not displayed in production error pages

---

## Maintenance

### Log Rotation

Logs are automatically rotated when they exceed 10MB. Backup files are created with timestamp:
```
activity.log.2025-11-20-143045.backup
```

### Manual Log Cleanup

To manually clean old logs:
```bash
# View log size
ls -lh logs/

# Remove old backup logs (older than 30 days)
find logs/ -name "*.backup" -mtime +30 -delete

# Clear all logs (USE WITH CAUTION)
rm logs/*.log
```

### Log Compression (Optional)

To enable automatic compression of rotated logs, uncomment in `includes/logger.php`:
```php
if (function_exists('gzencode')) {
    $content = file_get_contents($backupFile);
    file_put_contents($backupFile . '.gz', gzencode($content));
    unlink($backupFile);
}
```

---

## Troubleshooting

### Logs Not Being Written

1. **Check Directory Permissions**:
   ```bash
   chmod 755 logs/
   chmod 644 logs/.gitignore
   ```

2. **Check File Permissions**:
   ```bash
   chmod 644 logs/activity.log
   chmod 644 logs/security.log
   ```

3. **Verify Error Handler Is Loaded**:
   Check `includes/config.php` contains:
   ```php
   require_once __DIR__ . '/error-handler.php';
   require_once __DIR__ . '/logger.php';
   ```

### Error Pages Not Showing

1. **Check .htaccess Configuration**:
   Ensure custom error pages are uncommented in `.htaccess`

2. **Verify Error Files Exist**:
   ```bash
   ls -la errors/404.php
   ls -la errors/500.php
   ```

3. **Check PHP Error Display Settings**:
   In production, ensure:
   ```php
   ini_set('display_errors', '0');
   ```

### Logs Viewer Not Accessible

1. **Check Admin Authentication**:
   - Ensure you're logged in as admin
   - Check `$_SESSION['is_admin']` is set

2. **Check File Permissions**:
   ```bash
   chmod 644 admin/logs.php
   ```

---

## Best Practices

1. **Regular Monitoring**: Review security logs weekly for suspicious activity
2. **Log Retention**: Keep logs for at least 30 days for audit purposes
3. **Backup Logs**: Include logs in regular backup routine
4. **Test Error Pages**: Test 404 and 500 error pages in staging environment
5. **Review Activity**: Check activity logs after major changes

---

## Future Enhancements

Potential improvements:
- Email notifications for critical security events
- Log analysis dashboard with charts
- Export logs to CSV/JSON
- Advanced search with date range filters
- Automated log archiving to external storage
- Integration with monitoring services (Sentry, Rollbar)

---

## Support

For issues or questions:
- Check logs: `logs/error.log`
- Review this documentation
- Check PHP error logs: `php -i | grep error_log`
