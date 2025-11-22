<?php
define('IN_APP', true);
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/seo-meta.php';
require_once __DIR__ . '/lib.php';

$valid_cols = ['gp8_1', 'gp8_2', 'track1', 'track2'];
$by = isset($_GET['by']) && in_array($_GET['by'], $valid_cols, true) ? $_GET['by'] : 'gp8_1';
$data = read_data();

// Sort by selected column (ascending). DNFs and missing times go to bottom.
usort($data, function($a, $b) use ($by) {
    $adnf = !empty($a[$by . '_dnf']);
    $bdnf = !empty($b[$by . '_dnf']);
    if ($adnf !== $bdnf) return $adnf ? 1 : -1; // non-DNF before DNF
    $ak = isset($a[$by.'_ms']) && is_numeric($a[$by.'_ms']) ? intval($a[$by.'_ms']) : PHP_INT_MAX;
    $bk = isset($b[$by.'_ms']) && is_numeric($b[$by.'_ms']) ? intval($b[$by.'_ms']) : PHP_INT_MAX;
    if ($ak === $bk) return 0;
    return ($ak < $bk) ? -1 : 1;
});
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <?php
    generate_seo_meta([
        'title' => 'miCROseconds Masters 2025 - Leaderboard',
        'description' => 'Trenutni rezultati i rang lista natjecatelja na miCROseconds MotoGymkhana Masters 2025 natjecanju.',
        'keywords' => 'leaderboard, rezultati, microseconds, masters, gymkhana, natjecanje, 2025',
        'type' => 'website'
    ]);
    ?>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Google Font-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;800&display=swap" rel="stylesheet">
    
    <!-- Main site CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    
    <style>
        .micro-hero {
            padding: 3rem 0;
            margin-bottom: 2rem;
            text-align: center;
        }
        .micro-hero h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }
        .micro-hero p {
            font-size: 1.1rem;
            opacity: 0.8;
        }
        .controls-top {
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .controls-top label {
            font-weight: 600;
        }
        .table-responsive {
            margin-top: 2rem;
        }
        .table-leaderboard {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            overflow: hidden;
        }
        .table-leaderboard thead th {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            font-weight: 600;
            padding: 1rem;
            border: none;
        }
        .table-leaderboard tbody td {
            padding: 0.75rem 1rem;
            border-color: rgba(255, 255, 255, 0.1);
        }
        .table-leaderboard tbody tr:hover {
            background: rgba(255, 193, 7, 0.1);
        }
        .dnf {
            color: #dc3545;
            font-weight: 700;
        }
        .alert-info-custom {
            background: rgba(255, 193, 7, 0.1);
            border-left: 4px solid #ffc107;
            padding: 1rem;
            margin-top: 2rem;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <header id="top">
        <?php include __DIR__ . '/../includes/nav.php'; ?>
    </header>

<main>
    <section class="py-5">
        <div class="container">
            <div class="mb-3">
                <a href="../index.php" class="btn btn-outline-light btn-sm">← Natrag na početnu</a>
            </div>
            
            <div class="micro-hero text-center">
                <h1>miCROseconds MotoGymkhana Masters 2025</h1>
                <p>Trenutni rezultati natjecanja • Live Leaderboard</p>
            </div>
            
            <div class="controls-top">
                <label for="by">Sortiraj po utrci:</label>
                <select id="by" name="by" onchange="location.href='?by='+this.value">
                    <option value="gp8_1" <?php if ($by==='gp8_1') echo 'selected'; ?>>GP8-1</option>
                    <option value="gp8_2" <?php if ($by==='gp8_2') echo 'selected'; ?>>GP8-2</option>
                    <option value="track1" <?php if ($by==='track1') echo 'selected'; ?>>Track 1</option>
                    <option value="track2" <?php if ($by==='track2') echo 'selected'; ?>>Track 2</option>
                </select>
            </div>

    <div class="table-responsive">
        <table class="table table-leaderboard table-hover">
            <thead>
                <tr>
                    <th>Startni broj</th>
                    <th>Ime</th>
                    <th>Prezime</th>
                    <th>GP8-1</th>
                    <th>GP8-2</th>
                    <th>Staza 1</th>
                    <th>Staza 2</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($data)): ?>
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <i class="bi bi-info-circle fs-1 mb-3 d-block" style="opacity: 0.5;"></i>
                        <p class="mb-0" style="opacity: 0.7;">Još nema rezultata. Leaderboard će biti ažuriran nakon početka natjecanja.</p>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($data as $row):
                    $sb = sanitize_text($row['startni_broj'] ?? '');
                    $ime = sanitize_text($row['ime'] ?? '');
                    $prez = sanitize_text($row['prezime'] ?? '');
                    $cells = [];
                    foreach (['gp8_1','gp8_2','track1','track2'] as $col) {
                        $dnf = !empty($row[$col . '_dnf']);
                        $cells[$col] = $dnf ? '<span class="dnf">DNF</span>' : sanitize_text(format_time_colon($row[$col . '_ms'] ?? ''));
                    }
                ?>
                    <tr>
                        <td><?php echo $sb; ?></td>
                        <td><?php echo $ime; ?></td>
                        <td><?php echo $prez; ?></td>
                        <td><?php echo $cells['gp8_1']; ?></td>
                        <td><?php echo $cells['gp8_2']; ?></td>
                        <td><?php echo $cells['track1']; ?></td>
                        <td><?php echo $cells['track2']; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
            
            <div class="alert-info-custom">
                <p class="mb-0">
                    <strong>Format vremena:</strong> MM:ss:mmm (npr. 02:15:378). Prihvaćamo i točku umjesto zadnje dvotočke (02:15.378).
                    <?php if (is_admin()): ?>
                        &nbsp;•&nbsp;
                        <a href="admin.php" class="text-warning fw-bold">Admin panel</a>
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
