<?php
define('IN_APP', true);
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/seo-meta.php';

$stmt = $pdo->prepare("
    SELECT id, slug, title, excerpt, image, category, created_at
    FROM articles
    WHERE category = :category AND status = 'published'
    ORDER BY created_at DESC
");
$stmt->execute([':category' => 'Novosti']);
$articles = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="hr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  
  <?php
  generate_seo_meta([
      'title' => 'Novosti',
      'description' => 'Sve najnovije vijesti, najave događaja i rezultati natjecanja iz svijeta moto gymkhane u Hrvatskoj.',
      'keywords' => 'novosti, moto gymkhana, natjecanja, događaji, hrvatska',
      'type' => 'website'
  ]);
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
    <section class="py-5 mg-section-bg">
      <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-baseline gap-3 mb-4">
          <div>
            <div class="mg-section-eyebrow">Novosti</div>
            <h1 class="mg-section-title">Sve. Novosti.</h1>
          </div>
        
          <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-3 ms-auto">
            <p class="mg-section-subtitle mb-0">
              Pregled svih vijesti iz Moto Gymkhana Croatia, edukacije, natjecanja i najave događaja.
            </p>
        
            <?php if (is_admin()): ?>
              <a href="admin/novosti.php" class="mg-footer-badge">
                Admin zona
              </a>
            <?php else: ?>
              <a href="admin/login.php" class="mg-footer-badge">
                Admin prijava
              </a>
            <?php endif; ?>
          </div>
        </div>

        <div id="news-list-all" class="row g-4">
          <?php if (empty($articles)): ?>
            <p class="text-secondary">Nema dostupnih vijesti.</p>
          <?php else: ?>
            <?php foreach ($articles as $article): ?>
              <div class="col-md-6 col-lg-4">
                <article class="card h-100 mg-card mg-news-card">
                  <?php if (!empty($article['image'])): ?>
                    <img src="<?= htmlspecialchars($article['image'], ENT_QUOTES, 'UTF-8') ?>"
                         class="card-img-top"
                         alt="<?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?>">
                  <?php endif; ?>
                  
                  <div class="card-body d-flex flex-column">
                    <p class="mg-news-date mb-1">
                      <?= date('d.m.Y.', strtotime($article['created_at'])) ?>
                    </p>
                  
                    <h2 class="h5 card-title text-secondary">
                      <a href="clanak.php?slug=<?= urlencode($article['slug']) ?>"
                         class="stretched-link text-decoration-none">
                        <?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?>
                      </a>
                    </h2>
                  
                    <?php
                      $summarySource = $article['excerpt'] ?: $article['content'];
                      $articleUrl    = 'clanak.php?slug=' . urlencode($article['slug']);
                    ?>
                    <p class="card-text text-secondary mb-0">
                      <?= shorten_with_more_link($summarySource, 400, $articleUrl) ?>
                    </p>
                  </div>
                </article>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

      </div>
    </section>
  </main>


  <!-- FOOTER -->
  <?php include __DIR__ . '/includes/footer.php'; ?>

  <!-- Back to top -->
  <a href="#top" class="btn btn-primary mg-back-to-top" aria-label="Povratak na vrh">
    ↑
  </a>

  <!-- Bootstrap JS bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
