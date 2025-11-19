<?php
define('IN_APP', true);
require __DIR__ . '/config.php';

// Zadnja 3 članka za mini-listu na početnoj
$stmtMini = $pdo->query("
    SELECT slug, title, excerpt, content, created_at
    FROM articles
    ORDER BY created_at DESC
    LIMIT 3
");
$latestArticles = $stmtMini->fetchAll();
?>
<!DOCTYPE html>
<html lang="hr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Moto Gymkhana Croatia</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Google Font-->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;800&display=swap" rel="stylesheet">

  <!-- Custom stilovi -->
  <link rel="stylesheet" href="style.css">

  <!-- Custom JS -->
   <script src="assets/js/news-home.js"></script>
</head>

<body>

  <!-- NAVBAR -->
  <header id="top">
    <?php include __DIR__ . '/nav.php'; ?>  
  </header>

  <main>

    <!-- HERO -->
    <section class="hero py-5">
      <div class="mg-hero-glow"></div>
      <div class="container py-5">
        <div class="row align-items-center gy-4">
          <div class="mg-hero-card d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
            <!-- Lijevo: banner -->
            <img src="assets/img/baner_mgc.png" alt="gymkhana croatia baner" width="250" class="img-fluid">
            <!-- Desno: gumb + broj -->
            <div class="d-flex flex-column align-items-md-end align-items-start text-md-end text-start">
              <a href="#kontakt" class="btn mg-btn mb-2">
                Kontaktirajte nas
              </a>
              <span class="mg-nav-phone small text-uppercase">
                +385 (0)99 236 0091
              </span>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="mg-eyebrow mb-2">
              <span class="mg-eyebrow-dot"></span>
              Sigurnost. Vještina. Zabava.
            </div>
            <div class="mg-hero-kicker">Škola. Sigurne. Vožnje.</div>
            <h1 class="mg-hero-title">
              Moto Gymkhana <span class="accent">Croatia</span>
            </h1>
            <p class="mg-hero-subtitle">
              Udruga koja spaja edukaciju sigurne vožnje i sport kroz dinamične poligone s čunjevima. Sve u
              kontroliranim uvjetima, uz instruktore i ekipu koja živi motocikle.
            </p>
            <div class="d-flex flex-wrap gap-2 mb-3">
              <a href="#edukacije" class="btn mg-btn">Prijavi se na edukaciju</a>
              <a href="#natjecanja" class="btn mg-btn-outline">Pogledaj natjecanja</a>
            </div>
            <div class="d-flex flex-wrap gap-3 mg-hero-meta">
              <span><strong>120+ članova</strong> u Hrvatskoj</span>
              <span><strong>2018.</strong> – početak priče</span>
              <span><strong>Europa</strong> – dio svjetske mreže Moto Gymkhane</span>
            </div>
            <div class="d-flex flex-wrap gap-2 mt-3">
              <span class="mg-hero-badge">Sigurna vožnja na prvom mjestu</span>
              <span class="mg-hero-badge">Trening. Edukacija. Natjecanja.</span>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="mg-hero-card">
              <div class="mg-hero-card-main">
                <img src="assets/img/vozac-na-poligonu.jpg" alt="Vozač motocikla na Moto Gymkhana poligonu">
                <div class="mg-hero-card-label">
                  <span>Poligon · Trening</span>
                  <strong>MOTO GYMKHANA</strong>
                </div>
                <!--div class="mg-hero-pill">
                  <span class="mg-hero-pill-dot"></span>
                  Live na stazi · Zagreb
                </div-->
              </div>
              <div class="d-flex flex-wrap gap-2 mt-3 mg-hero-stats">
                <div class="mg-hero-stat-pill">
                  <strong>Škola sigurne vožnje</strong>
                  <span>za nove i povratnike</span>
                </div>
                <div class="mg-hero-stat-pill">
                  <strong>Edukacijski poligon</strong>
                  <span>za finu kontrolu motocikla</span>
                </div>
                <div class="mg-hero-stat-pill">
                  <strong>Natjecanja</strong>
                  <span>po pravilima Moto Gymkhane</span>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- O NAMA -->
    <section id="o-nama" class="py-5 mg-section-bg">
      <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-baseline gap-3 mb-4">
          <div>
            <div class="mg-section-eyebrow">O nama</div>
            <h2 class="mg-section-title">Tko. Smo. Mi.</h2>
          </div>
          <p class="mg-section-subtitle">
            Udruga građana koja je Moto Gymkhanu dovela u Hrvatsku i spojila edukaciju, sport i zajednicu vozača.
          </p>
        </div>

        <div class="row gy-4">
          <div class="col-md-6">
            <article class="mg-about-block h-100">
              <h3>Moto Gymkhana</h3>
              <p>
                Moto Gymkhana je sport u kojem vještina upravljanja motociklom dolazi na prvo mjesto. Vozi se na
                tehničkim poligonima označenim čunjevima, pri relativno malim brzinama ali uz veliku preciznost.
              </p>
              <p>
                Upravo zato naglasak je na sigurnosti – vozač uči granice motocikla i svoje reakcije, bez rizika ceste
                ili velikih brzina.
              </p>
              <div class="mg-about-meta">
                <span><strong>Podrijetlo:</strong> Japan, širenje po Europi od 2011.</span>
                <span><strong>U Hrvatskoj:</strong> organizirano djelovanje od 2018.</span>
              </div>
            </article>
          </div>
          <div class="col-md-6">
            <article class="mg-about-block h-100">
              <h3>Moto Gymkhana Croatia</h3>
              <p>
                Dio smo svjetske Moto Gymkhana zajednice i jedna od najaktivnijih udruga u Europi. Naši članovi su
                početnici, povratnici, instruktori i natjecatelji – ono što ih povezuje je želja da svaki dan voze
                bolje.
              </p>
              <p>
                Kroz godinu organiziramo edukacije, treninge, otvorene i zatvorene događaje te natjecanja za različite
                razine iskustva.
              </p>
              <div class="mg-about-meta">
                <span><strong>Članovi:</strong> 120+ aktivnih</span>
                <span><strong>Grad:</strong> Zagreb, ali dolazimo sa svih strana Hrvatske</span>
                <span><strong>Fokus:</strong> sigurnost, edukacija, zajednica</span>
              </div>
            </article>
          </div>
        </div>
      </div>
    </section>

    <!-- EDUKACIJE -->
    <section id="edukacije" class="py-5">
      <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-baseline gap-3 mb-4">
          <div>
            <div class="mg-section-eyebrow">Edukacije</div>
            <h2 class="mg-section-title">Škola. Poligon. Trening.</h2>
          </div>
          <p class="mg-section-subtitle">
            Programi za one koji tek ulaze u svijet motocikala, vozače povratnike i iskusne koji žele finu kontrolu i
            pripremu za natjecanja.
          </p>
        </div>

        <div class="row gy-4">
          <div class="col-md-4">
            <article class="card mg-card h-100">
              <div class="card-body">
                <span class="mg-pill-label">Škola sigurne vožnje</span>
                <h3 class="mg-edu-title text-secondary">Za nove vozače i povratnike</h3>
                <p class="mg-edu-body">
                  Idealno za one koji su tek položili ili se vraćaju nakon pauze. Fokus je na osnovama kontrole,
                  kočenju, izbjegavanju prepreka i sigurnom stavu na motociklu.
                </p>
                <p class="mg-edu-meta">
                  Cilj: samopouzdanje na cesti, razumijevanje granica sebe i motocikla.
                </p>
              </div>
            </article>
          </div>

          <div class="col-md-4">
            <article class="card mg-card h-100">
              <div class="card-body">
                <span class="mg-pill-label">Edukacijski poligon</span>
                <h3 class="mg-edu-title text-secondary">Za vozače koji žele više</h3>
                <p class="mg-edu-body">
                  Poligon s čunjevima koji traži preciznost, finu kontrolu gasa, kočnice i nagiba. Savršeno za vozače
                  koji žele razviti reflekse i pokrete za realne situacije.
                </p>
                <p class="mg-edu-meta">
                  Cilj: izgradnja mišićne memorije i sigurnih reakcija pri manjim brzinama.
                </p>
              </div>
            </article>
          </div>

          <div class="col-md-4">
            <article class="card mg-card h-100">
              <div class="card-body">
                <span class="mg-pill-label">Moto Gymkhana Trening</span>
                <h3 class="mg-edu-title text-secondary">Priprema za natjecanja</h3>
                <p class="mg-edu-body">
                  Napredni treninzi za iskusne vozače koji žele kontinuirano napredovati i voziti natjecateljske
                  poligone prema pravilima Moto Gymkhane.
                </p>
                <p class="mg-edu-meta">
                  Cilj: postizanje brzine kroz preciznost, ne samo “gas do kraja”.
                </p>
              </div>
            </article>
          </div>
        </div>
      </div>
    </section>

    <!-- NATJECANJA & SURADNJA -->
    <section id="natjecanja" class="py-5 mg-section-bg">
      <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-baseline gap-3 mb-4">
          <div>
            <div class="mg-section-eyebrow">Šta. Mi. Radimo.</div>
            <h2 class="mg-section-title"><span class="accent">Natjecanja</span> &amp; članstvo</h2>
          </div>
          <p class="mg-section-subtitle">
            Od prvih poligona do europskih prvenstava – Moto Gymkhana Croatia pokriva cijeli put, uz snažnu zajednicu
            iza kulisa.
          </p>
        </div>

        <div class="row gy-4">
          <div class="col-lg-7">
            <div class="d-grid gap-3">
              <article class="mg-stacked-item">
                <h3 class="mg-stacked-title text-secondary">Natjecanja Moto Gymkhane</h3>
                <p class="mg-stacked-body">
                  Utrke protiv vremena na tehničkim poligonima, po pravilima krovne Moto Gymkhana organizacije iz
                  Japana. Cilj je u što kraćem vremenu savladati zadani raspored čunjeva, uz maksimalnu kontrolu.
                </p>
              </article>

              <article class="mg-stacked-item">
                <h3 class="mg-stacked-title text-secondary">Postani član</h3>
                <p class="mg-stacked-body">
                  Članstvom podržavaš razvoj sporta, dobivaš pristup edukacijama, treninzima i popustima na događaje i
                  opremu. Pridruži se ekipi koja stalno radi na tome da vožnja bude sigurnija i zabavnija.
                </p>
              </article>

              <article class="mg-stacked-item">
                <h3 class="mg-stacked-title text-secondary">Sponzori i partneri</h3>
                <p class="mg-stacked-body">
                  Zahvalni smo svima koji podržavaju rad Udruge – od tehničkih partnera, guma, opreme, pa do lokalnih
                  zajednica. Bez njih bi bilo puno manje čunjeva i osmijeha.
                </p>
              </article>
            </div>
          </div>

          <div class="col-lg-5">
            <div class="d-grid gap-3">
              <article class="mg-stacked-item" id="europsko-prvenstvo">
                <h3 class="mg-stacked-title text-secondary">11th European Championship</h3>
                <p class="mg-stacked-body">
                  Hrvatska je domaćin 11. Europskog prvenstva Moto Gymkhana. Ovdje možeš istaknuti datum, lokaciju,
                  prijave i sve što je važno natjecateljima i publici.
                </p>
              </article>
              <article class="mg-stacked-item" id="suradnja">
                <h3 class="mg-stacked-title text-secondary">Suradnja</h3>
                <p class="mg-stacked-body">
                  Za partnerstva, edukacije zatvorenog tipa, suradnje s klubovima i kompanijama – javi nam se putem
                  kontakta. Poligon prilagođavamo različitim formatima i razinama iskustva.
                </p>
              </article>
            </div>
          </div>

        </div>
      </div>
    </section>


    <!-- LIVE + NOVOSTI -->
    <section id="novosti" class="py-5 mg-section-bg">
      <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-baseline gap-3 mb-4">
          <div>
            <div class="mg-section-eyebrow">Live rezultati &amp; novosti</div>
            <h2 class="mg-section-title">Provjerite. Što. Se. Događa.</h2>
          </div>
          <p class="mg-section-subtitle">
            Brzi uvid u live timing aktualnih natjecanja i pregled zadnjih vijesti iz svijeta Moto Gymkhana Croatia.
          </p>
        </div>

        <div class="row gy-4">
          <div class="col-lg-7">
            <article class="card mg-card h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-baseline mb-3">
                  <h3 class="mg-card-title mb-0  text-secondary">Live rezultati natjecanja</h3>
                  <span class="mg-card-tag">Live timing</span>
                </div>
                <p class="mg-card-text">
                  Praćenje vremena vožnje u stvarnom vremenu tijekom natjecanja Moto Gymkhane. Otvori live timing i
                  prati svoje ili rezultate prijatelja iz prvog reda.
                </p>
                <a href="https://live.motogymkhana.hr" class="btn mg-btn mt-3" target="_blank" rel="noreferrer">
                  Otvori live timing
                </a>
              </div>
            </article>
          </div>

          <div class="col-lg-5">
            <div class="small text-secondary d-grid gap-2 mg-live-meta">
              <span><strong>Sljedeće natjecanje:</strong> pogledaj raspored u sekciji Natjecanja</span>
              <span><strong>Live link:</strong> postavi URL za aktualno natjecanje u CMS-u</span>
              <span><strong>Napomena:</strong> ovaj blok možeš povezati s vlastitim timing backendom ili app-om.</span>
            </div>
          </div>
        </div>

        <div class="d-flex flex-wrap justify-content-between align-items-baseline gap-3 mt-5 mb-3">
          <div>
            <div class="mg-section-eyebrow mb-0">Novosti</div>
            <h2 class="mg-section-title">Najnovije vijesti.</h2>
          </div>
          <a href="novosti.php" class="btn mg-btn-outline">Sve novosti</a>
        </div>

        <div class="row g-4">
          <?php if (empty($latestArticles)): ?>
            <p class="text-secondary mb-0">Još nema objavljenih vijesti.</p>
          <?php else: ?>
            <?php foreach ($latestArticles as $article): ?>
              <div class="col-md-4">
                <article class="card h-100 mg-card mg-news-card-mini">
                  <div class="card-body">
                    <p class="mg-news-date mb-1">
                      <?= date('d.m.Y.', strtotime($article['created_at'])) ?>
                    </p>
            
                    <h3 class="h6 card-title text-secondary mb-2">
                      <a href="clanak.php?slug=<?= urlencode($article['slug']) ?>"
                         class="stretched-link text-decoration-none">
                        <?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?>
                      </a>
                    </h3>
            
                    <?php
                      $summarySource = $article['excerpt'] ?: $article['content'] ?? '';
                      $articleUrl    = 'clanak.php?slug=' . urlencode($article['slug']);
                    ?>
                    <p class="card-text text-secondary small mb-0">
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

    <!-- GALERIJA -->
    <section id="galerija" class="py-5">
      <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-baseline gap-3 mb-4">
          <div>
            <div class="mg-section-eyebrow">Galerija</div>
            <h2 class="mg-section-title">Pogledajte. Što. Radimo.</h2>
          </div>
          <p class="mg-section-subtitle">
            Treninzi, natjecanja, druženja – od prvog poligona do velikih događaja. Ovdje prikaži najbolje trenutke u
            fotografijama.
          </p>
        </div>

        <div class="row g-3">
          <div class="col-6 col-md-3">
            <div class="mg-gallery-item">
              <img src="assets/img/skola1.jpg" alt="Motocikl na poligonu s čunjevima">
              <span class="mg-gallery-label">Trening</span>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="mg-gallery-item">
              <img src="assets/img/vozaci_teorija.jpg" alt="Skupina vozača na Moto Gymkhana događaju">
              <span class="mg-gallery-label">Događaji</span>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="mg-gallery-item">
              <img src="assets/img/instruktor.jpg" alt="Instruktor objašnjava poligon">
              <span class="mg-gallery-label">Instrukcije</span>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="mg-gallery-item">
              <img src="assets/img/edukacija_hp.jpg" alt="Startna linija natjecanja">
              <span class="mg-gallery-label">Edukacije</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ČLANOVI -->
    <section id="clanovi" class="py-5">
      <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-baseline gap-3 mb-4">
          <div>
            <div class="mg-section-eyebrow">Članovi</div>
            <h2 class="mg-section-title">Ljudi. Moto. Gymkhane.</h2>
          </div>
          <p class="mg-section-subtitle">
            Predsjedništvo i organizacijski tim koji stoji iza treninga, natjecanja i svakog konusa na stazi.
          </p>
        </div>

        <div class="row gy-4">
          <div class="col-6 col-lg-3">
            <article class="mg-team-card h-100 text-center">
              <div class="mg-team-avatar mx-auto">
                <img src="assets/img/marinovic.png" alt="Željko Marinović">
              </div>
              <div class="mg-team-name">Željko Marinović</div>
              <div class="mg-team-role">Predsjednik</div>
              <p class="mg-team-note">
                Jedan od osnivača Moto Gymkhana Croatia i lice koje ćete često vidjeti na poligonu i u organizaciji.
              </p>
            </article>
          </div>
          <div class="col-6 col-lg-3">
            <article class="mg-team-card h-100 text-center">
              <div class="mg-team-avatar mx-auto">
                <img src="assets/img/turkalj.png" alt="Milan Turkalj">
              </div>
              <div class="mg-team-name">Milan Turkalj</div>
              <div class="mg-team-role">Dopredsjednik</div>
              <p class="mg-team-note">
                Brine da trening i natjecanja teku glatko – od poligona do logistike na dan događaja.
              </p>
            </article>
          </div>
          <div class="col-6 col-lg-3">
            <article class="mg-team-card h-100 text-center">
              <div class="mg-team-avatar mx-auto">
                <img src="assets/img/a_marinovic.png" alt="Andrea Marinović">
              </div>
              <div class="mg-team-name">Andrea Marinović</div>
              <div class="mg-team-role">Tajnica</div>
              <p class="mg-team-note">
                Organizacija, komunikacija i koordinacija – i zato poruke i prijave stižu na pravo mjesto.
              </p>
            </article>
          </div>
          <div class="col-6 col-lg-3">
            <article class="mg-team-card h-100 text-center">
              <div class="mg-team-avatar mx-auto">
                <img src="assets/img/gerhardinger.png" alt="David Gerhandinger">
              </div>
              <div class="mg-team-name">David Gerhandinger</div>
              <div class="mg-team-role">Blagajnik</div>
              <p class="mg-team-note">
                Čuva financije i brine da svi projekti i događaji stoje na čvrstim temeljima.
              </p>
            </article>
          </div>
        </div>

        <p class="mg-team-footer-note mt-4">
          Uz predsjedništvo, udrugu čini i organizacijski tim volontera koji svojim vremenom i energijom drže cijelu
          priču živom – od prvog postavljenog čunja do zadnje vožnje dana.
        </p>
      </div>
    </section>

  </main>

  <!-- FOOTER / KONTAKT -->
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
            <a href="#edukacije">Škola sigurne vožnje</a>
            <a href="#edukacije">Edukacijski poligoni</a>
            <a href="#edukacije">Moto Gymkhana trening</a>
          </div>
        </div>
        <div class="col-6 col-lg-2">
          <div class="mg-footer-heading">Linkovi</div>
          <div class="mg-footer-list">
            <a href="#natjecanja">Natjecanja</a>
            <a href="#natjecanja">Postani član</a>
            <a href="#natjecanja">Sponzori</a>
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

  <!-- Bootstrap JS bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    document.getElementById('year').textContent = new Date().getFullYear();
  </script>

  <a href="#top" class="btn btn-primary mg-back-to-top" aria-label="Povratak na vrh">
    ↑
  </a>

</body>

</html>