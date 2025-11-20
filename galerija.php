<?php
define('IN_APP', true);
require __DIR__ . '/includes/config.php';

$slug = $_GET['slug'] ?? '';

if ($slug === '') {
    http_response_code(404);
    $gallery = null;
} else {
    $stmt = $pdo->prepare("SELECT * FROM galleries WHERE slug = :slug LIMIT 1");
    $stmt->execute([':slug' => $slug]);
    $gallery = $stmt->fetch();
    
    if (!$gallery) {
        http_response_code(404);
    } else {
        // Dohvati sve slike galerije
        $stmtImages = $pdo->prepare("
            SELECT id, filename, title, alt_text
            FROM gallery_images
            WHERE gallery_id = :gid
            ORDER BY sort_order, id
        ");
        $stmtImages->execute([':gid' => $gallery['id']]);
        $images = $stmtImages->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="hr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= $gallery ? htmlspecialchars($gallery['name'], ENT_QUOTES, 'UTF-8') : 'Galerija' ?> – Moto Gymkhana Croatia</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Google Font-->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;800&display=swap" rel="stylesheet">

  <!-- Custom stilovi -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

  <!-- NAVBAR -->
  <header id="top">
    <?php include __DIR__ . '/includes/nav.php'; ?>
  </header>

  <main>
    <section class="py-5">
      <div class="container">
        <?php if (!$gallery): ?>
          <div class="alert alert-danger">
            Tražena galerija nije pronađena.
          </div>
          <a href="galerije.php" class="btn btn-outline-light">← Natrag na galerije</a>
        <?php else: ?>
          <div class="d-flex flex-wrap justify-content-between align-items-baseline gap-3 mb-4">
            <div>
              <div class="mg-section-eyebrow">Galerija</div>
              <h1 class="mg-section-title">
                <?= htmlspecialchars($gallery['name'], ENT_QUOTES, 'UTF-8') ?>
              </h1>
              <?php if (!empty($gallery['description'])): ?>
                <p class="mg-section-subtitle">
                  <?= htmlspecialchars($gallery['description'], ENT_QUOTES, 'UTF-8') ?>
                </p>
              <?php endif; ?>
            </div>
            <div>
              <a href="galerije.php" class="btn btn-outline-light btn-sm">← Sve galerije</a>
            </div>
          </div>

          <?php if (empty($images)): ?>
            <div class="alert alert-info">
              Ova galerija još nema slika.
            </div>
          <?php else: ?>
            <div class="row g-3">
              <?php foreach ($images as $index => $img): ?>
                <div class="col-6 col-md-4 col-lg-3">
                  <a href="#" 
                     onclick="openModal(<?= $index ?>); return false;"
                     class="d-block"
                     style="cursor: pointer;">
                    <img src="uploads/gallery/<?= htmlspecialchars($img['filename'], ENT_QUOTES, 'UTF-8') ?>" 
                         class="img-fluid rounded" 
                         alt="<?= htmlspecialchars($img['alt_text'] ?: $gallery['name'], ENT_QUOTES, 'UTF-8') ?>"
                         style="width: 100%; height: 200px; object-fit: cover; transition: transform 0.2s;"
                         onmouseover="this.style.transform='scale(1.05)'"
                         onmouseout="this.style.transform='scale(1)'">
                  </a>
                </div>
              <?php endforeach; ?>
            </div>

            <!-- Modal za prikaz slika -->
            <div id="imageModal" class="modal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.95); overflow: hidden;">
              <span onclick="closeModal()" style="position: absolute; top: 20px; right: 35px; color: #f1f1f1; font-size: 40px; font-weight: bold; cursor: pointer; z-index: 10000;">&times;</span>
              
              <div style="position: relative; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                <img id="modalImage" src="" style="max-width: 90%; max-height: 90vh; object-fit: contain;">
                
                <a onclick="changeImage(-1)" style="position: absolute; left: 20px; top: 50%; transform: translateY(-50%); color: white; font-size: 50px; font-weight: bold; cursor: pointer; user-select: none; padding: 10px 20px; background-color: rgba(0,0,0,0.5); border-radius: 5px; text-decoration: none;">&#10094;</a>
                <a onclick="changeImage(1)" style="position: absolute; right: 20px; top: 50%; transform: translateY(-50%); color: white; font-size: 50px; font-weight: bold; cursor: pointer; user-select: none; padding: 10px 20px; background-color: rgba(0,0,0,0.5); border-radius: 5px; text-decoration: none;">&#10095;</a>
              </div>
              
              <div id="imageCounter" style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); color: #f1f1f1; font-size: 18px;"></div>
            </div>

            <script>
              const images = <?= json_encode(array_map(function($img) {
                return 'uploads/gallery/' . $img['filename'];
              }, $images)) ?>;
              
              let currentIndex = 0;

              function openModal(index) {
                currentIndex = index;
                document.getElementById('imageModal').style.display = 'block';
                showImage();
                document.body.style.overflow = 'hidden';
              }

              function closeModal() {
                document.getElementById('imageModal').style.display = 'none';
                document.body.style.overflow = 'auto';
              }

              function changeImage(direction) {
                currentIndex += direction;
                if (currentIndex >= images.length) {
                  currentIndex = 0;
                } else if (currentIndex < 0) {
                  currentIndex = images.length - 1;
                }
                showImage();
              }

              function showImage() {
                document.getElementById('modalImage').src = images[currentIndex];
                document.getElementById('imageCounter').textContent = (currentIndex + 1) + ' / ' + images.length;
              }

              // Zatvori modal klikom na pozadinu
              document.getElementById('imageModal').addEventListener('click', function(e) {
                if (e.target === this) {
                  closeModal();
                }
              });

              // Keyboard navigation
              document.addEventListener('keydown', function(e) {
                if (document.getElementById('imageModal').style.display === 'block') {
                  if (e.key === 'ArrowLeft') {
                    changeImage(-1);
                  } else if (e.key === 'ArrowRight') {
                    changeImage(1);
                  } else if (e.key === 'Escape') {
                    closeModal();
                  }
                }
              });
            </script>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
