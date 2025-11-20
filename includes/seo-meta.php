<?php
/**
 * SEO Meta Tags Helper
 * 
 * Generira dinamičke meta tagove za SEO optimizaciju
 * Uključuje: title, description, keywords, Open Graph, Twitter Cards, Schema.org
 * 
 * @package MotoGymkhana
 * @since 1.0.0
 */

if (!defined('IN_APP')) {
    http_response_code(403);
    exit('Forbidden');
}

/**
 * Generate and output SEO meta tags
 * 
 * @param array $data SEO data array with keys:
 *   - title: Page title (optional, defaults to site name)
 *   - description: Page description (optional)
 *   - keywords: Comma-separated keywords (optional)
 *   - image: Featured image URL (optional)
 *   - url: Canonical URL (optional, auto-detected)
 *   - type: og:type (default: 'website', use 'article' for blog posts)
 *   - author: Author name for articles (optional)
 *   - published_time: ISO 8601 date for articles (optional)
 *   - section: Article section/category (optional)
 * 
 * @return void Outputs HTML meta tags
 * 
 * @example
 * // Homepage
 * generate_seo_meta([
 *     'title' => 'Moto Gymkhana Croatia - Škola sigurne vožnje',
 *     'description' => 'Moto Gymkhana Croatia je škola sigurne vožnje motocikla...',
 *     'keywords' => 'moto gymkhana, škola vožnje, motocikl, sigurna vožnja'
 * ]);
 * 
 * // Article
 * generate_seo_meta([
 *     'title' => 'Europsko prvenstvo 2025',
 *     'description' => 'Rezultati Europskog prvenstva u moto gymkhani...',
 *     'image' => 'https://example.com/image.jpg',
 *     'type' => 'article',
 *     'author' => 'Moto Gymkhana Croatia',
 *     'published_time' => '2025-11-20T14:30:00+01:00',
 *     'section' => 'Natjecanja'
 * ]);
 */
function generate_seo_meta($data = [])
{
    // Default values
    $siteName = 'Moto Gymkhana Croatia';
    $defaultTitle = $siteName . ' - Škola sigurne vožnje';
    $defaultDescription = 'Moto Gymkhana Croatia je škola sigurne vožnje motocikla. Učimo tehnike precizne vožnje, sigurnosnu vožnju i pripremu za natjecanja.';
    $defaultKeywords = 'moto gymkhana, škola vožnje motocikla, sigurna vožnja, hrvatska, gymkhana hrvatska, precizna vožnja';
    $defaultImage = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'www.motogymkhana.hr') . '/assets/img/logo-mgc.png';
    
    // Extract data
    $title = $data['title'] ?? $defaultTitle;
    $description = $data['description'] ?? $defaultDescription;
    $keywords = $data['keywords'] ?? $defaultKeywords;
    $image = $data['image'] ?? $defaultImage;
    $type = $data['type'] ?? 'website';
    $author = $data['author'] ?? $siteName;
    $publishedTime = $data['published_time'] ?? null;
    $section = $data['section'] ?? null;
    
    // Auto-detect canonical URL if not provided
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'www.motogymkhana.hr';
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    $url = $data['url'] ?? ($protocol . '://' . $host . $uri);
    
    // Clean title (remove site name if already present)
    $fullTitle = $title;
    if (stripos($title, $siteName) === false && $title !== $defaultTitle) {
        $fullTitle = $title . ' | ' . $siteName;
    }
    
    // Truncate description to optimal length (155-160 characters)
    if (mb_strlen($description) > 160) {
        $description = mb_substr($description, 0, 157) . '...';
    }
    
    // Output meta tags
    ?>
    <!-- Basic Meta Tags -->
    <title><?= htmlspecialchars($fullTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="description" content="<?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="keywords" content="<?= htmlspecialchars($keywords, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="author" content="<?= htmlspecialchars($author, ENT_QUOTES, 'UTF-8') ?>">
    <link rel="canonical" href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>">
    
    <!-- Open Graph Meta Tags (Facebook, LinkedIn) -->
    <meta property="og:site_name" content="<?= htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:title" content="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:description" content="<?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:type" content="<?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:url" content="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:image" content="<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:locale" content="hr_HR">
    
    <?php if ($type === 'article'): ?>
        <?php if ($publishedTime): ?>
            <meta property="article:published_time" content="<?= htmlspecialchars($publishedTime, ENT_QUOTES, 'UTF-8') ?>">
        <?php endif; ?>
        <?php if ($section): ?>
            <meta property="article:section" content="<?= htmlspecialchars($section, ENT_QUOTES, 'UTF-8') ?>">
        <?php endif; ?>
        <meta property="article:author" content="<?= htmlspecialchars($author, ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>">
    
    <!-- Additional SEO Meta Tags -->
    <meta name="robots" content="index, follow">
    <meta name="googlebot" content="index, follow">
    <meta name="language" content="Croatian">
    <meta name="revisit-after" content="7 days">
    <?php
}

/**
 * Generate Schema.org structured data for articles
 * 
 * @param array $article Article data from database
 * @return void Outputs JSON-LD script tag
 */
function generate_article_schema($article)
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'www.motogymkhana.hr';
    
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $article['title'],
        'description' => $article['excerpt'] ?? strip_tags(mb_substr($article['content'], 0, 200)),
        'datePublished' => date('c', strtotime($article['created_at'])),
        'author' => [
            '@type' => 'Organization',
            'name' => 'Moto Gymkhana Croatia'
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => 'Moto Gymkhana Croatia',
            'logo' => [
                '@type' => 'ImageObject',
                'url' => $protocol . '://' . $host . '/assets/img/logo-mgc.png'
            ]
        ]
    ];
    
    if (!empty($article['image'])) {
        $schema['image'] = $article['image'];
    }
    
    if (!empty($article['updated_at'])) {
        $schema['dateModified'] = date('c', strtotime($article['updated_at']));
    }
    
    echo '<script type="application/ld+json">' . "\n";
    echo json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    echo "\n" . '</script>' . "\n";
}

/**
 * Generate Schema.org structured data for organization (homepage)
 * 
 * @return void Outputs JSON-LD script tag
 */
function generate_organization_schema()
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'www.motogymkhana.hr';
    
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => 'Moto Gymkhana Croatia',
        'url' => $protocol . '://' . $host,
        'logo' => $protocol . '://' . $host . '/assets/img/logo-mgc.png',
        'description' => 'Škola sigurne vožnje motocikla i organizator moto gymkhana natjecanja u Hrvatskoj',
        'address' => [
            '@type' => 'PostalAddress',
            'addressCountry' => 'HR'
        ],
        'sameAs' => [
            // Dodaj social media linkove ako postoje
            // 'https://www.facebook.com/motogymkhanacroatia',
            // 'https://www.instagram.com/motogymkhanacroatia'
        ]
    ];
    
    echo '<script type="application/ld+json">' . "\n";
    echo json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    echo "\n" . '</script>' . "\n";
}
