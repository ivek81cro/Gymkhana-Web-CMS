<?php
define('IN_APP', true);
require __DIR__ . '/../includes/config.php';

// Samo za prijavljene admine
require_admin();

header('Content-Type: application/json');

$galleryId = isset($_GET['gallery_id']) ? (int)$_GET['gallery_id'] : 0;

if ($galleryId <= 0) {
    echo json_encode(['error' => 'Nevažeći ID galerije.']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT id, filename
        FROM gallery_images
        WHERE gallery_id = :gallery_id
        ORDER BY id DESC
    ");
    
    $stmt->execute([':gallery_id' => $galleryId]);
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Dodaj punu putanju za svaki filename
    foreach ($images as &$img) {
        $img['image_path'] = 'uploads/gallery/' . $img['filename'];
        $img['display_name'] = $img['filename'];
    }
    
    echo json_encode([
        'success' => true,
        'images' => $images
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Greška pri učitavanju slika: ' . $e->getMessage()]);
}
