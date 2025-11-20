<?php
/**
 * AJAX Endpoint: Get Gallery Images
 * 
 * Fetches all images from a specific gallery for the image picker modal.
 * Used in admin/novosti.php when selecting an article image from a gallery.
 * 
 * @method POST
 * @param int gallery_id - ID of the gallery to fetch images from
 * @return JSON - {success: bool, images: array} or {error: string}
 * 
 * @example
 * POST ajax-get-gallery-images.php
 * Body: gallery_id=5
 * 
 * Response:
 * {
 *   "success": true,
 *   "images": [
 *     {"id": 123, "filename": "IMG_1234.jpg", "image_path": "uploads/gallery/IMG_1234.jpg", "display_name": "IMG_1234.jpg"}
 *   ]
 * }
 * 
 * @requires Admin authentication
 * @see docs/API-ajax-get-gallery-images.md for full documentation
 */

define('IN_APP', true);
require __DIR__ . '/../includes/config.php';

// Authentication: Only logged-in admins can access this endpoint
require_admin();

// Set JSON response header
header('Content-Type: application/json');

// Get gallery_id from POST or GET request (POST preferred, but supports both)
$galleryId = isset($_POST['gallery_id']) ? (int)$_POST['gallery_id'] : (isset($_GET['gallery_id']) ? (int)$_GET['gallery_id'] : 0);

// Validate gallery_id
if ($galleryId <= 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Gallery ID is required'
    ]);
    exit;
}

try {
    // Fetch all images for the specified gallery
    // Note: Queries 'filename' column (not 'image_path') from gallery_images table
    $stmt = $pdo->prepare("
        SELECT id, filename
        FROM gallery_images
        WHERE gallery_id = :gallery_id
        ORDER BY sort_order, id
    ");
    
    $stmt->execute([':gallery_id' => $galleryId]);
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Build full image path for each image
    // Filename is stored in DB, but we need full path for <img> src attribute
    foreach ($images as &$img) {
        $img['image_path'] = 'uploads/gallery/' . $img['filename'];
        $img['display_name'] = $img['filename']; // For display in modal
    }
    unset($img); // Break reference to avoid issues
    
    // Return success response
    echo json_encode([
        'success' => true,
        'images' => $images
    ]);
    
} catch (PDOException $e) {
    // Database error - return error message
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
