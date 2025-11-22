# Micro Leaderboard - miCROseconds MotoGymkhana Masters 2025

## Pregled

Micro leaderboard je modul za praćenje i prikaz rezultata natjecanja **miCROseconds MotoGymkhana Masters 2025**. Integriran je sa glavnim CMS-om i koristi zajedničku navigaciju, dizajn i sigurnosne sustave.

---

## Struktura

```
micro/
├── index.php           # Javni prikaz leaderboarda (integrirano sa CMS-om)
├── admin.php          # Admin panel za upravljanje rezultatima (integrirano sa CMS-om)
├── lib.php            # Helper funkcije (čitanje/pisanje JSON-a, parsiranje vremena)
├── config.php         # Konfiguracija (admin kredencijali, putanje)
├── assets/            # Statički resursi (CSS, logo)
│   ├── mgc-logo.png
│   └── ... (legacy CSS files)
└── data/
    ├── data.json      # JSON baza podataka sa rezultatima
    └── .htaccess      # Zaštita direktorija
```

---

## Funkcionalnosti

### Javni prikaz (index.php)
- ✅ **Integracija sa CMS-om**: Bootstrap 5, glavna navigacija, SEO meta tagovi
- ✅ **Sortiranje rezultata**: Po GP8-1, GP8-2, Track 1, Track 2
- ✅ **Format vremena**: MM:ss:mmm (npr. 02:15:378)
- ✅ **DNF označavanje**: Prikazuje "DNF" za natjecatelje koji nisu završili
- ✅ **Responsive dizajn**: Prilagođen za sve uređaje
- ✅ **Admin pristup**: Link na admin panel vidljiv samo prijavljenim adminima

### Admin panel (admin.php)
- ✅ **Integracija sa CMS-om**: Bootstrap 5, glavna navigacija, footer
- ✅ **Jedinstvena autentifikacija**: Koristi glavni CMS login (`/admin/login.php`)
- ✅ **CRUD operacije**: Dodavanje, uređivanje, brisanje natjecatelja
- ✅ **CSRF zaštita**: Svi POST zahtjevi zaštićeni
- ✅ **Logiranje**: Sve akcije se logiraju (pristup, dodavanje, uređivanje, brisanje)
- ✅ **Modal UI**: Moderna forma za uređivanje podataka
- ✅ **Time masking**: Automatsko formatiranje unosa vremena
- ✅ **CSS ekstrakcija**: Stilovi izdvojeni u `assets/admin-styles.css`

---

## Navigacija

Link "Rezultati" dodan u glavni navigacijski meni između "Natjecanja" i "Suradnja":

```
O nama | Novosti | Edukacije | Galerije | Natjecanja | **Rezultati** | Suradnja | Kontakt
```

---

## Struktura podataka (data.json)

```json
[
  {
    "id": 1,
    "startni_broj": "7",
    "ime": "Marko",
    "prezime": "Horvat",
    "gp8_1_ms": 135378,
    "gp8_2_ms": 130512,
    "track1_ms": 105200,
    "track2_ms": 107999,
    "gp8_1_dnf": false,
    "gp8_2_dnf": false,
    "track1_dnf": false,
    "track2_dnf": false
  }
]
```

### Polja:
- `id` (int): Jedinstveni identifikator
- `startni_broj` (string): Startni broj natjecatelja
- `ime` (string): Ime
- `prezime` (string): Prezime
- `gp8_1_ms` (int): Vrijeme za GP8-1 stazu u milisekundama
- `gp8_2_ms` (int): Vrijeme za GP8-2 stazu u milisekundama
- `track1_ms` (int): Vrijeme za Track 1 stazu u milisekundama
- `track2_ms` (int): Vrijeme za Track 2 stazu u milisekundama
- `gp8_1_dnf` (bool): DNF flag za GP8-1
- `gp8_2_dnf` (bool): DNF flag za GP8-2
- `track1_dnf` (bool): DNF flag za Track 1
- `track2_dnf` (bool): DNF flag za Track 2

---

## Autentifikacija

### ✅ Jedinstvena prijava sa glavnim CMS-om
Micro admin panel koristi istu autentifikaciju kao i glavni CMS:

1. **Prijava:** `/admin/login.php`
2. **Credentials:** Glavni CMS admin username/password
3. **Automatski pristup:** Nakon prijave u glavni CMS, automatski imate pristup micro admin panelu

**Nema odvojenih credentials!** Svi admini koji imaju pristup glavnom CMS-u automatski imaju pristup i micro admin panelu.

---

## Sigurnost

### ✅ Implementirano:
- **CSRF zaštita**: Svi POST zahtjevi provjeravaju `csrf_token()`
- **Rate limiting**: 5 neuspjelih pokušaja prijave = 30s blokada
- **Session regeneracija**: Nova sesija nakon uspješne prijave
- **XSS zaštita**: `sanitize_text()` za sve outpute
- **Atomic file writes**: `flock()` za sigurno pisanje u JSON
- **Logiranje**: Sve akcije se logiraju (admin/logs.php)
- **Directory protection**: `.htaccess` blokira direktan pristup `data/` folderu

### 🔒 Preporuke:
- Postavite jake lozinke u `micro/config.php`
- Regularne backup-e `data/data.json` datoteke
- Pregledajte logove za sumnjive aktivnosti

---

## API funkcije (lib.php)

### `read_data(): array`
Čita JSON podatke iz `data/data.json`.

### `write_data(array $arr): bool`
Piše podatke u JSON datoteku sa atomic locking-om (`flock()`).

### `parse_time_ms(string $str): int|false`
Parsira vrijeme u formatu MM:ss:mmm ili MMssmmm u milisekunde.

**Primjeri:**
- `"02:15:378"` → `135378` ms
- `"21378"` → `135378` ms (MMssmmm format)
- `"DNF"` → `false`

### `format_time_colon(int $ms): string`
Formatira milisekunde u MM:ss:mmm format za prikaz.

**Primjer:**
- `135378` ms → `"02:15:378"`

### `sanitize_text(string $str): string`
XSS zaštita (`htmlspecialchars()` wrapper).

### `csrf_token(): string`
Generira CSRF token.

### `csrf_check(string $token): bool`
Provjerava CSRF token.

### `is_logged_in(): bool`
Provjerava je li korisnik prijavljen u micro admin.

### `ensure_session(): void`
Inicijalizira sesiju sa sigurnosnim postavkama.

---

## Logiranje

Sve admin akcije se logiraju u `logs/activity.log` i pregledaju putem `admin/logs.php`:

### Logirane akcije:
- `login_micro`: Uspješna prijava u micro admin
- `create_competitor`: Dodan novi natjecatelj
- `update_competitor`: Ažuriran postojeći natjecatelj
- `delete_competitor`: Obrisan natjecatelj
- `login_failed_micro`: Neuspješan pokušaj prijave
- `login_rate_limit_micro`: Korisnik blokiran zbog previše pokušaja

---

## Troubleshooting

### Problem: Greška pri spremanju podataka
**Uzrok:** Neispravne dozvole na `data/data.json` datoteci.

**Rješenje:**
```bash
chmod 664 micro/data/data.json
chmod 775 micro/data/
```

### Problem: Ne prikazuje se leaderboard
**Uzrok:** Prazna ili neispravna JSON datoteka.

**Rješenje:**
```bash
echo "[]" > micro/data/data.json
```

### Problem: Ne mogu se prijaviti u admin panel
**Uzrok 1:** Neispravne dozvole na `logs/` direktoriju (logger ne može pisati).

**Rješenje:**
```bash
chmod 775 logs/
```

**Uzrok 2:** Pogrešan hash u `micro/config.php`.

**Rješenje:**
```php
// Generiranje novog hasha
echo password_hash('your_password', PASSWORD_BCRYPT);
// Kopiraj hash u micro/config.php
```

---

## Deployment checklist

### Produkcijska postavka:
- [ ] Promijenite `ADMIN_USER` i `ADMIN_PASS_HASH` u `micro/config.php`
- [ ] Postavite `APP_DEBUG = false` u `micro/config.php`
- [ ] Provjerite dozvole: `chmod 664 micro/data/data.json`
- [ ] Provjerite `.htaccess` zaštitu direktorija
- [ ] Testirajte autentifikaciju (samostalna i CMS prijava)
- [ ] Testirajte CRUD operacije (dodaj, uredi, obriši)
- [ ] Pregledajte logove (`admin/logs.php`)
- [ ] Backup `data/data.json` datoteke

---

## Nadogradnje

### Buduće mogućnosti:
- 📊 Statistike i grafikoni (najbrži krugovi, prosječna vremena)
- 📱 Live timing (WebSocket integracija)
- 📄 PDF izvještaji
- 🏆 Automatsko rangiranje po ukupnom zboju
- 📸 Integracija sa galerijama (foto natjecatelja)
- 📧 Email obavijesti o novim rezultatima

---

## Kontakt za podršku

Za tehničku podršku i pitanja:
- **Email:** info@motogymkhana.hr
- **Admin panel:** https://www.motogymkhana.hr/admin/
- **Logovi:** https://www.motogymkhana.hr/admin/logs.php

---

*Dokumentacija kreirana: 2025*  
*Zadnje ažuriranje: 2025*
