<?php
$pageTitle = 'Irrigation';
$currentPage = 'irrigations';
/** Attendu : $irrigations (Irrigation[]), $cultures (lignes SQL), $cultureId, $statut, $eauRealisee (float), $nbPlanifiees (int) */
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
        <h2 class="text-primary mb-0">Irrigation</h2>
        <a href="<?= url('irrigation-form', $cultureId ? ['culture' => $cultureId] : []) ?>" class="btn btn-primary"><i class="fa fa-plus"></i> Nouvelle irrigation</a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="bg-light stat-card p-4 d-flex align-items-center h-100">
                <div class="stat-icon me-3"><i class="fa fa-tint"></i></div>
                <div><div class="fs-3 fw-bold"><?= e(fmtLitres($eauRealisee)) ?></div><div class="text-muted">Eau utilisée (irrigations réalisées affichées)</div></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="bg-light stat-card p-4 d-flex align-items-center h-100">
                <div class="stat-icon me-3"><i class="fa fa-calendar-check"></i></div>
                <div><div class="fs-3 fw-bold"><?= (int) $nbPlanifiees ?></div><div class="text-muted">Irrigation(s) planifiée(s) affichée(s)</div></div>
            </div>
        </div>
    </div>

    <form method="get" action="index.php" class="row g-2 mb-4">
        <input type="hidden" name="page" value="irrigations">
        <div class="col-md-5">
            <select name="culture" class="form-select" aria-label="Filtrer par culture">
                <option value="0">Toutes les cultures</option>
                <?php foreach ($cultures as $c): ?>
                    <option value="<?= (int) $c['id'] ?>"<?= $cultureId === (int) $c['id'] ? ' selected' : '' ?>><?= e($c['nom'] . ' (' . $c['parcelle_nom'] . ')') ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="statut" class="form-select" aria-label="Filtrer par statut">
                <option value="">Tous les statuts</option>
                <?php foreach (Irrigation::LIBELLES as $key => $label): ?>
                    <option value="<?= e($key) ?>"<?= $statut === $key ? ' selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2 d-grid"><button class="btn btn-outline-primary" type="submit">Filtrer</button></div>
    </form>

    <?php if (!$irrigations): ?>
        <div class="alert alert-info">Aucune irrigation enregistrée<?= ($cultureId || $statut) ? ' avec ces filtres' : '' ?>.</div>
    <?php else: ?>
        <div class="bg-light p-3">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Date</th><th>Culture</th><th>Quantité</th><th>Méthode</th><th>Durée</th><th>Statut</th><th class="text-end">Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($irrigations as $i): ?>
                        <tr>
                            <td><?= e(fmtDate($i->getDate())) ?></td>
                            <td class="fw-semi-bold"><?= e($i->getCultureNom()) ?> <small class="text-muted">(<?= e($i->getParcelleNom()) ?>)</small></td>
                            <td><?= e(fmtLitres($i->getQuantiteEau())) ?></td>
                            <td><?= e($i->getMethode()) ?></td>
                            <td><?= $i->getDuree() ?> min</td>
                            <td><span class="badge <?= $i->isRealisee() ? 'bg-success' : 'bg-warning text-dark' ?>"><?= e(Irrigation::LIBELLES[$i->getStatut()] ?? $i->getStatut()) ?></span></td>
                            <td class="text-end text-nowrap">
                                <?php if (!$i->isRealisee()): ?>
                                    <form method="post" action="<?= url('irrigation-done') ?>" class="d-inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= $i->getId() ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-primary" title="Marquer comme réalisée"><i class="fa fa-check"></i></button>
                                    </form>
                                <?php endif; ?>
                                <a href="<?= url('irrigation-form', ['id' => $i->getId()]) ?>" class="btn btn-sm btn-outline-secondary">Modifier</a>
                                <form method="post" action="<?= url('irrigation-delete') ?>" class="d-inline js-confirm" data-confirm="Supprimer cette irrigation ?">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= $i->getId() ?>">
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
