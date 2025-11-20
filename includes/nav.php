<?php
// trenutna skripta (index.php, novosti.php, clanak.php, admin/novosti.php...)
$current = basename($_SERVER['SCRIPT_NAME']);
// Detektiraj jesi li u admin folderu
$inAdmin = strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false || strpos($_SERVER['SCRIPT_NAME'], '\\admin\\') !== false;
$baseUrl = $inAdmin ? '../' : '';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mg-navbar">
      <div class="container">

        <!-- GORNJI RED: logo + naziv + telefon (desktop) + hamburger -->
        <div class="d-flex align-items-center justify-content-between flex-nowrap w-100">
          <!-- Logo + naziv u istom flex kontejneru -->
          <div class="d-flex align-items-center flex-nowrap mg-brand-wrap">
            <span class="mg-nav-logo d-flex align-items-center justify-content-center flex-shrink-0">
              <img src="<?= $baseUrl ?>assets/img/logo-mgc.png" alt="Moto Gymkhana Croatia logo">
            </span>

            <!-- Natpis -->
            <a class="navbar-brand mg-navbar-brand mb-0" href="<?= $baseUrl ?>index.php">
              Moto Gymkhana Croatia
              <small class="d-block text-white-50 mg-navbar-tagline">Škola sigurne vožnje</small>
            </a>
          </div>

          <!-- Hamburger uvijek desno u istom redu -->
          <button class="navbar-toggler flex-shrink-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
            aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
        </div>

        <!-- DONJI RED: meni (collapse) -->
        <div class="collapse navbar-collapse mt-2 mt-lg-0" id="mainNav">
          <ul class="navbar-nav ms-lg-auto">
            <li class="nav-item">
                <a class="nav-link <?= $current === 'index.php' ? 'active' : '' ?>"
                    href="<?= $baseUrl ?>index.php">O&nbsp;nama
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current === 'novosti.php' ? 'active' : '' ?>"
                     href="<?= $baseUrl ?>novosti.php">Novosti
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current === 'edukacije.php' ? 'active' : '' ?>"
                     href="<?= $baseUrl ?>edukacije.php">Edukacije
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($current === 'galerije.php' || $current === 'galerija.php') ? 'active' : '' ?>"
                     href="<?= $baseUrl ?>galerije.php">Galerije
                </a>
            </li>
            <?php if ($inAdmin && is_admin()): ?>
            <li class="nav-item">
                <a class="nav-link <?= $current === 'logs.php' ? 'active' : '' ?>"
                     href="<?= $baseUrl ?>admin/logs.php">Logovi
                </a>
            </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link <?= $current === 'index.php#natjecanja' ? 'active' : '' ?>"
                     href="<?= $baseUrl ?>index.php#natjecanja">Natjecanja
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current === 'index.php#suradnja' ? 'active' : '' ?>"
                     href="<?= $baseUrl ?>index.php#suradnja">Suradnja
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current === 'index.php#kontakt' ? 'active' : '' ?>"
                     href="<?= $baseUrl ?>index.php#kontakt">Kontakt
                </a>
            </li>            
          </ul>
        </div>

      </div>
    </nav>