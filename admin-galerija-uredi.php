<?php
// Admin - kreiranje i uređivanje galerije + upload slika

define('IN_APP', true);
require __DIR__ . '/config.php';
require_admin();

/**
 * Resize slike uz održavanje omjera
 * @param string $source Putanja do originalne slike
 * @param string $destination Putanja gdje spremiti resizanu sliku
 * @param int $maxWidth Maksimalna širina
 * @param int $maxHeight Maksimalna visina
 * @param int $quality Kvaliteta (1-100)
 * @return bool Success
 */
function resizeImage($source, $destination, $maxWidth = 1920, $maxHeight = 1080, $quality = 85) {
    $imageInfo = @getimagesize($source);
    if (!$imageInfo) {
        return false;
    }

    list($origWidth, $origHeight, $imageType) = $imageInfo;

    // Učitaj originalnu sliku ovisno o tipu
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $image = @imagecreatefromjpeg($source);
            break;
        case IMAGETYPE_PNG:
            $image = @imagecreatefrompng($source);
            break;
        case IMAGETYPE_GIF:
            $image = @imagecreatefromgif($source);
            break;
        case IMAGETYPE_WEBP:
            $image = @imagecreatefromwebp($source);
            break;
        default:
            return false;
    }

    if (!$image) {
        return false;
    }

    // Ispravi orijentaciju na osnovu EXIF podataka (za fotografije s mobitela)
    if ($imageType == IMAGETYPE_JPEG && function_exists('exif_read_data')) {
        $exif = @exif_read_data($source);
        if ($exif && !empty($exif['Orientation'])) {
            switch ($exif['Orientation']) {
                case 3:
                    $image = imagerotate($image, 180, 0);
                    break;
                case 6:
                    $image = imagerotate($image, -90, 0);
                    // Zamijeni širinu i visinu nakon rotacije
                    $temp = $origWidth;
                    $origWidth = $origHeight;
                    $origHeight = $temp;
                    break;
                case 8:
                    $image = imagerotate($image, 90, 0);
                    // Zamijeni širinu i visinu nakon rotacije
                    $temp = $origWidth;
                    $origWidth = $origHeight;
                    $origHeight = $temp;
                    break;
            }
        }
    }

    // Izračunaj nove dimenzije uz održavanje omjera
    $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight);
    
    // Ako je slika manja od max dimenzija, ne mijenjaj je
    if ($ratio >= 1) {
        $newWidth = $origWidth;
        $newHeight = $origHeight;
    } else {
        $newWidth = (int)($origWidth * $ratio);
        $newHeight = (int)($origHeight * $ratio);
    }

    // Kreiraj novu sliku
    $newImage = imagecreatetruecolor($newWidth, $newHeight);

    // Očuvaj transparentnost za PNG i GIF
    if ($imageType == IMAGETYPE_PNG || $imageType == IMAGETYPE_GIF) {
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
        imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
    }

    // Kopiraj i resiziraj
    imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

    // Spremi sliku
    $result = false;
    $ext = strtolower(pathinfo($destination, PATHINFO_EXTENSION));
    
    switch ($ext) {
        case 'jpg':
        case 'jpeg':
            $result = imagejpeg($newImage, $destination, $quality);
            break;
        case 'png':
            // PNG quality je 0-9 (0 = bez kompresije, 9 = max kompresija)
            $pngQuality = (int)(9 - ($quality / 100) * 9);
            $result = imagepng($newImage, $destination, $pngQuality);
            break;
        case 'gif':
            $result = imagegif($newImage, $destination);
            break;
        case 'webp':
            $result = imagewebp($newImage, $destination, $quality);
            break;
    }

    // Oslobodi memoriju
    imagedestroy($image);
    imagedestroy($newImage);

    return $result;
}

// DEBUG - privremeno uključeno dok ne proradi upload
error_reporting(E_ALL);
ini_set('display_errors', '1');

$errors = [];
$success = null;
$debugFiles = '';
$debugInfo = [];

$galleryId = isset($_GET['id']) ? (int) $_GET['id'] : null;

// Default vrijednosti galerije
$gallery = [
    'id' => $galleryId,
    'name' => '',
    'slug' => '',
    'description' => '',
];

// Ako uređujemo postojeću galeriju, pokušaj je učitati
if ($galleryId) {
    $stmt = $pdo->prepare("SELECT * FROM galleries WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $galleryId]);
    $row = $stmt->fetch();

    if ($row) {
        $gallery = $row;
    } else {
        $errors[] = 'Tražena galerija ne postoji.';
        $galleryId = null;
    }
}

// Obrada forme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_gallery'])) {
    // Ako je POST došao, a $_FILES je prazan, moguće je da je prekoračen post_max_size
    if (empty($_FILES) && !empty($_SERVER['CONTENT_LENGTH'])) {
        $errors[] = 'Izgleda da je ukupna veličina uploada veća od dopuštenog limita na serveru (post_max_size / upload_max_filesize). 
        Pokušaj s manjim brojem / manjim slikama ili povećaj limite u PHP postavkama na hostingu.';
    }
    // Debug info
    $debugFiles = print_r($_FILES, true);
    $debugInfo['post'] = $_POST;

    // ID iz POST-a
    $galleryId = !empty($_POST['id']) ? (int) $_POST['id'] : null;
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($name === '') {
        $errors[] = 'Naziv galerije je obavezan.';
    }

    if ($slug === '' && $name !== '') {
        // slugify definirana u config.php
        $slug = slugify($name);
    }

    if ($slug === '') {
        $errors[] = 'Slug nije moguće generirati.';
    }

    if (!$errors) {
        // UPDATE ili INSERT galerije
        if ($galleryId) {
            $stmt = $pdo->prepare("
                UPDATE galleries
                SET name = :name,
                    slug = :slug,
                    description = :description
                WHERE id = :id
            ");
            $stmt->execute([
                ':name' => $name,
                ':slug' => $slug,
                ':description' => $description,
                ':id' => $galleryId,
            ]);
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO galleries (name, slug, description, created_at)
                VALUES (:name, :slug, :description, NOW())
            ");
            $stmt->execute([
                ':name' => $name,
                ':slug' => $slug,
                ':description' => $description,
            ]);
            $galleryId = (int) $pdo->lastInsertId();
        }

        $gallery['name'] = $name;
        $gallery['slug'] = $slug;
        $gallery['description'] = $description;

        // Upload slika
        $uploadedCount = 0;
        $fileErrors = [];

        // Putanja do upload foldera
        $uploadDir = __DIR__ . '/uploads/gallery/';
        $debugInfo['uploadDir'] = $uploadDir;

        // Kreiraj folder ako ne postoji
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
                $errors[] = 'Ne mogu kreirati direktorij za upload: ' . $uploadDir;
            }
        }

        $debugInfo['uploadDir_exists'] = is_dir($uploadDir);
        $debugInfo['uploadDir_writable'] = is_writable($uploadDir);

        if (!$errors && !empty($_FILES['images']['name'][0])) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            foreach ($_FILES['images']['name'] as $idx => $origName) {
                if ($origName === '') {
                    continue;
                }

                $errCode = $_FILES['images']['error'][$idx];

                if ($errCode !== UPLOAD_ERR_OK) {
                    // zabilježi grešku za ovaj fajl
                    $fileErrors[] = $origName . ' - greška pri uploadu (error code: ' . $errCode . ')';
                    continue;
                }

                $tmpName = $_FILES['images']['tmp_name'][$idx];
                $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

                if (!in_array($ext, $allowed, true)) {
                    $fileErrors[] = $origName . ' - zabranjena ekstenzija.';
                    continue;
                }

                $newName = uniqid('img_', true) . '.' . $ext;
                $fullPath = $uploadDir . $newName;
                
                // Resize sliku prije spremanja
                $resized = resizeImage($tmpName, $fullPath, 1920, 1080, 85);

                if ($resized) {
                    $stmt = $pdo->prepare("
                        INSERT INTO gallery_images (gallery_id, filename, created_at)
                        VALUES (:gallery_id, :filename, NOW())
                    ");
                    $stmt->execute([
                        ':gallery_id' => $galleryId,
                        ':filename' => $newName,
                    ]);

                    $uploadedCount++;
                } else {
                    $fileErrors[] = $origName . ' - resize/spremanje nije uspjelo.';
                }
            }
        }

        if (!$errors) {
            $success = 'Galerija je spremljena.';
            if ($uploadedCount > 0) {
                $success .= ' Uspješno uploadano slika: ' . $uploadedCount . '.';
            }
            if ($fileErrors) {
                $errors = array_merge($errors, $fileErrors);
            }
        }
    }

    // Vrati vrijednosti u formu ako ima grešaka
    $gallery['name'] = $name ?? $gallery['name'];
    $gallery['slug'] = $slug ?? $gallery['slug'];
    $gallery['description'] = $description ?? $gallery['description'];
}

// Handle image deletion
if (isset($_GET['delete_image'])) {
    $imageId = (int)$_GET['delete_image'];
    
    // Get image info to delete file
    $stmt = $pdo->prepare("SELECT filename FROM gallery_images WHERE id = :id");
    $stmt->execute([':id' => $imageId]);
    $imgData = $stmt->fetch();
    
    if ($imgData) {
        // Delete file
        $filePath = __DIR__ . '/uploads/gallery/' . $imgData['filename'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        // Delete from database
        $stmt = $pdo->prepare("DELETE FROM gallery_images WHERE id = :id");
        $stmt->execute([':id' => $imageId]);
        
        $success = 'Slika je obrisana.';
    }
}
?>
<!DOCTYPE html>
<html lang="hr">

<head>
    <meta charset="utf-8">
    <title>Galerija – admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Glavni stil (isti kao na javnom dijelu) -->
    <link rel="stylesheet" href="style.css">
</head>

<body class="bg-dark text-light">
    <div class="container py-4">
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">
                <?= $galleryId ? 'Uređivanje galerije' : 'Nova galerija' ?>
            </h1>
            <div class="d-flex gap-2">
                <a href="admin-galerije.php" class="btn btn-outline-light btn-sm">← Galerije</a>
                <a href="admin-novosti.php" class="btn btn-outline-light btn-sm">Članci</a>
                <a href="admin-logout.php" class="btn btn-outline-secondary btn-sm">Odjava</a>
            </div>
        </div>

        <?php if ($errors): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <details class="mb-4">
                <summary class="text-warning">Debug podaci (klikni za detalje)</summary>
                <div class="mt-2">
                    <h6 class="text-info">$_FILES:</h6>
                    <pre
                        class="small bg-black text-light p-3 rounded-3 mb-3"><?= htmlspecialchars($debugFiles, ENT_QUOTES, 'UTF-8') ?></pre>

                    <h6 class="text-info">Upload direktorij i ostalo:</h6>
                    <pre
                        class="small bg-black text-light p-3 rounded-3 mb-0"><?= htmlspecialchars(print_r($debugInfo, true), ENT_QUOTES, 'UTF-8') ?></pre>
                </div>
            </details>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" class="bg-body-tertiary p-4 rounded-3 text-light">
            <input type="hidden" name="id"
                value="<?= htmlspecialchars((string) ($galleryId ?? ''), ENT_QUOTES, 'UTF-8') ?>">

            <div class="mb-3">
                <label for="name" class="form-label">Naziv galerije</label>
                <input type="text" id="name" name="name" class="form-control"
                    value="<?= htmlspecialchars($gallery['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            </div>

            <div class="mb-3">
                <label for="slug" class="form-label">Slug (URL naziv)</label>
                <input type="text" id="slug" name="slug" class="form-control"
                    value="<?= htmlspecialchars($gallery['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="ostavi prazno za automatski slug">
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Opis (nije obavezno)</label>
                <textarea id="description" name="description" rows="3"
                    class="form-control"><?= htmlspecialchars($gallery['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="mb-3">
                <label for="images" class="form-label">Dodaj slike u galeriju</label>
                <input type="file" name="images[]" id="images" class="form-control" multiple>
                <div class="form-text text-secondary">
                    Podržani formati: JPG, PNG, GIF, WEBP.
                </div>
            </div>

            <button type="submit" name="save_gallery" class="btn btn-primary">
                Spremi galeriju
            </button>
        </form>
        <?php
        // Prikaz već postojećih slika u galeriji (ako postoji ID)
        if (!empty($galleryId)) {
            $stmtImg = $pdo->prepare("
        SELECT id, filename, title, alt_text
        FROM gallery_images
        WHERE gallery_id = :gid
        ORDER BY sort_order, id
    ");
            $stmtImg->execute([':gid' => $galleryId]);
            $imgs = $stmtImg->fetchAll(PDO::FETCH_ASSOC);

            if ($imgs):
                ?>
                <hr class="border-secondary my-4">

                <h2 class="h5 mb-3">Slike u ovoj galeriji</h2>
                <div class="row g-3">
                    <?php foreach ($imgs as $img):
                        $src = 'uploads/gallery/' . $img['filename'];
                        ?>
                        <div class="col-6 col-md-3">
                            <div class="border rounded-3 p-2 bg-black">
                                <img src="<?= htmlspecialchars($src, ENT_QUOTES, 'UTF-8') ?>" class="img-fluid rounded mb-2" alt="" style="width: 100%; height: 200px; object-fit: cover; object-position: center;">
                                <div class="small text-truncate text-secondary">
                                    <?= htmlspecialchars($img['filename'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                                <!-- Add delete button here -->
                                <a href="?id=<?= $galleryId ?>&delete_image=<?= $img['id'] ?>" 
                                   class="btn btn-danger btn-sm w-100 mt-2"
                                   onclick="return confirm('Obrisati ovu sliku?');">
                                    Obriši
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php
            endif;
        }
        ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>