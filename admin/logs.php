<?php
/**
 * Admin Logs Viewer - View Activity and Security Logs
 * 
 * Features:
 * - View recent activity logs
 * - View security events
 * - Filter by action type
 * - Statistics overview
 * - Download logs
 */

define('IN_APP', true);
require __DIR__ . '/../includes/config.php';
require_admin();

// Ensure logger functions are available
if (!function_exists('read_activity_logs')) {
    require __DIR__ . '/../includes/logger.php';
}

// Get filter parameters
$filterAction = $_GET['action'] ?? '';
$filterSeverity = $_GET['severity'] ?? '';
$logType = $_GET['type'] ?? 'activity'; // 'activity' or 'security'
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;

// Read logs based on type
if ($logType === 'security') {
    $logs = read_security_logs($limit, $filterSeverity !== '' ? $filterSeverity : null);
    $pageTitle = 'Sigurnosni logovi';
} else {
    $logs = read_activity_logs($limit, $filterAction !== '' ? $filterAction : null);
    $pageTitle = 'Logovi aktivnosti';
}

// Get statistics (last 7 days)
$stats = get_activity_stats(7);
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="utf-8">
    <title><?= $pageTitle ?> – Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    
    <style>
        .log-entry {
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
        }
        .status-success { color: #28a745; }
        .status-failed { color: #dc3545; }
        .severity-low { color: #17a2b8; }
        .severity-medium { color: #ffc107; }
        .severity-high { color: #fd7e14; }
        .severity-critical { color: #dc3545; font-weight: bold; }
        .log-table {
            font-size: 0.85rem;
        }
    </style>
</head>
<body class="bg-dark text-light">
    <header id="top">
        <?php include __DIR__ . '/../includes/nav.php'; ?>
    </header>

    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">
                <i class="bi bi-file-earmark-text"></i> <?= $pageTitle ?>
            </h1>
            <div class="d-flex gap-2">
                <a href="novosti.php" class="btn btn-outline-light btn-sm">← Članci</a>
                <a href="galerije.php" class="btn btn-outline-light btn-sm">Galerije</a>
                <a href="logout.php" class="btn btn-outline-secondary btn-sm">Odjava</a>
            </div>
        </div>

        <div class="row">
            <!-- Statistics Card -->
            <div class="col-md-3 mb-4">
                <div class="card bg-body-tertiary">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-graph-up"></i> Statistika (7 dana)
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($stats)): ?>
                            <p class="text-muted small">Nema aktivnosti</p>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($stats as $action => $count): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0 px-0 py-1">
                                        <span class="small"><?= htmlspecialchars($action) ?></span>
                                        <span class="badge bg-primary rounded-pill"><?= $count ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Log Type Switcher -->
                <div class="card bg-body-tertiary mt-3">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bi bi-toggle-on"></i> Tip logova</h6>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="?type=activity" class="list-group-item list-group-item-action <?= $logType === 'activity' ? 'active' : '' ?>">
                            <i class="bi bi-activity"></i> Aktivnost
                        </a>
                        <a href="?type=security" class="list-group-item list-group-item-action <?= $logType === 'security' ? 'active' : '' ?>">
                            <i class="bi bi-shield-lock"></i> Sigurnost
                        </a>
                    </div>
                </div>
            </div>

            <!-- Logs Table -->
            <div class="col-md-9">
                <!-- Filters -->
                <div class="card bg-body-tertiary mb-3">
                    <div class="card-body">
                        <form method="get" class="row g-3">
                            <input type="hidden" name="type" value="<?= htmlspecialchars($logType) ?>">
                            
                            <?php if ($logType === 'activity'): ?>
                                <div class="col-md-4">
                                    <label class="form-label small">Filter po akciji</label>
                                    <select name="action" class="form-select form-select-sm">
                                        <option value="">Sve akcije</option>
                                        <option value="login" <?= $filterAction === 'login' ? 'selected' : '' ?>>Login</option>
                                        <option value="logout" <?= $filterAction === 'logout' ? 'selected' : '' ?>>Logout</option>
                                        <option value="create_article" <?= $filterAction === 'create_article' ? 'selected' : '' ?>>Kreiraj članak</option>
                                        <option value="update_article" <?= $filterAction === 'update_article' ? 'selected' : '' ?>>Ažuriraj članak</option>
                                        <option value="delete_article" <?= $filterAction === 'delete_article' ? 'selected' : '' ?>>Obriši članak</option>
                                        <option value="create_gallery" <?= $filterAction === 'create_gallery' ? 'selected' : '' ?>>Kreiraj galeriju</option>
                                        <option value="update_gallery" <?= $filterAction === 'update_gallery' ? 'selected' : '' ?>>Ažuriraj galeriju</option>
                                        <option value="delete_gallery" <?= $filterAction === 'delete_gallery' ? 'selected' : '' ?>>Obriši galeriju</option>
                                        <option value="upload_images" <?= $filterAction === 'upload_images' ? 'selected' : '' ?>>Upload slika</option>
                                    </select>
                                </div>
                            <?php else: ?>
                                <div class="col-md-4">
                                    <label class="form-label small">Filter po severity</label>
                                    <select name="severity" class="form-select form-select-sm">
                                        <option value="">Svi nivoi</option>
                                        <option value="low" <?= $filterSeverity === 'low' ? 'selected' : '' ?>>Low</option>
                                        <option value="medium" <?= $filterSeverity === 'medium' ? 'selected' : '' ?>>Medium</option>
                                        <option value="high" <?= $filterSeverity === 'high' ? 'selected' : '' ?>>High</option>
                                        <option value="critical" <?= $filterSeverity === 'critical' ? 'selected' : '' ?>>Critical</option>
                                    </select>
                                </div>
                            <?php endif; ?>
                            
                            <div class="col-md-3">
                                <label class="form-label small">Broj zapisa</label>
                                <select name="limit" class="form-select form-select-sm">
                                    <option value="50" <?= $limit === 50 ? 'selected' : '' ?>>50</option>
                                    <option value="100" <?= $limit === 100 ? 'selected' : '' ?>>100</option>
                                    <option value="200" <?= $limit === 200 ? 'selected' : '' ?>>200</option>
                                    <option value="500" <?= $limit === 500 ? 'selected' : '' ?>>500</option>
                                </select>
                            </div>
                            
                            <div class="col-md-5 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bi bi-funnel"></i> Primijeni
                                </button>
                                <a href="?type=<?= htmlspecialchars($logType) ?>" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-x-circle"></i> Očisti
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Logs Table -->
                <div class="card bg-body-tertiary">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-list-ul"></i> Zapisi (<?= count($logs) ?>)
                        </h5>
                    </div>
                    <div class="table-responsive">
                        <?php if (empty($logs)): ?>
                            <div class="card-body">
                                <div class="alert alert-info mb-0">
                                    <i class="bi bi-info-circle"></i> Nema logova za prikaz.
                                </div>
                            </div>
                        <?php else: ?>
                            <table class="table table-dark table-hover table-sm mb-0 log-table">
                                <thead>
                                    <tr>
                                        <th style="width: 150px;">Vrijeme</th>
                                        <?php if ($logType === 'activity'): ?>
                                            <th style="width: 120px;">Akcija</th>
                                            <th>Detalji</th>
                                            <th style="width: 100px;">Status</th>
                                            <th style="width: 100px;">Korisnik</th>
                                        <?php else: ?>
                                            <th style="width: 120px;">Event</th>
                                            <th>Detalji</th>
                                            <th style="width: 100px;">Severity</th>
                                        <?php endif; ?>
                                        <th style="width: 120px;">IP adresa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($logs as $log): ?>
                                        <tr>
                                            <td class="text-muted small">
                                                <?= htmlspecialchars($log['timestamp']) ?>
                                            </td>
                                            
                                            <?php if ($logType === 'activity'): ?>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        <?= htmlspecialchars($log['action']) ?>
                                                    </span>
                                                </td>
                                                <td class="log-entry">
                                                    <?= htmlspecialchars($log['details']) ?>
                                                </td>
                                                <td>
                                                    <span class="status-<?= htmlspecialchars($log['status']) ?>">
                                                        <?= htmlspecialchars($log['status']) ?>
                                                    </span>
                                                </td>
                                                <td class="text-muted small">
                                                    <?= htmlspecialchars($log['user']) ?>
                                                </td>
                                            <?php else: ?>
                                                <td>
                                                    <span class="badge bg-warning">
                                                        <?= htmlspecialchars($log['event']) ?>
                                                    </span>
                                                </td>
                                                <td class="log-entry">
                                                    <?= htmlspecialchars($log['details']) ?>
                                                </td>
                                                <td>
                                                    <span class="severity-<?= htmlspecialchars($log['severity']) ?>">
                                                        <?= strtoupper(htmlspecialchars($log['severity'])) ?>
                                                    </span>
                                                </td>
                                            <?php endif; ?>
                                            
                                            <td class="text-muted small">
                                                <?= htmlspecialchars($log['ip']) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
