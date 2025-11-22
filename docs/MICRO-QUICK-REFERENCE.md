# Micro Leaderboard - Quick Reference

## 🎯 Brzi pristup

### URL-ovi
- **Public leaderboard:** `/micro/index.php`
- **Admin panel:** `/micro/admin.php`
- **Glavni meni link:** "Rezultati" (između "Natjecanja" i "Suradnja")

### Prijava u admin
**Jedinstvena prijava sa CMS-om:**
- Koristite `/admin/login.php` sa glavnim CMS credentials
- Nakon uspješne prijave, imate automatski pristup micro admin panelu
- Nema odvojenih credentials za micro!

---

## ⚡ Brze operacije

### Dodavanje novog natjecatelja
1. Klik na **"+ Dodaj natjecatelja"** u admin panelu
2. Popunite:
   - Startni broj
   - Ime
   - Prezime
   - Vremena za staze (format: MM:ss:mmm, npr. 02:15:378)
   - Označite DNF checkbox ako natjecatelj nije završio
3. Klik **"Dodaj"**

### Uređivanje rezultata
1. Klik **"Uredi"** pored natjecatelja
2. Promijenite podatke
3. Klik **"Spremi promjene"**

### Brisanje natjecatelja
1. Klik **"Obriši"** pored natjecatelja
2. Potvrdite brisanje

---

## 🕐 Format vremena

### Unos vremena
- **Format:** `MM:ss:mmm` (minuta:sekunda:milisekunda)
- **Primjeri:**
  - `02:15:378` → 2 minute, 15 sekundi, 378 milisekundi
  - `01:45:200` → 1 minuta, 45 sekundi, 200 milisekundi
  - `00:59:999` → 59 sekundi, 999 milisekundi

### Alternativni unos (bez znakova)
Možete upisati samo brojeve:
- `21378` → automatski formatira u `02:15:378`
- `145200` → automatski formatira u `01:45:200`

### DNF (Did Not Finish)
Ako natjecatelj nije završio stazu:
1. Označite **DNF checkbox** za tu stazu
2. Vrijeme se neće prikazivati (prikazat će se "DNF")

---

## 🏁 Staze

Leaderboard prati 4 staze:
1. **GP8-1** - Prva GP8 staza
2. **GP8-2** - Druga GP8 staza
3. **Track 1** - Prva trening staza
4. **Track 2** - Druga trening staza

---

## 📊 Sortiranje rezultata

### Javni prikaz
Korisnici mogu sortirati rezultate po stazi:
- **GP8-1:** Sortira po GP8-1 vremenu (najbrži prvi)
- **GP8-2:** Sortira po GP8-2 vremenu
- **Track 1:** Sortira po Track 1 vremenu
- **Track 2:** Sortira po Track 2 vremenu

DNF rezultati idu na kraj liste.

---

## 🔐 Sigurnost

### Jedinstvena autentifikacija
- **Prijava:** `/admin/login.php` (glavni CMS login)
- **Rate limiting:** 5 neuspješnih pokušaja = 15 min blokada (glavni CMS)
- **Session security:** Session regeneracija, secure cookies

### CSRF zaštita
Svi POST zahtjevi (dodavanje, uređivanje, brisanje) zaštićeni su CSRF tokenom iz glavnog CMS-a.

### Logiranje
Sve akcije se logiraju:
- Pristup micro admin panelu
- Dodavanje natjecatelja
- Uređivanje natjecatelja
- Brisanje natjecatelja

**Pregled logova:** `/admin/logs.php`

---

## 🛠️ Maintenance

### Promjena admin lozinke

Micro koristi glavni CMS login:

1. **Generiraj novi hash:**
```bash
php -r "echo password_hash('nova_lozinka', PASSWORD_BCRYPT);"
```

2. **Kopiraj hash u `includes/config.php`:**
```php
define('ADMIN_PASSWORD_HASH', '$2y$10$...novi_hash...');
```

**Napomena:** Ova lozinka se koristi za cijeli CMS, uključujući micro admin.

### Backup podataka

**JSON datoteka:** `micro/data/data.json`

```bash
# Kopiranje backup-a
cp micro/data/data.json backups/data-$(date +%Y%m%d-%H%M%S).json
```

### Vraćanje podataka iz backup-a

```bash
# Restore iz backup-a
cp backups/data-20250115-120000.json micro/data/data.json
```

---

## 🐛 Troubleshooting

### Problem: Ne mogu se prijaviti
**Provjeri:**
1. Je li lozinka ispravna u `includes/config.php` (glavni CMS)?
2. Je li blokiran zbog previše pokušaja? (čekaj 15 minuta - glavni CMS rate limit)
3. Ima li greške u `/logs/security.log`?
4. Koristi `/admin/login.php` za prijavu (ne postoji odvojeni micro login)

### Problem: Ne mogu spremiti rezultate
**Provjeri:**
1. Dozvole na `micro/data/data.json` (mora biti writable)
   ```bash
   chmod 664 micro/data/data.json
   ```
2. Dozvole na `micro/data/` direktoriju
   ```bash
   chmod 775 micro/data/
   ```

### Problem: Prikazuje se greška "Invalid CSRF token"
**Uzrok:** Sesija je istekla ili token nije valjan.

**Rješenje:**
1. Refresh stranice
2. Pokušaj ponovno

### Problem: Ne prikazuju se rezultati
**Provjeri:**
1. Je li `micro/data/data.json` valjan JSON?
   ```bash
   php -r "json_decode(file_get_contents('micro/data/data.json'));"
   ```
2. Ako je prazan, inicijaliziraj:
   ```bash
   echo "[]" > micro/data/data.json
   ```

---

## 📱 Responsive design

Leaderboard je optimiziran za sve uređaje:
- **Desktop:** Puna tablica sa svim stazama
- **Tablet:** Prilagođen prikaz sa scroll-om
- **Mobitel:** Kompaktna tablica sa horizontalnim scroll-om

---

## 🔗 Integracija sa CMS-om

### Navigacijski link
Link "Rezultati" automatski se prikazuje u glavnoj navigaciji:

```
O nama | Novosti | Edukacije | Galerije | Natjecanja | **Rezultati** | Suradnja | Kontakt
```

### Shared komponente
- **Navigacija:** `includes/nav.php`
- **Footer:** `includes/footer.php`
- **SEO meta:** `includes/seo-meta.php`
- **Logger:** `includes/logger.php`

### Autentifikacija
- **Jedinstveni login:** Micro koristi glavni CMS login (`/admin/login.php`)
- **Automatski pristup:** Nakon prijave u CMS, automatski pristup micro admin panelu
- **Nema odvojenih credentials:** Svi admini koriste isti login sustav

---

## 📞 Podrška

- **Email:** info@motogymkhana.hr
- **Admin panel:** https://www.motogymkhana.hr/admin/
- **Logovi:** https://www.motogymkhana.hr/admin/logs.php

---

## 📚 Dodatna dokumentacija

- **Puna dokumentacija:** `/docs/MICRO-LEADERBOARD.md`
- **Code dokumentacija:** `/docs/code-documentation.md`

---

*Quick reference kreiran: 2025*
