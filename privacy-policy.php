<?php
define('IN_APP', true);
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/seo-meta.php';
?>
<!DOCTYPE html>
<html lang="hr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  
  <?php
  generate_seo_meta([
      'title' => 'Pravila privatnosti i uvjeti korištenja',
      'description' => 'Pravila privatnosti, uvjeti korištenja portala i zaštita osobnih podataka korisnika Moto Gymkhana Croatia.',
      'keywords' => 'privatnost, uvjeti korištenja, zaštita podataka, GDPR',
      'type' => 'website'
  ]);
  ?>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Google Font-->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;800&display=swap" rel="stylesheet">

  <!-- Custom stilovi -->
  <link rel="stylesheet" href="assets/css/style.css">
  
  <style>
    .privacy-content {
      background: rgba(255, 255, 255, 0.05);
      border-radius: 15px;
      padding: 2rem;
      margin-bottom: 2rem;
    }
    .privacy-content h2 {
      color: #ffc107;
      font-size: 1.75rem;
      font-weight: 600;
      margin-top: 2rem;
      margin-bottom: 1rem;
      padding-bottom: 0.5rem;
      border-bottom: 2px solid rgba(255, 193, 7, 0.3);
    }
    .privacy-content h2:first-child {
      margin-top: 0;
    }
    .privacy-content h3 {
      color: #fff;
      font-size: 1.25rem;
      font-weight: 500;
      margin-top: 1.5rem;
      margin-bottom: 0.75rem;
    }
    .privacy-content p {
      color: rgba(255, 255, 255, 0.8);
      line-height: 1.8;
      margin-bottom: 1rem;
    }
    .privacy-content ul {
      color: rgba(255, 255, 255, 0.8);
      line-height: 1.8;
      margin-bottom: 1rem;
      padding-left: 1.5rem;
    }
    .privacy-content ul li {
      margin-bottom: 0.5rem;
    }
    .privacy-content strong {
      color: #ffc107;
    }
    .last-updated {
      color: rgba(255, 255, 255, 0.5);
      font-size: 0.875rem;
      margin-top: 2rem;
      padding-top: 1rem;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
    }
  </style>
</head>

<body>

  <!-- NAVBAR -->
  <header id="top">
    <?php include __DIR__ . '/includes/nav.php'; ?>
  </header>

  <main>
    <section class="py-5">
      <div class="container">
        <div class="mb-4">
          <a href="index.php" class="btn btn-outline-light btn-sm">← Natrag na početnu</a>
        </div>

        <div class="mb-4">
          <div class="mg-section-eyebrow">Pravila i uvjeti</div>
          <h1 class="mg-section-title">Privatnost i uvjeti korištenja</h1>
        </div>

        <div class="privacy-content">
          <h2>Zaštita privatnosti i osobnih podataka</h2>
          <p>
            Moto Gymkhana Croatia poštuje privatnost svih posjetitelja i korisnika portala. 
            Prikupljanje, obrada i korištenje osobnih podataka provodi se u skladu s 
            <strong>Općom uredbom o zaštiti podataka (GDPR)</strong> i važećim zakonima Republike Hrvatske.
          </p>

          <h3>Koje podatke prikupljamo</h3>
          <ul>
            <li><strong>Podaci o registraciji:</strong> Korisničko ime, email adresa, lozinka (hashirana)</li>
            <li><strong>Podaci o korištenju:</strong> IP adresa, preglednici, vrijeme pristupa,stranice koje posjećujete</li>
            <li><strong>Komunikacija:</strong> Sadržaj komentara, poruka i drugih interakcija na portalu</li>
          </ul>

          <h3>Kako koristimo vaše podatke</h3>
          <ul>
            <li>Omogućavanje pristupa i funkcionalnosti portala</li>
            <li>Komunikacija s korisnicima (obavijesti, odgovori na upite)</li>
            <li>Poboljšanje korisničkog iskustva i sadržaja</li>
            <li>Zaštita od zloupotrebe i sigurnosnih prijetnji</li>
            <li>Analiza statistike korištenja portala (anonimizirano)</li>
          </ul>

          <h3>Vaša prava</h3>
          <p>U skladu s GDPR-om, imate sljedeća prava:</p>
          <ul>
            <li><strong>Pravo na pristup:</strong> Možete zatražiti kopiju svojih osobnih podataka</li>
            <li><strong>Pravo na ispravak:</strong> Možete ispraviti netočne ili nepotpune podatke</li>
            <li><strong>Pravo na brisanje:</strong> Možete zatražiti brisanje svojih podataka</li>
            <li><strong>Pravo na prigovor:</strong> Možete se usprotiviti obradi svojih podataka</li>
            <li><strong>Pravo na prenosivost:</strong> Možete zatražiti prijenos podataka drugom pružatelju usluga</li>
          </ul>

          <h2>Linkovi na vanjske stranice</h2>
          <p>
            Moto Gymkhana Croatia može sadržavati linkove na web stranice izvan vlastitog portala. 
            Linkove objavljujemo u dobroj namjeri i ne možemo se smatrati odgovornima za sadržaje 
            izvan našeg portala. Preporučujemo da pročitate pravila privatnosti vanjskih stranica 
            prije nego što im pružite osobne podatke.
          </p>

          <h2>Registrirani korisnici</h2>
          <p>
            Registriranjem na Moto Gymkhana Croatia portal, korisnik je obvezan:
          </p>
          <ul>
            <li>Završiti postupak registracije i unijeti istinite podatke</li>
            <li>Odabrati jedinstveno korisničko ime i sigurnu lozinku</li>
            <li>Čuvati svoje pristupne podatke i odmah prijaviti neovlašteno korištenje računa</li>
            <li>Biti isključivo odgovoran za sve sadržaje objavljene pod vlastitim računom</li>
            <li>Ponašati se u skladu s pravilima portala i važećim zakonima</li>
          </ul>
          <p>
            <strong>Moto Gymkhana Croatia nije odgovoran za sadržaje koje objavljuju korisnici.</strong> 
            Za sve sadržaje objavljene pod pojedinim korisničkim računom odgovoran je isključivo 
            korisnik koji ga koristi.
          </p>
          <p>
            Moto Gymkhana Croatia je ovlašten, po utvrđivanju opravdanosti prijave, u razumnom roku 
            ukloniti sadržaje korisnika koji nisu u skladu s važećim zakonima i pravilima portala.
          </p>
          <p>
            Moto Gymkhana Croatia zadržava pravo zabraniti korištenje portala pojedinom korisniku 
            ukoliko se ustanovi kršenje Uvjeta korištenja.
          </p>

          <h2>Komunikacijski servisi (E-mail i poruke)</h2>
          <p>
            Moto Gymkhana Croatia omogućuje posjetiteljima besplatno korištenje komunikacijskih 
            servisa (kontakt forme, poruke) pod uvjetom da ne krše pravila korištenja portala.
          </p>
          <p>
            Korištenjem komunikacijskih servisa obvezujete se slati i primati materijale koji su 
            zakoniti i odgovarajući.
          </p>
          <p>
            <strong>Komunikacijski servisi ponuđeni su besplatno, u dobroj namjeri.</strong> 
            Moto Gymkhana Croatia ne može se držati odgovornim za bilo kakvu štetu nastalu 
            korištenjem istih.
          </p>

          <h2>Nedozvoljen sadržaj</h2>
          <p>
            Prilikom korištenja portala Moto Gymkhana Croatia <strong>strogo je zabranjeno:</strong>
          </p>
          <ul>
            <li>Koristiti servise za spam, lančana pisma ili bilo koji oblik neželjenog masovnog slanja</li>
            <li>Maltretirati, ugrožavati, vrijeđati, prijetiti ili obmanjivati druge korisnike</li>
            <li>Objavljivati nedoličan, vulgaran, nepristojan, rasistički ili nezakonit sadržaj</li>
            <li>Uploadati ili širiti sadržaje zaštićene autorskim pravima bez dopuštenja</li>
            <li>Uploadati datoteke koje sadržavaju viruse, trojane ili zlonamjerni software</li>
            <li>Lažno se predstavljati u svrhu prijevare ili obmane</li>
            <li>Sugerirati službeni odnos s Moto Gymkhana Croatia portalom bez ovlaštenja</li>
            <li>Kršiti bilo koji važeći zakon Republike Hrvatske ili međunarodno pravo</li>
            <li>Prijetiti drugim korisnicima ili subjektima članaka (može rezultirati kaznenim progonom)</li>
            <li>Iznositi uvrede ili govor mržnje na temelju nacionalne, rasne, spolne, vjerske pripadnosti</li>
            <li>Prikupljati i objavljivati osobne podatke drugih osoba bez pristanka</li>
            <li>Objavljivati neistinite informacije s ciljem zavaravanja, klevete ili "trollanja"</li>
            <li>Kršiti autorska prava, objavljivati oglase ili netematski sadržaj (spam)</li>
          </ul>
          <p>
            Moto Gymkhana Croatia nema obvezu kontinuirano pratiti sve sadržaje, ali zadržava pravo 
            da u slučaju opravdane prijave, <strong>bez prethodne obavijesti</strong>, ukloni bilo 
            koji materijal koji nije u skladu s pravilima.
          </p>

          <h2>Pravila komentiranja</h2>
          <p>
            Svim korisnicima portala dodatno je zabranjeno:
          </p>
          <ul>
            <li>Vrijeđanje ostalih korisnika, autora članaka ili subjekata članaka</li>
            <li>Upotreba psovki (osim kao stilski izraz, ne upućenih izravno nekome)</li>
            <li>Pisanje bilo kojim pismom osim latinice</li>
            <li>Prijeteće ili nametljivo ponašanje</li>
            <li>Namjerno ometanje diskusija repetitivnim ili nesadržajnim porukama</li>
          </ul>
          <p>
            <strong>Korisnik je osobno odgovoran za sadržaj koji objavljuje</strong>, budući da 
            portal ne može kontrolirati i/ili revidirati svaku objavu u realnom vremenu.
          </p>
          <p>
            Moto Gymkhana Croatia pridržava pravo ukloniti, premjestiti ili urediti svaku objavu 
            koja predstavlja kršenje ovih Uvjeta. U slučaju teškog ili učestalog kršenja Uvjeta, 
            portal pridržava pravo izbrisati korisnika iz arhive, čime će se automatski izbrisati 
            svi njegovi prethodni komentari.
          </p>

          <h2>Prijava nedozvoljenog sadržaja</h2>
          <p>
            Moto Gymkhana Croatia ima velik broj korisnika koji u realnom vremenu, samostalno 
            objavljuju komentare, pa nije u mogućnosti kontrolirati cjelokupni sadržaj.
          </p>
          <p>
            <strong>Svaka osoba koja smatra da je neki korisnik portala povrijedio njena prava 
            ili prava trećih osoba</strong>, dužna je takav sadržaj odmah prijaviti putem e-mail 
            adrese: <a href="mailto:info@motogymkhana.hr" class="text-warning">info@motogymkhana.hr</a>
          </p>
          <p>
            Nakon prijave nedozvoljenog sadržaja, Moto Gymkhana Croatia će, u slučaju utvrđenja 
            da se radi o nedozvoljenom sadržaju i povredi ovih Uvjeta korištenja, odmah ukloniti 
            prijavljeni sadržaj.
          </p>

          <h2>Kolačići (Cookies)</h2>
          <p>
            Moto Gymkhana Croatia koristi kolačiće (cookies) za poboljšanje korisničkog iskustva 
            i funkcionalnosti portala.
          </p>
          <h3>Vrste kolačića koje koristimo:</h3>
          <ul>
            <li><strong>Nužni kolačići:</strong> Omogućuju osnovne funkcije (prijava, navigacija)</li>
            <li><strong>Funkcionalni kolačići:</strong> Pamte vaše postavke i preference</li>
            <li><strong>Analitički kolačići:</strong> Prikupljaju anonimizirane podatke o korištenju</li>
          </ul>
          <p>
            Možete kontrolirati i ograničiti kolačiće kroz postavke vašeg preglednika. 
            Napominjemo da onemogućavanje kolačića može utjecati na funkcionalnost portala.
          </p>

          <h2>Sigurnost podataka</h2>
          <p>
            Moto Gymkhana Croatia primjenjuje odgovarajuće tehničke i organizacijske mjere zaštite:
          </p>
          <ul>
            <li>Hashiranje lozinki (bcrypt algoritam)</li>
            <li>HTTPS šifrirana komunikacija</li>
            <li>Zaštita od CSRF napada (Cross-Site Request Forgery)</li>
            <li>Rate limiting za sprječavanje brute force napada</li>
            <li>Redovito ažuriranje sustava i sigurnosnih zakrpa</li>
            <li>Backup podataka i disaster recovery plan</li>
          </ul>
          <p>
            Unatoč primjeni sigurnosnih mjera, nijedan sustav nije 100% siguran. 
            Moto Gymkhana Croatia ne može garantirati apsolutnu sigurnost podataka.
          </p>

          <h2>Promjene pravila privatnosti</h2>
          <p>
            Moto Gymkhana Croatia zadržava pravo povremeno promijeniti ili modificirati ova pravila 
            korištenja i privatnosti. O važnim promjenama obavijestit ćemo korisnike putem:
          </p>
          <ul>
            <li>Obavijesti na portalu</li>
            <li>E-mail obavijesti registriranim korisnicima</li>
            <li>Ažuriranja ove stranice s novim datumom izmjene</li>
          </ul>
          <p>
            <strong>Nastavkom korištenja portala nakon objave promjena, smatrat će se da ste 
            upoznati i suglasni s najnovijim pravilima.</strong>
          </p>

          <h2>Kontakt</h2>
          <p>
            Za sva pitanja vezana uz zaštitu privatnosti, pravila korištenja ili ostvarivanje 
            vaših prava u vezi s osobnim podacima, kontaktirajte nas:
          </p>
          <ul>
            <li><strong>E-mail:</strong> <a href="mailto:info@motogymkhana.hr" class="text-warning">info@motogymkhana.hr</a></li>
            <li><strong>Kontakt forma:</strong> <a href="index.php#kontakt" class="text-warning">Kontakt forma na portalu</a></li>
          </ul>

          <div class="last-updated">
            <p class="mb-0">
              <strong>Posljednje ažurirano:</strong> <?= date('d.m.Y.') ?><br>
              Verzija dokumenta: 2.0
            </p>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- FOOTER -->
  <?php include __DIR__ . '/includes/footer.php'; ?>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
