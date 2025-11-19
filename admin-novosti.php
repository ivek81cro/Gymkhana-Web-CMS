<?php
define('IN_APP', true);
require __DIR__ . '/config.php';

// Samo za prijavljene admine
require_admin();

// Dohvati sve galerije za dropdown
$galleries = $pdo->query("
    SELECT id, name
    FROM galleries
    ORDER BY created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

$errors = [];
$success = null;

// ID članka ako uređujemo (admin-novosti.php?id=123)
$articleId = isset($_GET['id']) ? (int) $_GET['id'] : null;
$editing = $articleId > 0;
$article = null;

// Ako uređujemo, učitaj članak iz baze
if ($editing) {
  $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = :id");
  $stmt->execute([':id' => $articleId]);
  $article = $stmt->fetch();

  if (!$article) {
    // Ako ne postoji, ponašaj se kao da radimo novi
    $errors[] = 'Članak s navedenim ID-em ne postoji. Kreiraj novi članak.';
    $editing = false;
    $articleId = null;
  }
}

// Obrada forme (novo + edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = trim($_POST['title'] ?? '');
  $slug = trim($_POST['slug'] ?? '');
  $excerpt = trim($_POST['excerpt'] ?? '');
  $image = trim($_POST['image'] ?? '');
  $category = trim($_POST['category'] ?? '');
  $tags = trim($_POST['tags'] ?? '');
  $content = trim($_POST['content'] ?? '');
  $date = trim($_POST['date'] ?? '');
  $galleryId = !empty($_POST['gallery_id']) ? (int)$_POST['gallery_id'] : null;

  if ($title === '') {
    $errors[] = 'Naslov je obavezan.';
  }

  if ($content === '') {
    $errors[] = 'Sadržaj članka je obavezan.';
  }

  // Ako nema slug, generiramo ga iz naslova
  if ($slug === '') {
    $slug = slugify($title);
  } else {
    $slug = slugify($slug);
  }

  // Ako ne zadaš datum:
  //  - kod novog članka → danas
  //  - kod uređivanja → stari datum, ako postoji
  if ($date === '') {
    if ($editing && $article && !empty($article['created_at'])) {
      $date = substr($article['created_at'], 0, 10);
    } else {
      $date = date('Y-m-d');
    }
  }

  $createdAt = $date . ' 00:00:00';

  if (!$errors) {
    try {
      if ($editing && $article) {
        // UPDATE postojećeg članka
        $stmt = $pdo->prepare("
                    UPDATE articles
                    SET slug = :slug,
                        title = :title,
                        excerpt = :excerpt,
                        content = :content,
                        image = :image,
                        category = :category,
                        tags = :tags,
                        gallery_id = :gallery_id,
                        created_at = :created_at
                    WHERE id = :id
                ");

        $stmt->execute([
          ':slug' => $slug,
          ':title' => $title,
          ':excerpt' => $excerpt,
          ':content' => $content,
          ':image' => $image,
          ':category' => $category,
          ':tags' => $tags,
          ':gallery_id' => $galleryId,
          ':created_at' => $createdAt,
          ':id' => $article['id'],
        ]);

        $success = 'Članak je uspješno ažuriran.';

        // Osvježi $article podacima koje smo upravo spremili
        $article['slug'] = $slug;
        $article['title'] = $title;
        $article['excerpt'] = $excerpt;
        $article['content'] = $content;
        $article['image'] = $image;
        $article['category'] = $category;
        $article['tags'] = $tags;
        $article['gallery_id'] = $galleryId;
        $article['created_at'] = $createdAt;
      } else {
        // INSERT novog članka
        $stmt = $pdo->prepare("
                    INSERT INTO articles (slug, title, excerpt, content, image, category, tags, gallery_id, created_at)
                    VALUES (:slug, :title, :excerpt, :content, :image, :category, :tags, :gallery_id, :created_at)
                ");

        $stmt->execute([
          ':slug' => $slug,
          ':title' => $title,
          ':excerpt' => $excerpt,
          ':content' => $content,
          ':image' => $image,
          ':category' => $category,
          ':tags' => $tags,
          ':gallery_id' => $galleryId,
          ':created_at' => $createdAt,
        ]);

        $success = 'Članak je uspješno spremljen.';
        // Očisti formu nakon spremanja novog članka
        $_POST = [];
      }
    } catch (PDOException $e) {
      $errors[] = 'Greška pri spremanju: ' . $e->getMessage();
    }
  }
}

// Vrijednosti za formu (prefill za novi/uređivanje)
$titleValue = $_POST['title'] ?? ($article['title'] ?? '');
$slugValue = $_POST['slug'] ?? ($article['slug'] ?? '');
$excerptValue = $_POST['excerpt'] ?? ($article['excerpt'] ?? '');
$imageValue = $_POST['image'] ?? ($article['image'] ?? '');
$categoryValue = $_POST['category'] ?? ($article['category'] ?? '');
$tagsValue = $_POST['tags'] ?? ($article['tags'] ?? '');
$contentValue = $_POST['content'] ?? ($article['content'] ?? '');
$galleryValue = $_POST['gallery_id'] ?? ($article['gallery_id'] ?? '');
$dateValue = $_POST['date']
  ?? (
    $article && !empty($article['created_at'])
    ? substr($article['created_at'], 0, 10)
    : date('Y-m-d')
  );

// Lista svih članaka za tablicu dolje
$stmtList = $pdo->query("
    SELECT id, slug, title, category, created_at
    FROM articles
    ORDER BY created_at DESC
");
$allArticles = $stmtList->fetchAll();
?>


<!DOCTYPE html>
<html lang="hr">

<head>
  <meta charset="utf-8">
  <title>Generator članaka – Moto Gymkhana Croatia</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 5 (kao na index.php) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Google Font (kao na index.php) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;800&display=swap" rel="stylesheet">

  <!-- Custom stilovi (isti kao front) -->
  <link rel="stylesheet" href="style.css">
</head>

<body>

  <!-- NAVBAR u MG stilu, ali “admin” verzija -->
  <header id="top">
    <?php include __DIR__ . '/nav.php'; ?>
  </header>

  <main class="py-5">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <div class="mg-section-eyebrow">Admin alat</div>
          <h1 class="mg-section-title">Unos članka</h1>
        </div>
        <a href="admin-galerije.php" class="btn btn-outline-light btn-sm">Galerije</a>
      </div>

      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <div class="mg-section-eyebrow">Admin alat</div>
          <h1 class="mg-section-title">
            <?= $editing ? 'Uređivanje članka' : 'Unos novog članka' ?>
          </h1>
        </div>
        <div class="d-flex gap-2">
          <a href="admin-novosti.php" class="btn btn-outline-light btn-sm">
            + Novi članak
          </a>
          <a href="admin-logout.php" class="btn btn-outline-secondary btn-sm">
            Odjava
          </a>
        </div>
      </div>

      <!-- Poruke -->
      <?php if (!empty($errors)): ?>
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

      <!-- Lista postojećih članaka -->
      <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h2 class="h5 mb-0">Postojeći članci</h2>
          <span class="text-secondary small">
            Klikni na <strong>Uredi</strong> za izmjenu članka.
          </span>
        </div>

        <?php if (empty($allArticles)): ?>
          <p class="text-secondary small mb-0">Još nema spremljenih članaka.</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-sm table-dark align-middle mb-0">
              <thead>
                <tr>
                  <th style="width: 60px;">ID</th>
                  <th>Naslov</th>
                  <th>Kategorija</th>
                  <th>Datum</th>
                  <th>Slug</th>
                  <th style="width: 140px;">Akcije</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($allArticles as $row): ?>
                  <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['category'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= date('d.m.Y.', strtotime($row['created_at'])) ?></td>
                    <td class="small"><?= htmlspecialchars($row['slug'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="d-flex gap-1">
                      <a href="admin-novosti.php?id=<?= (int) $row['id'] ?>" class="btn btn-sm btn-outline-light">
                        Uredi
                      </a>
                      <a href="clanak.php?slug=<?= urlencode($row['slug']) ?>" target="_blank"
                        class="btn btn-sm btn-outline-secondary">
                        Pogledaj
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>


      <div class="card mg-card">
        <div class="card-body">
          <form method="post">
            <div class="row g-3">
              <div class="col-md-8">
                <label for="title" class="text-secondary">Naslov</label>
                <input type="text" name="title" id="title" class="form-control"
                  value="<?= htmlspecialchars($titleValue, ENT_QUOTES, 'UTF-8') ?>" required>
              </div>

              <div class="col-md-4">
                <label for="date" class="text-secondary">Datum</label>
                <input type="date" name="date" id="date" class="form-control"
                  value="<?= htmlspecialchars($dateValue, ENT_QUOTES, 'UTF-8') ?>">
              </div>

              <div class="col-md-6">
                <label for="slug" class="text-secondary">Slug (URL dio – opcionalno)</label>
                <input type="text" name="slug" id="slug" class="form-control" placeholder="npr. europsko-prvenstvo-2025"
                  value="<?= htmlspecialchars($slugValue, ENT_QUOTES, 'UTF-8') ?>">
                <div class="form-text">Ako ostaviš prazno, generirat će se iz naslova.</div>
              </div>

              <div class="col-md-6">
                <label for="image" class="text-secondary">Slika (putanja)</label>
                <input type="text" name="image" id="image" class="form-control"
                  placeholder="assets/img/news/europsko_press.jpg"
                  value="<?= htmlspecialchars($imageValue, ENT_QUOTES, 'UTF-8') ?>">
              </div>

              <div class="col-md-4">
                <label for="category" class="text-secondary">Kategorija</label>
                <input type="text" name="category" id="category" class="form-control" placeholder="Novosti"
                  value="<?= htmlspecialchars($categoryValue, ENT_QUOTES, 'UTF-8') ?>">
              </div>

              <div class="col-md-8">
                <label for="tags" class="text-secondary">Tagovi</label>
                <input type="text" name="tags" id="tags" class="form-control"
                  placeholder="europsko prvenstvo, natjecanja"
                  value="<?= htmlspecialchars($tagsValue, ENT_QUOTES, 'UTF-8') ?>">
              </div>

              <div class="col-md-12">
                <label for="gallery_id" class="text-secondary">Galerija (opcionalno)</label>
                <select name="gallery_id" id="gallery_id" class="form-select">
                  <option value="">-- Bez galerije --</option>
                  <?php foreach ($galleries as $gal): ?>
                    <option value="<?= (int)$gal['id'] ?>" 
                      <?= ($galleryValue == $gal['id']) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($gal['name'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <div class="form-text">Odaberi galeriju koja će se prikazati ispod članka.</div>
              </div>

              <div class="col-12">
                <label for="excerpt" class="text-secondary">Sažetak (kratki opis)</label>
                <textarea name="excerpt" id="excerpt" rows="3" class="form-control"
                  placeholder="Kratki uvod koji se prikazuje na popisu novosti."><?= htmlspecialchars($excerptValue, ENT_QUOTES, 'UTF-8') ?></textarea>
              </div>

              <div class="col-12">
                <label for="content" class="text-secondary">Sadržaj članka</label>
                <textarea name="content" id="content" rows="10" class="form-control"
                  placeholder="Puni tekst članka, može sadržavati HTML tagove."><?= htmlspecialchars($contentValue, ENT_QUOTES, 'UTF-8') ?></textarea>
                <div class="text-secondary">
                  HTML tagovi (npr. &lt;p&gt;, &lt;h2&gt;, &lt;strong&gt;) koriste se za formatiranje.
                  U prikazu se tagovi ne vide, samo formatirani tekst.
                </div>
              </div>

              <div class="col-12">
                <button type="submit" class="btn btn-primary">Spremi članak</button>
              </div>
            </div>
          </form>
        </div>
      </div>

    </div>
  </main>

  <!-- (Opcionalno) mali footer da znaš da si na admin stranici -->
  <footer class="py-3 mt-4 border-top border-secondary-subtle">
    <div class="container small text-secondary d-flex justify-content-between flex-wrap gap-2">
      <span>Admin alat za novosti – Moto Gymkhana Croatia</span>
      <a href="index.php" class="text-decoration-none">← Povratak na web</a>
    </div>
  </footer>

  <!-- Bootstrap JS bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Tvoj JS za generator -->
  <script src="assets/js/admin-novosti.js"></script>
</body>

</html>