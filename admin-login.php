<?php
define('IN_APP', true);
require __DIR__ . '/config.php';

// Ako je već logiran, šaljemo ga na admin-novosti
if (is_admin()) {
    header('Location: admin-novosti.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        $_SESSION['is_admin'] = true;
        header('Location: admin-novosti.php');
        exit;
    } else {
        $error = 'Pogrešno korisničko ime ili lozinka.';
    }
}
?>
<!DOCTYPE html>
<html lang="hr">
<head>
  <meta charset="utf-8">
  <title>Admin prijava – Moto Gymkhana Croatia</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Uveži iste CSS/Bootstrap kao i na ostalim stranicama -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">

  <main class="d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="container" style="max-width: 420px;">
      <div class="card mg-card">
        <div class="card-body">
          <h1 class="h4 mb-3">Admin prijava</h1>
          <p class="text-secondary small mb-4">
            Prijava za uređivanje i objavu novosti.
          </p>

          <?php if ($error): ?>
            <div class="alert alert-danger py-2">
              <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
          <?php endif; ?>

          <form method="post">
            <div class="mb-3">
              <label for="username" class="form-label">Korisničko ime</label>
              <input type="text" name="username" id="username" class="form-control" required autofocus>
            </div>

            <div class="mb-3">
              <label for="password" class="form-label">Lozinka</label>
              <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">
              Prijava
            </button>
          </form>
        </div>
      </div>
    </div>
  </main>

  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
