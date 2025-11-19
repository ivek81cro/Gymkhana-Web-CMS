<?php
define('IN_APP', true);
require __DIR__ . '/config.php';

// Dohvati sve galerije s prvom slikom za thumbnail
$stmt = $pdo->query("
    SELECT 
        g.id, 
        g.name, 
        g.slug, 
        g.description, 
        g.created_at,
        (SELECT filename FROM gallery_images WHERE gallery_id = g.id ORDER BY sort_order, id LIMIT 1) as thumbnail
    FROM galleries g
    ORDER BY g.created_at DESC
");
$galleries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="hr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Galerije – Moto Gymkhana Croatia</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Google Font-->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;800&display=swap" rel="stylesheet">

  <!-- Custom stilovi -->
  <link rel="stylesheet" href="style.css">
</head>

<body>

  <!-- NAVBAR -->
  <header id="top">
    <?php include __DIR__ . '/nav.php'; ?>
  </header>

  <main>
    <section class="py-5">
      <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-baseline gap-3 mb-4">
          <div>
            <div class="mg-section-eyebrow">Fotografije</div>
            <h1 class="mg-section-title">Galerije</h1>
          </div>
        </div>

        <?php if (empty($galleries)): ?>
          <div class="alert alert-info">
            Trenutno nema dostupnih galerija.
          </div>
        <?php else: ?>
          <div class="row g-4">
            <?php foreach ($galleries as $gallery): ?>
              <div class="col-md-6 col-lg-4">
                <div class="card mg-card h-100">
                  <?php if ($gallery['thumbnail']): ?>
                    <a href="galerija.php?slug=<?= urlencode($gallery['slug']) ?>">
                      <img src="uploads/gallery/<?= htmlspecialchars($gallery['thumbnail'], ENT_QUOTES, 'UTF-8') ?>" 
                           class="card-img-top" 
                           alt="<?= htmlspecialchars($gallery['name'], ENT_QUOTES, 'UTF-8') ?>"
                           style="width: 100%; height: 250px; object-fit: cover; object-position: center;">
                    </a>
                  <?php else: ?>
                    <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" 
                         style="height: 250px;">
                      <span class="text-muted">Nema slika</span>
                    </div>
                  <?php endif; ?>
                  
                  <div class="card-body">
                    <h5 class="card-title">
                      <a href="galerija.php?slug=<?= urlencode($gallery['slug']) ?>" 
                         class="text-decoration-none text-light">
                        <?= htmlspecialchars($gallery['name'], ENT_QUOTES, 'UTF-8') ?>
                      </a>
                    </h5>
                    
                    <?php if (!empty($gallery['description'])): ?>
                      <p class="card-text text-secondary small">
                        <?= htmlspecialchars($gallery['description'], ENT_QUOTES, 'UTF-8') ?>
                      </p>
                    <?php endif; ?>
                    
                    <div class="d-flex justify-content-between align-items-center">
                      <small class="text-secondary">
                        <?= date('d.m.Y.', strtotime($gallery['created_at'])) ?>
                      </small>
                      <a href="galerija.php?slug=<?= urlencode($gallery['slug']) ?>" 
                         class="btn btn-sm btn-outline-light">
                        Pogledaj →
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </section>
  </main>

  <!-- FOOTER -->
  <footer class="py-4 mt-5 border-top border-secondary-subtle">
    <div class="container">
      <div class="row gy-3">
        <div class="col-md-6">
          <p class="small text-secondary mb-0">
            © <?= date('Y') ?> Moto Gymkhana Croatia
          </p>
        </div>
        <div class="col-md-6 text-md-end">
          <a href="index.php" class="text-decoration-none text-secondary small me-3">Početna</a>
          <a href="novosti.php" class="text-decoration-none text-secondary small me-3">Novosti</a>
          <a href="galerije.php" class="text-decoration-none text-secondary small">Galerije</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
