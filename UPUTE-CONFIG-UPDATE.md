# Upute za ažuriranje production config.php

## Dodaj nove include-ove

Otvori `includes/config.php` i dodaj ove linije **na početak fajla, odmah nakon IN_APP provjere**:

```php
<?php
if (!defined('IN_APP')) {
    http_response_code(403);
    exit('Forbidden');
}

// =============================================================================
// ERROR HANDLING & LOGGING
// =============================================================================

// Load error handler (must be first)
require_once __DIR__ . '/error-handler.php';

// Load activity logger
require_once __DIR__ . '/logger.php';

// =============================================================================
// DATABASE CONFIGURATION
// =============================================================================

$dbHost = 'localhost';
// ... ostalo ...
```

## Provjera

Nakon dodavanja include-ova, testiraj:

1. Otvori admin panel: `/admin/novosti.php`
2. Klikni na "Logovi" u navigaciji
3. Trebala bi se otvoriti stranica sa logovima aktivnosti

## Ako i dalje dobivaaš error 500:

1. Privremeno omogući prikaz errora u `config.php`:
   ```php
   error_reporting(E_ALL);
   ini_set('display_errors', '1');
   ```

2. Osvježi stranicu i vidi točan error

3. Javi koji je error za debugging
