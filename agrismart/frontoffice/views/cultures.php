<?php
$pageTitle = 'Mes cultures';
$currentPage = 'cultures';
/** Attendu : $cultures (Culture[]), $parcelles (lignes SQL), $parcelleId (int, filtre) */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once __DIR__ . '/includes/head.php'; ?>
</head>
<body>
<?php require_once __DIR__ . '/includes/spinner.php'; ?>
<?php require_once __DIR__ . '/includes/navbar.php'; ?>
<?php require_once __DIR__ . '/includes/page-header.php'; ?>

<section class="container pb-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <h2 class="text-primary mb-0">Mes cultures</h2>
        <a href="<?= url('culture-form') ?>" class="btn btn-primary"><i class="fa fa-plus"></i> Nouvelle culture</a>
    </div>

    <form method="get" action="index.php" class="row g-2 mb-4">
        <input type="hidden" name="page" value="cultures">
        <div class="col-md-6">
            <select name="parcelle" class="form-select" aria-label="Filtrer par parcelle">
                <option value="0">Toutes les parcelles</option>
                <?php foreach ($parcelles as $p): ?>
                    <option value="<?= (int) $p['id'] ?>"<?= $parcelleId === (int) $p['id'] ? ' selected' : '' ?>><?= e($p['exploitation_nom'] . ' · ' . $p['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2 d-grid"><button class="btn btn-outline-primary" type="submit">Filtrer</button></div>
    </form>

    <?php if (!$cultures): ?>
        <div class="alert alert-info">Aucune culture<?= $parcelleId ? ' sur cette parcelle' : '' ?> pour le moment.</div>
    <?php else: ?>
        <div class="bg-light p-3">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Culture</th><th>Parcelle</th><th>Semis</th><th>Récolte</th><th>Eau utilisée</th><th class="text-end">Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($cultures as $c): ?>
                        <tr>
                            <td class="fw-semi-bold"><?= e($c->getNom()) ?></td>
                            <td><?= e($c->getExploitationNom()) ?> · <?= e($c->getParcelleNom()) ?></td>
                            <td><?= e(fmtDate($c->getDateSemis())) ?></td>
                            <td><?= e(fmtDate($c->getDateRecolte())) ?></td>
                            <td><?= e(fmtLitres($c->getEauTotale())) ?></td>
                            <td class="text-end text-nowrap">
                                <a href="<?= url('irrigations', ['culture' => $c->getId()]) ?>" class="btn btn-sm btn-outline-primary">Irrigation</a>
                                <a href="<?= url('culture-form', ['id' => $c->getId()]) ?>" class="btn btn-sm btn-outline-secondary">Modifier</a>
                                <form method="post" action="<?= url('culture-delete') ?>" class="d-inline js-confirm" data-confirm="Supprimer la culture « <?= e($c->getNom()) ?> » et ses irrigations ?">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= $c->getId() ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa fa-trash-alt"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<?php require_once __DIR__ . '/includes/scripts.php'; ?>
</body>
</html>
