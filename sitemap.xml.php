<?php
/**
 * Sitemap.xml Generator
 * 
 * Dynamically generates XML sitemap for search engines
 * Lists all public pages, published articles, and galleries
 * 
 * @see https://www.sitemaps.org/protocol.html
 * @output XML formatted sitemap
 */

require_once __DIR__ . '/includes/config.php';

// Set XML content type
header('Content-Type: application/xml; charset=utf-8');

// Start XML output
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
echo "\n";

// Base URL (change to your domain)
$base_url = 'https://www.motogymkhana.hr';

/**
 * Add URL to sitemap
 * 
 * @param string $loc URL location
 * @param string $lastmod Last modification date (Y-m-d format)
 * @param string $changefreq How frequently the page is likely to change
 * @param float $priority Priority of this URL relative to other URLs (0.0 to 1.0)
 */
function add_url($loc, $lastmod = null, $changefreq = 'monthly', $priority = 0.5) {
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($loc) . "</loc>\n";
    if ($lastmod) {
        echo "    <lastmod>" . htmlspecialchars($lastmod) . "</lastmod>\n";
    }
    echo "    <changefreq>" . htmlspecialchars($changefreq) . "</changefreq>\n";
    echo "    <priority>" . number_format($priority, 1) . "</priority>\n";
    echo "  </url>\n";
}

// Homepage
add_url($base_url . '/', date('Y-m-d'), 'daily', 1.0);

// Static pages
add_url($base_url . '/novosti.php', date('Y-m-d'), 'daily', 0.9);
add_url($base_url . '/edukacije.php', null, 'monthly', 0.8);
add_url($base_url . '/galerije.php', null, 'weekly', 0.8);

// Fetch all published articles
try {
    $stmt = $pdo->prepare("
        SELECT slug, datum, updated_at 
        FROM articles 
        WHERE status = 'objavljeno' 
        ORDER BY datum DESC
    ");
    $stmt->execute();
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($articles as $article) {
        // Use updated_at if available, otherwise datum
        $lastmod = $article['updated_at'] ?? $article['datum'];
        
        add_url(
            $base_url . '/clanak/' . $article['slug'],
            date('Y-m-d', strtotime($lastmod)),
            'monthly',
            0.7
        );
    }
} catch (PDOException $e) {
    // Continue even if articles fail
    error_log("Sitemap: Failed to fetch articles - " . $e->getMessage());
}

// Fetch all galleries
try {
    $stmt = $pdo->prepare("
        SELECT slug, created_at 
        FROM galleries 
        ORDER BY created_at DESC
    ");
    $stmt->execute();
    $galleries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($galleries as $gallery) {
        add_url(
            $base_url . '/galerija/' . $gallery['slug'],
            date('Y-m-d', strtotime($gallery['created_at'])),
            'yearly',
            0.6
        );
    }
} catch (PDOException $e) {
    // Continue even if galleries fail
    error_log("Sitemap: Failed to fetch galleries - " . $e->getMessage());
}

// Close XML
echo '</urlset>';
