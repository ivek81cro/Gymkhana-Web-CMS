<?php
define('IN_APP', true);
require __DIR__ . '/includes/config.php';

$stmt = $pdo->query("
    SELECT id, slug, title, excerpt, image, category, created_at
    FROM articles
    ORDER BY created_at DESC
");
$articles = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="hr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Novosti – Moto Gymkhana Croatia</title>

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
              Pregled svih vijesti iz Moto Gymkhana Croatia – edukacije, natjecanja i najave događaja.
            </p>
        
            <?php if (is_admin()): ?>
              <a href="admin/novosti.php" class="btn mg-btn">
                Admin zona
              </a>
            <?php else: ?>
              <a href="admin/login.php" class="btn mg-btn-outline">
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

  <!-- FOOTER (isti kao u index.php) -->
  <footer class="site-footer" id="kontakt">
    <div class="container py-4">
      <div class="row gy-4">
        <div class="col-lg-4">
          <div class="mg-footer-brand">
            <div class="mg-footer-brand-title">Udruga “Moto Gymkhana Croatia”</div>
            <p>
              Pridruži se treningu, edukaciji ili natjecanju i upoznaj koliko je vožnja sigurna i zabavna kad imaš
              kontrolu – i ekipu iza sebe.
            </p>
          </div>
        </div>
        <div class="col-6 col-lg-2">
          <div class="mg-footer-heading">Edukacija</div>
          <div class="mg-footer-list">
            <a href="index.php#edukacije">Škola sigurne vožnje</a>
            <a href="index.php#edukacije">Edukacijski poligoni</a>
            <a href="index.php#edukacije">Moto Gymkhana trening</a>
          </div>
        </div>
        <div class="col-6 col-lg-2">
          <div class="mg-footer-heading">Linkovi</div>
          <div class="mg-footer-list">
            <a href="index.php#natjecanja">Natjecanja</a>
            <a href="index.php#natjecanja">Postani član</a>
            <a href="index.php#natjecanja">Sponzori</a>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="mg-footer-heading">Kontakt</div>
          <div class="mg-footer-list">
            <span>Zagorska ul. 48, 10000 Zagreb</span>
            <a href="tel:+385992360091">+385 (0)99 236 0091</a>
            <a href="mailto:info@motogymkhana.hr">info@motogymkhana.hr</a>
            <a href="/privacy-policy">MotoGymkhana Croatia Privacy Policy</a>
          </div>
        </div>
      </div>

      <div
        class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-4 pt-3 border-top border-secondary-subtle">
        <span class="small text-secondary">
          © <span id="year"></span> Moto Gymkhana Croatia. Sva prava pridržana.
        </span>
        <div class="d-flex flex-wrap gap-2">
          <a href="https://www.facebook.com/motogymkhanacroatia" target="_blank" rel="noreferrer"
            class="mg-footer-badge">Facebook</a>
          <a href="https://www.instagram.com/moto_gymkhana_croatia" target="_blank" rel="noreferrer"
            class="mg-footer-badge">Instagram</a>
          <a href="https://www.youtube.com/@motogymkhanacroatia" target="_blank" rel="noreferrer"
            class="mg-footer-badge">YouTube</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- Back to top -->
  <a href="#top" class="btn btn-primary mg-back-to-top" aria-label="Povratak na vrh">
    ↑
  </a>

  <!-- Bootstrap JS bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    document.getElementById('year').textContent = new Date().getFullYear();
  </script>

</body>

</html>
