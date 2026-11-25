<?php
$pageTitle = 'Tableau de bord';
$currentPage = 'dashboard';
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
    <h3 class="text-primary mb-1">Bonjour, <?= e($user->getNom()) ?></h3>
    <p class="text-muted mb-4">Voici un aperçu de votre activité · Région : <?= e($user->getRegion()) ?></p>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="bg-light stat-card p-4 d-flex align-items-center h-100">
                <div class="stat-icon me-3"><i class="fa fa-home"></i></div>
                <div><div class="fs-2 fw-bold"><?= count($exploitations) ?></div><div class="text-muted">Exploitation(s)</div></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="bg-light stat-card p-4 d-flex align-items-center h-100">
                <div class="stat-icon me-3"><i class="fa fa-th-large"></i></div>
                <div><div class="fs-2 fw-bold"><?= $nbParcelles ?></div><div class="text-muted">Parcelle(s)</div></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="bg-light stat-card p-4 d-flex align-items-center h-100">
                <div class="stat-icon me-3"><i class="fa fa-ruler-combined"></i></div>
                <div><div class="fs-2 fw-bold"><?= number_format($surface, 1, ',', ' ') ?> ha</div><div class="text-muted">Surface totale</div></div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="bg-light stat-card p-4 d-flex align-items-center h-100">
                <div class="stat-icon me-3"><i class="fa fa-seedling"></i></div>
                <div><div class="fs-2 fw-bold"><?= (int) $nbCultures ?></div><div class="text-muted">Culture(s) · <a href="<?= url('cultures') ?>">voir</a></div></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="bg-light stat-card p-4 d-flex align-items-center h-100">
                <div class="stat-icon me-3"><i class="fa fa-tint"></i></div>
                <div><div class="fs-2 fw-bold"><?= e(fmtLitres($eauTotale)) ?></div><div class="text-muted">Eau utilisée · <a href="<?= url('irrigations') ?>">détail</a></div></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="bg-light stat-card p-4 d-flex align-items-center h-100">
                <div class="stat-icon me-3"><i class="fa fa-comments"></i></div>
                <div><div class="fs-2 fw-bold"><?= (int) $nbConsultations ?></div><div class="text-muted">Consultation(s) active(s) · <a href="<?= url('consultations') ?>">voir</a></div></div>
            </div>
        </div>
    </div>

    <?php if ($prochaines): ?>
        <div class="bg-light p-4 mb-5">
            <h5 class="text-primary mb-3"><i class="fa fa-calendar-check me-2"></i>Prochaines irrigations planifiées</h5>
            <ul class="list-unstyled mb-0">
                <?php foreach ($prochaines as $i): ?>
                    <li class="mb-1"><strong><?= e(fmtDate($i->getDate())) ?></strong> · <?= e($i->getCultureNom()) ?> (<?= e($i->getParcelleNom()) ?>) · <?= e(fmtLitres($i->getQuantiteEau())) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Mes exploitations</h4>
        <a href="<?= url('exploitation-form') ?>" class="btn btn-primary"><i class="fa fa-plus me-2"></i>Ajouter</a>
    </div>
    <?php if (!$exploitations): ?>
        <div class="alert alert-info">Vous n'avez pas encore d'exploitation. Commencez par en ajouter une.</div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($exploitations as $ex): ?>
                <div class="col-md-6">
                    <div class="bg-light p-4 h-100 d-flex flex-column">
                        <h5 class="text-primary"><?= e($ex->getNom()) ?></h5>
                        <p class="text-muted mb-2"><i class="fa fa-map-marker-alt me-2"></i><?= e($ex->getLocalisation()) ?></p>
                        <p class="mb-3"><?= count($ex->getParcelles()) ?> parcelle(s) · <?= number_format($ex->calculerSurface(), 1, ',', ' ') ?> ha</p>
                        <a href="<?= url('parcelles', ['exploitation' => $ex->getId()]) ?>" class="btn btn-outline-primary btn-sm align-self-start mt-auto">Voir les parcelles</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<?php require_once __DIR__ . '/includes/scripts.php'; ?>
</body>
</html>
