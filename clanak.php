<?php
define('IN_APP', true);
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/seo-meta.php';

$slug = $_GET['slug'] ?? '';

if ($slug === '') {
    http_response_code(404);
    $article = null;
} else {
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE slug = :slug LIMIT 1");
    $stmt->execute([':slug' => $slug]);
    $article = $stmt->fetch();
    if (!$article) {
        http_response_code(404);
    }
}
?>

<!DOCTYPE html>
<html lang="hr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  
  <?php
  if ($article) {
      // Extract excerpt or content preview
      $description = !empty($article['excerpt']) 
          ? $article['excerpt'] 
          : strip_tags(mb_substr($article['content'], 0, 160));
      
      generate_seo_meta([
          'title' => $article['title'],
          'description' => $description,
          'keywords' => $article['tags'] ?? 'moto gymkhana, hrvatska',
          'image' => $article['image'] ?? null,
          'type' => 'article',
          'author' => 'Moto Gymkhana Croatia',
          'published_time' => date('c', strtotime($article['created_at'])),
          'section' => $article['category'] ?? 'Novosti'
      ]);
      generate_article_schema($article);
  } else {
      generate_seo_meta([
          'title' => 'Članak nije pronađen',
          'description' => 'Traženi članak nije pronađen na Moto Gymkhana Croatia.'
      ]);
  }
  ?>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Google Font-->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;800&display=swap" rel="stylesheet">

  <!-- Custom stilovi (isti kao na index.php) -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

  <!-- NAVBAR (isti kao u index.php) -->
  <header id="top">
    <?php include __DIR__ . '/includes/nav.php'; ?>
  </header>

  <main>
    <article class="py-5">
      <div class="container">
        <?php if (!$article): ?>
          <p class="text-danger">Traženi članak nije pronađen.</p>
          <a href="novosti.php" class="btn btn-outline-light">← Natrag na novosti</a>
        <?php else: ?>
          <div class="mb-3">
            <a href="novosti.php" class="btn btn-outline-light btn-sm">← Sve novosti</a>
          </div>

          <div class="d-flex flex-wrap justify-content-between align-items-baseline gap-3 mb-4">
            <div>
              <div class="mg-section-eyebrow">
                <?= htmlspecialchars($article['category'] ?: 'Novosti', ENT_QUOTES, 'UTF-8') ?>
              </div>
              <h1 class="mg-section-title">
                <?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?>
              </h1>
            </div>
            <p class="mg-section-subtitle mb-0">
              Objavljeno:
              <?= date('d.m.Y.', strtotime($article['created_at'])) ?>
            </p>
          </div>

          <div class="mg-article-body clearfix">
            <?php if (!empty($article['image'])): ?>
              <figure class="mb-3 float-md-start me-md-4">
                <img src="<?= htmlspecialchars($article['image'], ENT_QUOTES, 'UTF-8') ?>"
                     class="img-fluid rounded"
                     width="500rem"
                     alt="<?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?>">
              </figure>
            <?php endif; ?>
            
            <!-- OVDJE IDE PUNI SADRŽAJ S HTML TAGOVIMA -->
            <?= $article['content'] ?>
          </div>
          <?php
          // GALERIJA SLIKA ispod članka (ako je povezana)
          if (!empty($article['gallery_id'] ?? null)) {
              $stmtGallery = $pdo->prepare("
                  SELECT filename, title, alt_text
                  FROM gallery_images
                  WHERE gallery_id = :gid
                  ORDER BY sort_order, id
              ");
              $stmtGallery->execute([':gid' => $article['gallery_id']]);
              $images = $stmtGallery->fetchAll(PDO::FETCH_ASSOC);

              if ($images):
          ?>
            <section class="mt-5">
              <div class="d-flex flex-wrap justify-content-between align-items-baseline gap-3 mb-3">
                <div>
                  <div class="mg-section-eyebrow">Galerija</div>
                  <h2 class="mg-section-title fs-4">Fotografije s događaja</h2>
                </div>
              </div>

              <div class="row g-3">
                <?php foreach ($images as $index => $img):
                  $src   = 'uploads/gallery/' . $img['filename'];
                  $alt   = $img['alt_text'] ?: $article['title'];
                  $label = $img['title'] ?: '';
                ?>
                  <div class="col-6 col-md-3">
                    <a
                      href="#"
                      onclick="openGalleryModal(<?= $index ?>); return false;"
                      class="d-block"
                      style="cursor: pointer;"
                    >
                      <img
                        src="<?= htmlspecialchars($src, ENT_QUOTES, 'UTF-8') ?>"
                        alt="<?= htmlspecialchars($alt, ENT_QUOTES, 'UTF-8') ?>"
                        class="img-fluid rounded"
                        style="width: 100%; height: 200px; object-fit: cover; object-position: center; transition: transform 0.2s;"
                        onmouseover="this.style.transform='scale(1.05)'"
                        onmouseout="this.style.transform='scale(1)'"
                      >
                    </a>
                  </div>
                <?php endforeach; ?>
              </div>

              <!-- Modal za galeriju u članku -->
              <div id="articleGalleryModal" class="modal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.95); overflow: hidden;">
                <span onclick="closeGalleryModal()" style="position: absolute; top: 20px; right: 35px; color: #f1f1f1; font-size: 40px; font-weight: bold; cursor: pointer; z-index: 10000;">&times;</span>
                
                <div style="position: relative; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                  <img id="galleryModalImage" src="" style="max-width: 90%; max-height: 90vh; object-fit: contain;">
                  
                  <a onclick="changeGalleryImage(-1)" style="position: absolute; left: 20px; top: 50%; transform: translateY(-50%); color: white; font-size: 50px; font-weight: bold; cursor: pointer; user-select: none; padding: 10px 20px; background-color: rgba(0,0,0,0.5); border-radius: 5px; text-decoration: none;">&#10094;</a>
                  <a onclick="changeGalleryImage(1)" style="position: absolute; right: 20px; top: 50%; transform: translateY(-50%); color: white; font-size: 50px; font-weight: bold; cursor: pointer; user-select: none; padding: 10px 20px; background-color: rgba(0,0,0,0.5); border-radius: 5px; text-decoration: none;">&#10095;</a>
                </div>
                
                <div id="galleryImageCounter" style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); color: #f1f1f1; font-size: 18px;"></div>
              </div>

              <script>
                const galleryImages = <?= json_encode(array_map(function($img) {
                  return 'uploads/gallery/' . $img['filename'];
                }, $images)) ?>;
                
                let currentGalleryIndex = 0;

                function openGalleryModal(index) {
                  currentGalleryIndex = index;
                  document.getElementById('articleGalleryModal').style.display = 'block';
                  showGalleryImage();
                  document.body.style.overflow = 'hidden';
                }

                function closeGalleryModal() {
                  document.getElementById('articleGalleryModal').style.display = 'none';
                  document.body.style.overflow = 'auto';
                }

                function changeGalleryImage(direction) {
                  currentGalleryIndex += direction;
                  if (currentGalleryIndex >= galleryImages.length) {
                    currentGalleryIndex = 0;
                  } else if (currentGalleryIndex < 0) {
                    currentGalleryIndex = galleryImages.length - 1;
                  }
                  showGalleryImage();
                }

                function showGalleryImage() {
                  document.getElementById('galleryModalImage').src = galleryImages[currentGalleryIndex];
                  document.getElementById('galleryImageCounter').textContent = (currentGalleryIndex + 1) + ' / ' + galleryImages.length;
                }

                // Zatvori modal klikom na pozadinu
                document.getElementById('articleGalleryModal').addEventListener('click', function(e) {
                  if (e.target === this) {
                    closeGalleryModal();
                  }
                });

                // Keyboard navigation
                document.addEventListener('keydown', function(e) {
                  if (document.getElementById('articleGalleryModal').style.display === 'block') {
                    if (e.key === 'ArrowLeft') {
                      changeGalleryImage(-1);
                    } else if (e.key === 'ArrowRight') {
                      changeGalleryImage(1);
                    } else if (e.key === 'Escape') {
                      closeGalleryModal();
                    }
                  }
                });
              </script>
            </section>
          <?php
              endif;
          }
          ?>
        <?php endif; ?>
      </div>
    </article>
  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>

  <!-- Modal za prikaz slika iz galerije -->
  <div class="modal fade mg-modal-image" id="galleryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content position-relative">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zatvori"></button>
        <div class="modal-body">
          <img src="" alt="" id="galleryModalImage" class="img-fluid w-100">
        </div>
        <div class="modal-footer border-0 pt-0">
          <p class="mb-0 small text-secondary" id="galleryModalCaption"></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Back to top -->
  <a href="#top" class="btn btn-primary mg-back-to-top" aria-label="Povratak na vrh">
    ↑
  </a>


  <!-- Back to top -->
  <a href="#top" class="btn btn-primary mg-back-to-top" aria-label="Povratak na vrh">
    ↑
  </a>

  <!-- Bootstrap JS bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Gallery overlay (lightbox preko Bootstrap modala)
    document.addEventListener('click', function (event) {
      var trigger = event.target.closest('.mg-gallery-item[data-full]');
      if (!trigger) return;

      var fullSrc = trigger.getAttribute('data-full') || '';
      var caption = trigger.getAttribute('data-caption') || '';

      var imgEl = document.getElementById('galleryModalImage');
      var captionEl = document.getElementById('galleryModalCaption');

      if (imgEl) {
        imgEl.src = fullSrc;
        imgEl.alt = caption;
      }
      if (captionEl) {
        captionEl.textContent = caption;
      }
    });
  </script>
  
</body>

</html>
