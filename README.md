# Moto Gymkhana Croatia - Web CMS

Content Management System for Moto Gymkhana Croatia website. PHP-based CMS with MySQL database for managing articles, news, and galleries.

## 📋 Table of Contents

- [Features](#features)
- [Technologies](#technologies)
- [Project Structure](#project-structure)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)

## ✨ Features

### Public Section (Frontend)
- 🏠 Homepage with school information
- 📰 Overview of all news and articles
- 📄 Individual article display
- 🖼️ Image galleries with lightbox display
- 📱 Responsive design (Bootstrap 5)
- 🎨 Custom Montserrat font styling

### Admin Panel
- 🔐 Secure login
- ✍️ WYSIWYG article management
  - Create and edit articles
  - Categories and tags
  - Add images
  - Link galleries to articles
- 🖼️ Gallery management
  - Upload multiple images at once
  - Automatic image resizing (max 1920x1080px)
  - EXIF rotation correction
  - Thumbnail preview
  - Delete individual images

## 🛠️ Technologies

- **Backend:** PHP 7.4+
- **Database:** MySQL/MariaDB
- **Frontend:** 
  - Bootstrap 5.3.3
  - Vanilla JavaScript
  - Google Fonts (Montserrat)
- **Image processing:** GD Library

## 📁 Project Structure

```
Gymkhana-Web-CMS/
├── admin/                      # Admin panel
│   ├── index.php              # Redirect to login/news
│   ├── login.php              # Admin login
│   ├── logout.php             # Logout
│   ├── novosti.php            # Article management
│   ├── galerije.php           # Gallery list
│   └── galerija-uredi.php     # Edit gallery
│
├── includes/                   # Shared components
│   ├── config.php             # Database and configuration
│   └── nav.php                # Navigation
│
├── assets/                     # Static resources
│   ├── css/
│   │   └── style.css          # Custom styles
│   ├── img/                   # Static images (logo, etc.)
│   └── js/                    # JavaScript files
│
├── dev/                        # Development tools
│   ├── test-db.php            # Database connection test
│   └── check-and-add-gallery-column.php
│
├── uploads/                    # User uploads (gitignored)
│   └── gallery/               # Gallery images
│
├── index.php                   # Homepage
├── novosti.php                # News list
├── clanak.php                 # Article display
├── galerije.php               # Gallery list
├── galerija.php               # Individual gallery display
├── .htaccess                  # Apache configuration
└── .gitignore                 # Git ignore rules
```

## 🚀 Installation

### Prerequisites
- PHP 7.4 or newer
- MySQL/MariaDB
- Apache/Nginx web server
- GD Library (for image processing)

### Steps

1. **Clone the repository**
```bash
git clone https://github.com/ivek81cro/Gymkhana-Web-CMS.git
cd Gymkhana-Web-CMS
```

2. **Create database**
```sql
CREATE DATABASE gymkhana_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

3. **Import database structure** (SQL schema)
```sql
-- Articles/news table
CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(255) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL,
    excerpt TEXT,
    content TEXT NOT NULL,
    image VARCHAR(255),
    category VARCHAR(100),
    tags VARCHAR(255),
    gallery_id INT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_created (created_at),
    FOREIGN KEY (gallery_id) REFERENCES galleries(id) ON DELETE SET NULL
);

-- Galleries table
CREATE TABLE galleries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_slug (slug)
);

-- Gallery images table
CREATE TABLE gallery_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gallery_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    title VARCHAR(255),
    alt_text VARCHAR(255),
    sort_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (gallery_id) REFERENCES galleries(id) ON DELETE CASCADE,
    INDEX idx_gallery (gallery_id)
);
```

4. **Copy and configure config file**
```bash
cp includes/config.sample.php includes/config.php
```

5. **Edit `includes/config.php`**
```php
$dbHost = 'localhost';
$dbName = 'gymkhana_db';
$dbUser = 'your_username';
$dbPass = 'your_password';

define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'your_secure_password');
```

6. **Set permissions**
```bash
chmod 755 uploads/
chmod 755 uploads/gallery/
```

7. **Start web server**
```bash
# PHP built-in server for development
php -S localhost:8000

# Or configure Apache/Nginx virtual host
```

8. **Access the site**
- Public site: `http://localhost:8000/`
- Admin panel: `http://localhost:8000/admin/`

## ⚙️ Configuration

### Database
Edit `includes/config.php`:
```php
$dbHost = 'localhost';
$dbName = 'your_database';
$dbUser = 'your_username';
$dbPass = 'your_password';
```

### Admin access
```php
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'secure_password_here');
```

### Image resize settings
In `admin/galerija-uredi.php`, line ~150:
```php
$resized = resizeImage($tmpName, $fullPath, 1920, 1080, 85);
// Parameters: source, destination, maxWidth, maxHeight, quality (1-100)
```

## 📖 Usage

### Admin Panel

1. **Login**
   - Go to `/admin/`
   - Enter username and password

2. **Managing Articles**
   - Click "Novosti" in the menu
   - Click "+ Novi članak" to create
   - Fill in fields: title, content, category, tags
   - Select gallery (optional)
   - Click "Spremi članak"

3. **Managing Galleries**
   - Click "Galerije" in the menu
   - Click "+ Nova galerija"
   - Enter name and description
   - Upload images (multiple upload)
   - Images are automatically resized and optimized

4. **Linking Gallery to Article**
   - When editing an article, select gallery from dropdown menu
   - Gallery will automatically display below article content

### Public Site

- **Homepage:** School information, latest news
- **News:** List of all published articles
- **Galleries:** Overview of all photo galleries
- **Article:** Individual article display with gallery

## 🔒 Security

- ✅ PDO prepared statements against SQL injection attacks
- ✅ HTML escaping against XSS attacks
- ✅ Session-based authentication for admin panel
- ✅ Direct file access protection (`IN_APP` constant)
- ✅ File upload validation (type, extension)
- ⚠️ **IMPORTANT:** Change admin credentials in production!

## 🐛 Development

### Debug mode
For debugging, enable error display in `admin/galerija-uredi.php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', '1');
```

### Database testing
```bash
php dev/test-db.php
```

## 📝 License

Copyright © 2025 Moto Gymkhana Croatia

## 👨‍💻 Author

**ivek81cro**
- GitHub: [@ivek81cro](https://github.com/ivek81cro)

## 🤝 Contributing

Pull requests are welcome! For major changes, please open an issue first to discuss what you would like to change.

---

**Moto Gymkhana Croatia** - Škola sigurne vožnje 🏍️
