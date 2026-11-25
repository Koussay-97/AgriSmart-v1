<?php
$pageTitle = 'Consultations';
$currentPage = 'consultations';
/** Attendu : $account (Expert), $aTraiter (Consultation[] ouvertes), $mesConsultations (Consultation[]) */
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
    <h3 class="text-primary mb-1">Bonjour, <?= e($account->getNom()) ?></h3>
    <p class="text-muted mb-4"><?= e($account->getSpecialite()) ?> · <?= $account->getExperience() ?> an(s) d'expérience</p>

    <h4 class="mb-3">À traiter <span class="badge bg-warning text-dark"><?= count($aTraiter) ?></span></h4>
    <?php if (!$aTraiter): ?>
        <div class="alert alert-info mb-5">Aucune consultation en attente : tout est pris en charge.</div>
    <?php else: ?>
        <div class="bg-light p-3 mb-5">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Sujet</th><th>Agriculteur</th><th>Exploitation</th><th>Date</th><th class="text-end">Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($aTraiter as $c): ?>
                        <tr>
                            <td class="fw-semi-bold"><?= e($c->getSujet()) ?></td>
                            <td><?= e($c->getAgriculteurNom()) ?></td>
                            <td><?= e($c->getExploitationNom() ?: '—') ?></td>
                            <td><?= e(fmtDate($c->getDateDemande())) ?></td>
                            <td class="text-end text-nowrap">
                                <a href="<?= url('consultation', ['id' => $c->getId()]) ?>" class="btn btn-sm btn-outline-primary">Lire</a>
                                <form method="post" action="<?= url('consultation-take') ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= $c->getId() ?>">
                                    <button type="submit" class="btn btn-sm btn-primary">Prendre en charge</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <h4 class="mb-3">Mes consultations <span class="badge bg-secondary"><?= count($mesConsultations) ?></span></h4>
    <?php if (!$mesConsultations): ?>
        <div class="alert alert-info">Vous n'avez encore pris aucune consultation en charge.</div>
    <?php else: ?>
        <div class="bg-light p-3">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Sujet</th><th>Agriculteur</th><th>Date</th><th>Conseils</th><th>Statut</th><th class="text-end">Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($mesConsultations as $c): ?>
                        <tr>
                            <td class="fw-semi-bold"><?= e($c->getSujet()) ?></td>
                            <td><?= e($c->getAgriculteurNom()) ?></td>
                            <td><?= e(fmtDate($c->getDateDemande())) ?></td>
                            <td><?= $c->getNbConseils() ?></td>
                            <td><?= consultationBadge($c) ?></td>
                            <td class="text-end"><a href="<?= url('consultation', ['id' => $c->getId()]) ?>" class="btn btn-sm btn-outline-primary">Ouvrir</a></td>
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
