<?php
define('IN_APP', true);

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include required files
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/seo-meta.php';

// Logger may not exist in all setups - include with fallback
if (file_exists(__DIR__ . '/../includes/logger.php')) {
    require_once __DIR__ . '/../includes/logger.php';
} else {
    // Define dummy functions if logger doesn't exist
    function log_activity($action, $details, $status) { return true; }
    function log_security($event, $details, $severity) { return true; }
}

require_once __DIR__ . '/lib.php';

// --- Unified authentication: Use main CMS login ---
// Redirect to main CMS login if not authenticated as admin
if (!is_admin()) {
    $_SESSION['redirect_after_login'] = '/micro/admin.php';
    header('Location: ../admin/login.php');
    exit;
}

// Log access to micro admin (with error handling)
if (!isset($_SESSION['micro_admin_logged'])) {
    try {
        log_activity('access_micro_admin', 'Pristup micro admin panelu', 'success');
        $_SESSION['micro_admin_logged'] = true;
    } catch (Exception $e) {
        // Silently fail logging - don't break the page
        error_log('Micro admin logging failed: ' . $e->getMessage());
    }
}

$csrf = csrf_token();
$data = read_data();
$notice = '';
$error = '';

// DELETE (POST only)
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (!csrf_check($_POST['csrf'] ?? '')) {
        $error = 'Neispravan CSRF token.';
    } else {
        $id = isset($_POST['id']) && ctype_digit($_POST['id']) ? intval($_POST['id']) : null;
        if ($id === null) {
            $error = 'Neispravan ID.';
        } else {
            $deleted_competitor = null;
            foreach ($data as $r) {
                if (intval($r['id'] ?? -1) === $id) {
                    $deleted_competitor = $r;
                    break;
                }
            }
            $data = array_values(array_filter($data, function($r) use ($id) { return intval($r['id'] ?? -1) !== $id; }));
            if (!write_data($data)) {
                $error = 'Greška pri spremanju (write_data).';
            } else {
                $notice = 'Natjecatelj obrisan.';
                if ($deleted_competitor) {
                    log_activity('delete_competitor', 'Obrisan natjecatelj: ' . $deleted_competitor['ime'] . ' ' . $deleted_competitor['prezime'] . ' (ID: ' . $id . ')', 'success');
                }
            }
        }
    }
}

// SAVE (create/update)
if (isset($_POST['action']) && $_POST['action'] === 'save') {
    if (!csrf_check($_POST['csrf'] ?? '')) {
        $error = 'Neispravan CSRF token.';
    } else {
        $id = isset($_POST['id']) && ctype_digit($_POST['id']) ? intval($_POST['id']) : null;
        $ime = normalize_name($_POST['ime'] ?? '');
        $prezime = normalize_name($_POST['prezime'] ?? '');
        $startni_broj = normalize_startno($_POST['startni_broj'] ?? '');

        $t_gp8_1_raw = trim($_POST['gp8_1'] ?? '');
        $t_gp8_2_raw = trim($_POST['gp8_2'] ?? '');
        $t_track1_raw= trim($_POST['track1'] ?? '');
        $t_track2_raw= trim($_POST['track2'] ?? '');

        $t_gp8_1_ms = parse_time_ms($t_gp8_1_raw);
        $t_gp8_2_ms = parse_time_ms($t_gp8_2_raw);
        $t_track1_ms= parse_time_ms($t_track1_raw);
        $t_track2_ms= parse_time_ms($t_track2_raw);

        // DNF flags
        $dnf_gp8_1 = isset($_POST['gp8_1_dnf']);
        $dnf_gp8_2 = isset($_POST['gp8_2_dnf']);
        $dnf_track1= isset($_POST['track1_dnf']);
        $dnf_track2= isset($_POST['track2_dnf']);

        // Validate formats (allow empty -> default to zero, but not invalid)
        $invalids = [];
        if ($t_gp8_1_raw !== '' && $t_gp8_1_ms === false) $invalids[] = 'GP8-1';
        if ($t_gp8_2_raw !== '' && $t_gp8_2_ms === false) $invalids[] = 'GP8-2';
        if ($t_track1_raw !== '' && $t_track1_ms === false) $invalids[] = 'Track 1';
        if ($t_track2_raw !== '' && $t_track2_ms === false) $invalids[] = 'Track 2';

        if (!empty($invalids)) {
            $error = 'Neispravan format vremena za: ' . implode(', ', $invalids) . '. Koristi MM:ss:mmm (npr. 02:15:378).';
        } else {
            // Default empty to 0 ms
            $v1 = ($t_gp8_1_raw === '') ? 0 : $t_gp8_1_ms;
            $v2 = ($t_gp8_2_raw === '') ? 0 : $t_gp8_2_ms;
            $v3 = ($t_track1_raw === '') ? 0 : $t_track1_ms;
            $v4 = ($t_track2_raw === '') ? 0 : $t_track2_ms;

            if ($id === null) {
                // Create
                $row = [
                    'id' => next_id($data),
                    'startni_broj' => $startni_broj,
                    'ime' => $ime,
                    'prezime' => $prezime,
                    'gp8_1_ms' => $v1,
                    'gp8_2_ms' => $v2,
                    'track1_ms'=> $v3,
                    'track2_ms'=> $v4,
                    'gp8_1_dnf' => $dnf_gp8_1,
                    'gp8_2_dnf' => $dnf_gp8_2,
                    'track1_dnf' => $dnf_track1,
                    'track2_dnf' => $dnf_track2,
                ];
                $data[] = $row;
                if (!write_data($data)) {
                    $error = 'Greška pri spremanju (write_data).';
                } else {
                    $notice = 'Natjecatelj dodan.';
                    log_activity('create_competitor', 'Dodan natjecatelj: ' . $ime . ' ' . $prezime . ' (Startni broj: ' . $startni_broj . ')', 'success');
                }
            } else {
                // Update
                $found = false;
                foreach ($data as &$r) {
                    if (intval($r['id']) === $id) {
                        $r['startni_broj'] = $startni_broj;
                        $r['ime'] = $ime;
                        $r['prezime'] = $prezime;
                        $r['gp8_1_ms'] = $v1;
                        $r['gp8_2_ms'] = $v2;
                        $r['track1_ms']= $v3;
                        $r['track2_ms']= $v4;
                        $r['gp8_1_dnf'] = $dnf_gp8_1;
                        $r['gp8_2_dnf'] = $dnf_gp8_2;
                        $r['track1_dnf'] = $dnf_track1;
                        $r['track2_dnf'] = $dnf_track2;
                        $found = true;
                        break;
                    }
                }
                unset($r);
                if ($found) {
                    if (!write_data($data)) {
                        $error = 'Greška pri spremanju (write_data).';
                    } else {
                        $notice = 'Natjecatelj ažuriran.';
                        log_activity('update_competitor', 'Ažuriran natjecatelj: ' . $ime . ' ' . $prezime . ' (ID: ' . $id . ')', 'success');
                    }
                } else {
                    $error = 'Zapis nije pronađen.';
                }
            }
        }
    }
}

// EDIT load
$edit = null;
if (isset($_GET['edit']) && ctype_digit($_GET['edit'])) {
    $eid = intval($_GET['edit']);
    foreach ($data as $r) {
        if (intval($r['id']) === $eid) { $edit = $r; break; }
    }
}

// Auto-open modal on edit or validation error
$open_modal = $edit || (isset($_POST['action']) && $_POST['action'] === 'save' && $error !== '');
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <?php
    generate_seo_meta([
        'title' => 'Admin panel - miCROseconds Masters',
        'description' => 'Administracija rezultata miCROseconds MotoGymkhana Masters 2025.',
        'robots' => 'noindex,nofollow',
        'type' => 'website'
    ]);
    ?>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;800&display=swap" rel="stylesheet">
    
    <!-- Main site CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    
    <!-- Micro Admin CSS -->
    <link rel="stylesheet" href="assets/admin-styles.css">
</head>
<body>
    <header id="top">
        <?php include __DIR__ . '/../includes/nav.php'; ?>
    </header>
    
    <div class="container admin-header">
        <h1><i class="bi bi-speedometer2"></i> Admin panel - Leaderboard</h1>
        <div class="admin-controls">
            <button id="openAddModal" class="btn-admin"><i class="bi bi-plus-circle"></i> Dodaj natjecatelja</button>
            <a class="btn-admin" href="index.php"><i class="bi bi-list-ol"></i> Leaderboard</a>
            <a class="btn-admin" href="../admin/novosti.php"><i class="bi bi-newspaper"></i> CMS Admin</a>
            <a class="btn-admin danger" href="../admin/logout.php"><i class="bi bi-box-arrow-right"></i> Odjava</a>
        </div>
    </div>

    <main class="container">
        <?php if ($notice): ?><div class="alert-success"><?php echo sanitize_text($notice); ?></div><?php endif; ?>
        <?php if ($error):  ?><div class="alert-danger"><?php echo sanitize_text($error);  ?></div><?php endif; ?>

        <div class="card-admin">
            <h2><i class="bi bi-people"></i> Natjecatelji (<?php echo count($data); ?>)</h2>
            <div class="table-admin">
                <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Startni broj</th>
                        <th>Ime</th>
                        <th>Prezime</th>
                        <th>GP8-1</th>
                        <th>GP8-2</th>
                        <th>Track 1</th>
                        <th>Track 2</th>
                        <th>Akcije</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($data as $r): ?>
                    <tr>
                        <td><?php echo intval($r['id']); ?></td>
                        <td><?php echo sanitize_text($r['startni_broj']); ?></td>
                        <td><?php echo sanitize_text($r['ime']); ?></td>
                        <td><?php echo sanitize_text($r['prezime']); ?></td>
                        <td><?php echo !empty($r['gp8_1_dnf']) ? '<span class="dnf">DNF</span>' : format_time_colon($r['gp8_1_ms']); ?></td>
                        <td><?php echo !empty($r['gp8_2_dnf']) ? '<span class="dnf">DNF</span>' : format_time_colon($r['gp8_2_ms']); ?></td>
                        <td><?php echo !empty($r['track1_dnf']) ? '<span class="dnf">DNF</span>' : format_time_colon($r['track1_ms']); ?></td>
                        <td><?php echo !empty($r['track2_dnf']) ? '<span class="dnf">DNF</span>' : format_time_colon($r['track2_ms']); ?></td>
                        <td class="nowrap">
                            <a class="btn-small" href="admin.php?edit=<?php echo intval($r['id']); ?>">Uredi</a>
                            <form method="post" style="display:inline" onsubmit="return confirm('Obrisati natjecatelja?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo intval($r['id']); ?>">
                                <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                                <button class="btn-small danger" type="submit">Obriši</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                </table>
            </div>
        </div>
    </main>
    
    <?php include __DIR__ . '/../includes/footer.php'; ?>

<!-- Modal (overlay) -->
<div class="modal" id="entryModal" aria-hidden="true" role="dialog" aria-modal="true">
  <div class="modal-overlay" data-close-modal></div>
  <div class="modal-card" role="document">
    <div class="modal-header">
      <h3 id="modalTitle"><?php echo $edit ? 'Uredi natjecatelja' : 'Dodaj natjecatelja'; ?></h3>
      <button class="modal-close" title="Zatvori" aria-label="Zatvori" data-close-modal>&times;</button>
    </div>
    <div class="modal-body">
      <form id="entryForm" method="post" autocomplete="off">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
        <?php if ($edit): ?>
            <input type="hidden" name="id" value="<?php echo intval($edit['id']); ?>">
        <?php endif; ?>
        <div class="grid">
            <label>Startni broj
                <input type="text" name="startni_broj" required value="<?php echo $edit ? sanitize_text($edit['startni_broj']) : ''; ?>">
            </label>
            <label>Ime
                <input type="text" name="ime" required value="<?php echo $edit ? sanitize_text($edit['ime']) : ''; ?>">
            </label>
            <label>Prezime
                <input type="text" name="prezime" required value="<?php echo $edit ? sanitize_text($edit['prezime']) : ''; ?>">
            </label>

            <div class="field-group">
              <label>GP8-1 (MM:ss:mmm)
                  <input class="time-mask" type="text" name="gp8_1" placeholder="02:15:378" value="<?php echo $edit ? format_time_colon($edit['gp8_1_ms']) : '00:00:000'; ?>">
              </label>
              <label class="dnf-flag"><input type="checkbox" name="gp8_1_dnf" <?php echo ($edit && !empty($edit['gp8_1_dnf'])) ? 'checked' : ''; ?>> DNF</label>
            </div>

            <div class="field-group">
              <label>GP8-2 (MM:ss:mmm)
                  <input class="time-mask" type="text" name="gp8_2" placeholder="02:10:512" value="<?php echo $edit ? format_time_colon($edit['gp8_2_ms']) : '00:00:000'; ?>">
              </label>
              <label class="dnf-flag"><input type="checkbox" name="gp8_2_dnf" <?php echo ($edit && !empty($edit['gp8_2_dnf'])) ? 'checked' : ''; ?>> DNF</label>
            </div>

            <div class="field-group">
              <label>Track 1 (MM:ss:mmm)
                  <input class="time-mask" type="text" name="track1" placeholder="01:45:200" value="<?php echo $edit ? format_time_colon($edit['track1_ms']) : '00:00:000'; ?>">
              </label>
              <label class="dnf-flag"><input type="checkbox" name="track1_dnf" <?php echo ($edit && !empty($edit['track1_dnf'])) ? 'checked' : ''; ?>> DNF</label>
            </div>

            <div class="field-group">
              <label>Track 2 (MM:ss:mmm)
                  <input class="time-mask" type="text" name="track2" placeholder="01:47:999" value="<?php echo $edit ? format_time_colon($edit['track2_ms']) : '00:00:000'; ?>">
              </label>
              <label class="dnf-flag"><input type="checkbox" name="track2_dnf" <?php echo ($edit && !empty($edit['track2_dnf'])) ? 'checked' : ''; ?>> DNF</label>
            </div>
        </div>
        <div class="form-actions modal-actions">
            <button id="submitBtn" type="submit"><?php echo $edit ? 'Spremi promjene' : 'Dodaj'; ?></button>
            <button type="button" class="btn-secondary" data-close-modal>Odustani</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- JS: time mask + caret + modal control -->
<script>
// ====== Time mask for MM:ss:mmm ======
(function(){
  function formatForDisplay(raw) {
    var d = String(raw || '').replace(/[^0-9]/g, '').slice(0, 7);
    if (d.length === 0) return '';
    var ms  = d.slice(-3);
    var sec = d.slice(-5, -3);
    var min = d.slice(0, Math.max(0, d.length - 5));
    if (sec.length === 0) return (min || d);
    if (min.length === 0) { min = '0'; }
    var min2 = ('0' + min).slice(-2);
    var sec2 = sec.padStart(2, '0');
    return min2 + ':' + sec2 + (ms ? (':' + ms.padEnd(3, '0')) : '');
  }

  function onInput(e) {
    if (!e.target.matches('input.time-mask')) return;
    var el = e.target;
    var start = el.selectionStart;
    var old = el.value;
    var before = old.slice(0, start);
    var digitsBefore = before.replace(/[^0-9]/g, '').length;

    var formatted = formatForDisplay(old);
    el.value = formatted;

    var pos = 0, seenDigits = 0;
    while (pos < el.value.length && seenDigits < digitsBefore) {
      if (/\d/.test(el.value[pos])) seenDigits++;
      pos++;
    }
    el.setSelectionRange(pos, pos);
  }

  function onBlur(e){
    if (!e.target.matches('input.time-mask')) return;
    var el = e.target;
    el.value = formatForDisplay(el.value);
  }

  document.addEventListener('input', onInput);
  document.addEventListener('blur', onBlur, true);
})();

// ====== Force caret to start on focus (time inputs) ======
(function(){
  function caretStart(el){
    try { el.setSelectionRange(0,0); } catch(e){}
  }
  document.addEventListener('focusin', function(e){
    if (e.target && e.target.matches('input.time-mask')) {
      setTimeout(function(){ caretStart(e.target); }, 0);
    }
  });
  document.addEventListener('mousedown', function(e){
    var t = e.target;
    if (t && t.matches('input.time-mask')) {
      e.preventDefault();
      t.focus();
      setTimeout(function(){ caretStart(t); }, 0);
    }
  }, true);
})();

// ====== Modal logic ======
(function(){
  var modal = document.getElementById('entryModal');
  var openBtn = document.getElementById('openAddModal');
  var closeEls = modal.querySelectorAll('[data-close-modal]');
  var form = document.getElementById('entryForm');
  var titleEl = document.getElementById('modalTitle');
  var submitBtn = document.getElementById('submitBtn');

  function setMode(mode) {
    if (mode === 'add') {
      var id = form.querySelector('input[name="id"]');
      if (id) id.remove();
      var defaults = {
        'startni_broj': '',
        'ime': '',
        'prezime': '',
        'gp8_1': '00:00:000',
        'gp8_2': '00:00:000',
        'track1': '00:00:000',
        'track2': '00:00:000'
      };
      Object.keys(defaults).forEach(function(name){
        var el = form.querySelector('[name="'+name+'"]');
        if (el) el.value = defaults[name];
      });
      ['gp8_1_dnf','gp8_2_dnf','track1_dnf','track2_dnf'].forEach(function(name){
        var el = form.querySelector('[name="'+name+'"]');
        if (el) el.checked = false;
      });
      titleEl.textContent = 'Dodaj natjecatelja';
      submitBtn.textContent = 'Dodaj';
    } else {
      titleEl.textContent = 'Uredi natjecatelja';
      submitBtn.textContent = 'Spremi promjene';
    }
    var first = form.querySelector('input[name="startni_broj"]');
    if (first) first.focus();
  }

  function openModal(mode) {
    setMode(mode || 'add');
    modal.setAttribute('aria-hidden', 'false');
    modal.classList.add('open');
  }

  function closeModal() {
    modal.setAttribute('aria-hidden', 'true');
    modal.classList.remove('open');
  }

  openBtn && openBtn.addEventListener('click', function(){ openModal('add'); });
  closeEls.forEach(function(el){ el.addEventListener('click', closeModal); });
  modal.addEventListener('click', function(e){ if (e.target === modal) closeModal(); });
  document.addEventListener('keydown', function(e){ if (e.key === 'Escape' && modal.classList.contains('open')) closeModal(); });

  <?php if ($open_modal): ?>
  openModal(<?php echo $edit ? "'edit'" : "'add'"; ?>);
  <?php endif; ?>
})();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
