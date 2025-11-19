<?php
declare(strict_types=1);

define('IN_APP', true);
require __DIR__ . '/config.php';
require_admin();

$errors = [];
$success = null;

// Brisanje galerije (opcionalno)
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    if ($id > 0) {
        $stmt = $pdo->prepare("DELETE FROM galleries WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $success = 'Galerija je obrisana (ako je postojala).';
    }
}

// Dohvati sve galerije
$stmt = $pdo->query("
    SELECT id, name, slug, created_at
    FROM galleries
    ORDER BY created_at DESC
");
$galleries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="utf-8">
    <title>Galerije – admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Tvoj glavni CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-dark text-light">
    <div class="container py-4">
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">Galerije</h1>
            <div class="d-flex gap-2">
                <a href="admin-novosti.php" class="btn btn-outline-light btn-sm">← Članci</a>
                <a href="admin-galerija-uredi.php" class="btn btn-primary btn-sm">+ Nova galerija</a>
                <a href="admin-logout.php" class="btn btn-outline-secondary btn-sm">Odjava</a>
            </div>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if (!$galleries): ?>
            <div class="alert alert-info">
                Trenutno nema nijedne galerije. Klikni na <strong>+ Nova galerija</strong> za dodavanje.
            </div>
        <?php else: ?>
            <div class="table-responsive bg-body-tertiary rounded-3">
                <table class="table table-dark table-striped table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Naziv</th>
                            <th>Slug</th>
                            <th>Datum</th>
                            <th style="width: 160px;">Akcije</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($galleries as $g): ?>
                            <tr>
                                <td><?= (int)$g['id'] ?></td>
                                <td><?= htmlspecialchars($g['name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="text-secondary small"><?= htmlspecialchars($g['slug'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="text-secondary small">
                                    <?= htmlspecialchars($g['created_at'], ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="admin-galerija-uredi.php?id=<?= (int)$g['id'] ?>" class="btn btn-outline-light">
                                            Uredi
                                        </a>
                                        <a href="?delete=<?= (int)$g['id'] ?>"
                                           class="btn btn-outline-danger"
                                           onclick="return confirm('Sigurno obrisati ovu galeriju?');">
                                            Obriši
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
