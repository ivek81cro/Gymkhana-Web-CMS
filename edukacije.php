<?php
define('IN_APP', true);
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/seo-meta.php';

// Dohvati sve članke iz kategorije "Edukacija"
$stmt = $pdo->prepare("
    SELECT slug, title, excerpt, content, image, created_at
    FROM articles
    WHERE category = :category AND status = 'published'
    ORDER BY created_at DESC
");
$stmt->execute([':category' => 'Edukacija']);
$articles = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="hr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <?php
  generate_seo_meta([
      'title' => 'Edukacije - Škola sigurne vožnje motocikla',
      'description' => 'Programi edukacije za početnike, povratnike i iskusne vozače. Učimo sigurnosnu vožnju, preciznu kontrolu i pripremu za natjecanja.',
      'keywords' => 'edukacija, škola vožnje, motocikl, trening, poligon, gymkhana trening',
      'type' => 'website'
  ]);
  ?>
  
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Google Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;800&display=swap" rel="stylesheet">
  
  <!-- Custom stilovi -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
  <?php include __DIR__ . '/includes/nav.php'; ?>

  <!-- HERO -->
  <section class="py-5 mg-section-bg">
    <div class="container">
      <div class="text-center mb-5">
        <div class="mg-section-eyebrow">Edukacije</div>
        <h1 class="mg-section-title">Škola. Poligon. Trening.</h1>
        <p class="mg-section-subtitle mx-auto" style="max-width: 700px;">
          Programi za one koji tek ulaze u svijet motocikala, vozače povratnike i iskusne koji žele finu kontrolu i pripremu za natjecanja.
        </p>
      </div>
    </div>
  </section>

  <!-- ČLANICI -->
  <section class="py-5">
    <div class="container">
      <?php if (empty($articles)): ?>
        <div class="text-center py-5 text-secondary">
          <p>Trenutno nema objavljenih edukacija.</p>
          <a href="index.php#edukacije" class="btn mg-btn mt-3">Natrag na početnu</a>
        </div>
      <?php else: ?>
        <div class="row g-4">
          <?php foreach ($articles as $article): ?>
            <div class="col-md-6 col-lg-4">
              <article class="card mg-card h-100">
                <?php if (!empty($article['image'])): ?>
                  <img src="<?= htmlspecialchars($article['image'], ENT_QUOTES, 'UTF-8') ?>" 
                       class="card-img-top" 
                       alt="<?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?>"
                       style="height: 200px; object-fit: cover;">
                <?php endif; ?>
                
                <div class="card-body">
                  <span class="mg-pill-label">
                    <?= date('d.m.Y', strtotime($article['created_at'])) ?>
                  </span>
                  
                  <h3 class="mg-edu-title text-secondary">
                    <a href="clanak.php?slug=<?= urlencode($article['slug']) ?>" class="text-decoration-none text-secondary">
                      <?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                  </h3>
                  
                  <?php
                    $summarySource = $article['excerpt'] ?: $article['content'] ?? '';
                    $articleUrl = 'clanak.php?slug=' . urlencode($article['slug']);
                  ?>
                  <p class="card-text text-secondary small mb-0">
                    <?= shorten_with_more_link($summarySource, 150, $articleUrl) ?>
                  </p>
                </div>
                
                <div class="card-footer bg-transparent border-0 pb-3">
                  <a href="clanak.php?slug=<?= urlencode($article['slug']) ?>" class="btn btn-sm mg-btn-outline">
                    Pročitaj više
                  </a>
                </div>
              </article>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <?php include __DIR__ . '/includes/footer.php'; ?>

  <!-- Bootstrap Bundle JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
