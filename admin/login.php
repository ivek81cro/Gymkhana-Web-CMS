<?php
define('IN_APP', true);
require __DIR__ . '/../includes/config.php';

// Ako je već logiran, šaljemo ga na admin-novosti
if (is_admin()) {
    header('Location: admin-novosti.php');
    exit;
}

$error = null;

// Rate limiting - max 5 pokušaja u 15 minuta
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = [];
}

// Očisti stare pokušaje (starije od 15 min)
$_SESSION['login_attempts'] = array_filter(
    $_SESSION['login_attempts'],
    fn($time) => $time > time() - 900
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Provjeri broj pokušaja
    if (count($_SESSION['login_attempts']) >= 5) {
        $error = 'Previše neuspješnih pokušaja. Pokušajte ponovno za 15 minuta.';
    } else {
        // CSRF zaštita
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($csrfToken)) {
            $error = 'Nevažeći sigurnosni token. Osvježite stranicu.';
        } else {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            // Password verify sa hash
            if ($username === ADMIN_USERNAME && password_verify($password, ADMIN_PASSWORD_HASH)) {
                // Regeneriraj session ID protiv session fixation
                regenerate_session();
                
                $_SESSION['is_admin'] = true;
                $_SESSION['admin_verified'] = true;
                $_SESSION['admin_username'] = $username;
                $_SESSION['login_attempts'] = []; // Reset pokušaja
                
                // Regeneriraj CSRF token
                unset($_SESSION['csrf_token']);
                
                // Log successful login
                log_activity('login', "User: {$username}", 'success');
                
                header('Location: novosti.php');
                exit;
            } else {
                // Zabilježi neuspješan pokušaj
                $_SESSION['login_attempts'][] = time();
                $error = 'Pogrešno korisničko ime ili lozinka.';
                
                // Log failed login attempt
                $attempts = count($_SESSION['login_attempts']);
                log_security('failed_login', "Username: {$username}, Attempts: {$attempts}", 'medium');
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="hr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin prijava – Moto Gymkhana Croatia</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Google Font-->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;800&display=swap" rel="stylesheet">

  <!-- Custom stilovi -->
  <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

  <!-- NAVBAR -->
  <header id="top">
    <?php include __DIR__ . '/../includes/nav.php'; ?>
  </header>

  <main>
    <section class="py-5">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-6 col-lg-5">
            <div class="mb-4 text-center">
              <div class="mg-section-eyebrow">Admin panel</div>
              <h1 class="mg-section-title">Prijava</h1>
              <p class="mg-section-subtitle">
                Prijava za uređivanje sadržaja i galerija
              </p>
            </div>

            <?php if ($error): ?>
              <div class="alert alert-danger">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
              </div>
            <?php endif; ?>

            <div class="card mg-card">
              <div class="card-body p-4">
                <form method="post">
                  <!-- CSRF Token -->
                  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generate_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                  
                  <div class="mb-3">
                    <label for="username" class="form-label text-secondary">Korisničko ime</label>
                    <input type="text" name="username" id="username" class="form-control" required autofocus>
                  </div>

                  <div class="mb-4">
                    <label for="password" class="form-label text-secondary">Lozinka</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                  </div>

                  <button type="submit" class="btn mg-btn w-100">
                    Prijava
                  </button>
                </form>
              </div>
            </div>

            <div class="text-center mt-4">
              <a href="../index.php" class="text-decoration-none text-secondary">
                ← Natrag na početnu
              </a>
            </div>
          </div>
        </div>
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
          <a href="../index.php" class="text-decoration-none text-secondary small me-3">Početna</a>
          <a href="../novosti.php" class="text-decoration-none text-secondary small me-3">Novosti</a>
          <a href="../galerije.php" class="text-decoration-none text-secondary small">Galerije</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
