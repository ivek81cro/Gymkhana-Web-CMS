<?php
// Detektiraj jesi li u admin ili micro folderu (kao u nav.php)
$inAdmin = strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false || strpos($_SERVER['SCRIPT_NAME'], '\\admin\\') !== false;
$inMicro = strpos($_SERVER['SCRIPT_NAME'], '/micro/') !== false || strpos($_SERVER['SCRIPT_NAME'], '\\micro\\') !== false;
$baseUrl = ($inAdmin || $inMicro) ? '../' : '';
?>

<!-- FOOTER / KONTAKT -->
<footer class="site-footer" id="kontakt">
  <div class="container py-4">
    <div class="row gy-4">
      <div class="col-lg-4">
        <div class="mg-footer-brand">
          <div class="mg-footer-brand-title">Udruga "Moto Gymkhana Croatia"</div>
          <p>
            Pridruži se treningu, edukaciji ili natjecanju i upoznaj koliko je vožnja sigurna i zabavna kad imaš
            kontrolu – i ekipu iza sebe.
          </p>
        </div>
      </div>
      <div class="col-6 col-lg-2">
        <div class="mg-footer-heading">Edukacija</div>
        <div class="mg-footer-list">
          <a href="<?= $baseUrl ?>edukacije.php">Sve edukacije</a>
          <a href="<?= $baseUrl ?>index.php#edukacije">Škola sigurne vožnje</a>
          <a href="<?= $baseUrl ?>index.php#edukacije">Edukacijski poligoni</a>
          <a href="<?= $baseUrl ?>index.php#edukacije">Moto Gymkhana trening</a>
        </div>
      </div>
      <div class="col-6 col-lg-2">
        <div class="mg-footer-heading">Linkovi</div>
        <div class="mg-footer-list">
          <a href="<?= $baseUrl ?>index.php#natjecanja">Natjecanja</a>
          <a href="<?= $baseUrl ?>index.php#natjecanja">Postani član</a>
          <a href="<?= $baseUrl ?>index.php#natjecanja">Sponzori</a>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="mg-footer-heading">Kontakt</div>
        <div class="mg-footer-list">
          <span>Zagorska ul. 48, 10000 Zagreb</span>
          <a href="tel:+385992360091">+385 (0)99 236 0091</a>
          <a href="mailto:info@motogymkhana.hr">info@motogymkhana.hr</a>
          <a href="<?= $baseUrl ?>privacy-policy.php">MotoGymkhana Croatia Privacy Policy</a>
        </div>
      </div>
    </div>

    <div
      class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-4 pt-3 border-top border-secondary-subtle">
      <span class="small text-secondary">
        © <span class="footer-year"></span> Moto Gymkhana Croatia. Sva prava pridržana.
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

<script>
  // Set current year in footer
  document.querySelectorAll('.footer-year').forEach(el => {
    el.textContent = new Date().getFullYear();
  });
</script>
