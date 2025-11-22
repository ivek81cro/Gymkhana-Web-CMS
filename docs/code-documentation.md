# Code Documentation Index

## Configuration Files

### `includes/config.php`
Main configuration file containing:
- **Database connection** - PDO setup with UTF-8 support
- **Helper functions** - `slugify()`, `shorten_with_more_link()`
- **Authentication** - Admin login verification
- **CSRF protection** - Token generation and validation
- **Session management** - Session start and regeneration

**Template:** `includes/config.sample.php`

**Key Functions:**
- `slugify(string $text): string` - Converts text to URL-safe slug
- `shorten_with_more_link(string $text, int $limit, string $url): string` - Truncates text with "more" link
- `is_admin(): bool` - Check if user is authenticated
- `require_admin(): void` - Enforce admin authentication (redirects if not logged in)
- `generate_csrf_token(): string` - Generate CSRF token for forms
- `verify_csrf_token(string $token): bool` - Validate CSRF token
- `regenerate_session(): void` - Regenerate session ID after login

---

## Admin Panel

### `admin/novosti.php`
Article management interface with:
- Create/edit/delete articles
- Draft/published status toggle
- Category dropdown (Novosti, Edukacija, Natjecanja, Ostalo)
- TinyMCE WYSIWYG editor
- Image picker from galleries
- CSRF protection on all forms
- Article list table with status badges

**POST Parameters:**
- `title` - Article title (required)
- `slug` - URL slug (auto-generated from title if empty)
- `excerpt` - Short summary
- `content` - Full article content (HTML from TinyMCE)
- `image` - Featured image path
- `category` - Article category
- `status` - 'draft' or 'published'
- `tags` - Comma-separated tags
- `gallery_id` - Linked gallery ID (optional)
- `date` - Publication date
- `csrf_token` - CSRF protection token

**GET Parameters:**
- `id` - Article ID for editing
- `delete` - Article ID to delete
- `token` - CSRF token for delete action

---

### `admin/galerija-uredi.php`
Gallery management with image upload:
- Create/edit galleries
- Multi-file upload
- Automatic image resizing (max 1920x1080)
- EXIF orientation correction
- Image deletion
- Debug mode (error display)

**Key Function:**
```php
resizeImage(
    string $source,      // Path to original
    string $destination, // Path to save
    int $maxWidth = 1920,
    int $maxHeight = 1080,
    int $quality = 85    // JPEG quality 1-100
): bool
```

**Features:**
- Maintains aspect ratio
- Preserves PNG transparency
- Handles JPEG, PNG, GIF, WebP
- EXIF rotation fix (mobile photos)
- Memory efficient

---

### `admin/ajax-get-gallery-images.php`
AJAX endpoint for image picker.

**See:** `docs/API-ajax-get-gallery-images.md` for full documentation

**Quick Reference:**
```javascript
POST ajax-get-gallery-images.php
Body: gallery_id=5

Response:
{
  "success": true,
  "images": [
    {"id": 123, "filename": "photo.jpg", "image_path": "uploads/gallery/photo.jpg"}
  ]
}
```

---

## Public Pages

### `index.php`
Homepage with:
- Latest 3 news articles (Novosti category, published only)
- Latest 3 education articles (Edukacija category, published only)
- Static content sections
- Navigation and footer includes

### `novosti.php`
News listing page:
- Displays all published articles in Novosti category
- Card grid layout
- Excerpt with "...više" link
- Date badge on each card

### `edukacije.php`
Education articles page:
- Displays all published articles in Edukacija category
- Same layout as novosti.php
- Filtered by `status = 'published'`

### `clanak.php`
Individual article display:
- Fetches by slug (`?slug=article-slug`)
- Shows full content with HTML formatting
- Displays linked gallery if present
- 404 handling for missing articles

---

## Components

### `includes/nav.php`
Navigation bar component:
- Detects current page
- Detects if in admin folder (`$inAdmin`)
- Dynamic `$baseUrl` for correct paths
- Responsive Bootstrap navbar

### `includes/footer.php`
Footer component:
- 4-column layout
- Dynamic year display
- Social media badges
- Privacy policy link
- Works in both root and admin contexts (`$baseUrl`)

---

## Helper Scripts

### `includes/generate-password.php`
Command-line tool to generate password hashes:
```bash
php includes/generate-password.php
```

Output:
```
Password hash for 'your_password':
$2y$10$...hash...

Copy this to includes/config.php:
const ADMIN_PASSWORD_HASH = '$2y$10$...hash...';
```

### `dev/migration-add-status-column.sql`
Database migration to add status column:
```sql
ALTER TABLE articles 
ADD COLUMN status ENUM('draft', 'published') NOT NULL DEFAULT 'published'
AFTER category;

ALTER TABLE articles 
ADD INDEX idx_status_category (status, category);
```

**Instructions:** See `dev/migration-instructions.md`

---

## Security Features

### CSRF Protection
All forms include CSRF tokens:
```php
<!-- In form -->
<input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">

// In processing
if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    die('Invalid CSRF token');
}
```

### Password Hashing
```php
// Store hash in config.php
const ADMIN_PASSWORD_HASH = '$2y$10$...';

// Verify on login
if (password_verify($password, ADMIN_PASSWORD_HASH)) {
    // Success
}
```

### Rate Limiting
Login page limits to 5 attempts per 15 minutes:
```php
$_SESSION['login_attempts'][] = time();
if (count($_SESSION['login_attempts']) >= 5) {
    $error = 'Too many attempts. Wait 15 minutes.';
}
```

### SQL Injection Protection
All queries use PDO prepared statements:
```php
$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = :id");
$stmt->execute([':id' => $articleId]);
```

### XSS Protection
All output is HTML-escaped:
```php
<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
```

---

## Micro Leaderboard Module

### `micro/index.php`
Public leaderboard display:
- Integrated with main CMS navigation and design
- Real-time timing results for 4 tracks (GP8-1, GP8-2, Track 1, Track 2)
- Sortable by track
- DNF (Did Not Finish) support
- Time format: MM:ss:mmm
- Admin link visible only to authenticated admins
- SEO optimized with meta tags

### `micro/admin.php`
Admin panel for leaderboard management:
- Dual authentication (standalone + main CMS)
- CRUD operations for competitor data
- Modal-based editing interface
- Time input with automatic formatting
- DNF checkbox per track
- CSRF protection
- Rate limiting (5 attempts = 30s lock)
- Activity logging via main CMS logger

**Authentication:**
- Standalone: `ADMIN_USER` / `ADMIN_PASS_HASH` in `micro/config.php`
- Main CMS: Automatic access if logged in as admin (`is_admin()`)

### `micro/lib.php`
Helper functions:
- `read_data(): array` - Read JSON timing data
- `write_data(array $arr): bool` - Atomic file writes with flock()
- `parse_time_ms(string $str): int|false` - Parse MM:ss:mmm to milliseconds
- `format_time_colon(int $ms): string` - Format milliseconds to MM:ss:mmm
- `sanitize_text(string $str): string` - XSS protection
- `csrf_token(): string` - CSRF token generation
- `csrf_check(string $token): bool` - CSRF validation
- `is_logged_in(): bool` - Micro login check
- `ensure_session(): void` - Secure session initialization

### `micro/config.php`
Simplified configuration:
- `DATA_DIR` - Directory for JSON data storage
- `DATA_FILE` - Path to JSON data file (`data/data.json`)

**Note:** Authentication is now handled by main CMS (`includes/config.php`)

**See:** `docs/MICRO-LEADERBOARD.md` for full documentation

---

## File Structure

```
Gymkhana-Web-CMS/
├── admin/                              # Admin panel
│   ├── index.php                       # Redirect to login/news
│   ├── login.php                       # Admin authentication
│   ├── logout.php                      # Session destroy
│   ├── novosti.php                     # Article CRUD
│   ├── galerije.php                    # Gallery list
│   ├── galerija-uredi.php              # Gallery upload
│   ├── ajax-get-gallery-images.php     # AJAX image picker
│   └── logs.php                        # Activity/security logs viewer
│
├── includes/                           # Shared code
│   ├── config.php                      # Database & auth (gitignored)
│   ├── config.sample.php               # Config template
│   ├── nav.php                         # Navigation component
│   ├── footer.php                      # Footer component
│   ├── seo-meta.php                    # SEO meta tags helper
│   ├── error-handler.php               # Error/exception handling
│   ├── logger.php                      # Activity/security logging
│   └── generate-password.php           # Password hash generator
│
├── micro/                              # Leaderboard module
│   ├── index.php                       # Public leaderboard (integrated)
│   ├── admin.php                       # Admin panel (integrated)
│   ├── lib.php                         # Helper functions
│   ├── config.php                      # Micro config (gitignored)
│   ├── assets/                         # Static resources
│   └── data/
│       ├── data.json                   # JSON database (gitignored)
│       └── .htaccess                   # Directory protection
│
├── errors/                             # Error pages
│   ├── 404.php                         # Not Found page
│   └── 500.php                         # Server Error page
│
├── assets/                             # Static resources
│   ├── css/style.css                   # Custom styles
│   ├── img/                            # Static images
│   └── js/tinymce/                     # TinyMCE editor
│
├── uploads/                            # User uploads (gitignored)
│   └── gallery/                        # Gallery images
│
├── logs/                               # Log files (gitignored)
│   ├── activity.log                    # Admin activity log
│   ├── security.log                    # Security events log
│   └── error.log                       # PHP errors log
│
├── dev/                                # Development tools
│   ├── migration-add-status-column.sql # Status field migration
│   ├── migration-instructions.md       # Migration guide
│   └── test-db.php                     # DB connection test
│
├── docs/                               # Documentation
│   ├── API-ajax-get-gallery-images.md  # AJAX endpoint docs
│   ├── code-documentation.md           # This file
│   ├── MICRO-LEADERBOARD.md            # Micro module docs
│   └── UPUTE-CONFIG-UPDATE.md          # Config update instructions
│
├── index.php                           # Homepage
├── novosti.php                         # News list
├── edukacije.php                       # Education list
├── clanak.php                          # Article display
├── galerije.php                        # Gallery list
├── galerija.php                        # Gallery display
├── privacy-policy.php                  # GDPR privacy policy
├── sitemap.xml.php                     # Dynamic XML sitemap
├── robots.txt                          # Search engine rules
├── .htaccess                           # Apache config (SEO, security)
├── .gitignore                          # Git ignore rules
└── README.md                           # Installation guide
```

---

## Database Schema

### `articles`
| Column | Type | Description |
|--------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| slug | VARCHAR(255) UNIQUE | URL-friendly identifier |
| title | VARCHAR(255) | Article title |
| excerpt | TEXT | Short summary |
| content | TEXT | Full article HTML |
| image | VARCHAR(255) | Featured image path |
| category | VARCHAR(100) | Article category |
| status | ENUM('draft','published') | Publication status |
| tags | VARCHAR(255) | Comma-separated tags |
| gallery_id | INT NULL | Foreign key to galleries |
| created_at | DATETIME | Publication date |

**Indexes:**
- `idx_slug` (slug)
- `idx_created` (created_at)
- `idx_status_category` (status, category)

### `galleries`
| Column | Type | Description |
|--------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| name | VARCHAR(255) | Gallery name |
| slug | VARCHAR(255) UNIQUE | URL-friendly identifier |
| description | TEXT | Gallery description |
| created_at | DATETIME | Creation date |

**Indexes:**
- `idx_slug` (slug)

### `gallery_images`
| Column | Type | Description |
|--------|------|-------------|
| id | INT AUTO_INCREMENT | Primary key |
| gallery_id | INT | Foreign key to galleries |
| filename | VARCHAR(255) | Image filename |
| title | VARCHAR(255) | Image title |
| alt_text | VARCHAR(255) | Alt text for accessibility |
| sort_order | INT | Display order |
| created_at | DATETIME | Upload date |

**Indexes:**
- `idx_gallery` (gallery_id)

---

## Common Tasks

### Add a new category
1. Update dropdown in `admin/novosti.php` line ~380
2. Add filtering in public pages if needed

### Change image upload size
Edit `admin/galerija-uredi.php` line ~250:
```php
$resized = resizeImage($tmpName, $fullPath, 1920, 1080, 85);
//                                          ↑     ↑     ↑
//                                      maxWidth maxHeight quality
```

### Change admin password
```bash
php includes/generate-password.php
# Copy new hash to includes/config.php
```

### Debug database queries
In config.php, add:
```php
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
error_reporting(E_ALL);
ini_set('display_errors', '1');
```

---

## Support

- **GitHub:** [@ivek81cro](https://github.com/ivek81cro)
- **Issues:** [GitHub Issues](https://github.com/ivek81cro/Gymkhana-Web-CMS/issues)
- **Docs:** See `/docs/` folder
