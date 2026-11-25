<?php
$pageTitle = 'Mes consultations';
$currentPage = 'consultations';
/** Attendu : $consultations (Consultation[]) - vue de l'agriculteur */
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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary mb-0">Mes consultations</h2>
        <a href="<?= url('consultation-form') ?>" class="btn btn-primary"><i class="fa fa-plus"></i> Nouvelle consultation</a>
    </div>
    <?php if (!$consultations): ?>
        <div class="alert alert-info">Vous n'avez posé aucune question pour le moment. Un expert agronome vous répond ici.</div>
    <?php else: ?>
        <div class="bg-light p-3">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Sujet</th><th>Exploitation</th><th>Date</th><th>Expert</th><th>Conseils</th><th>Statut</th><th class="text-end">Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($consultations as $c): ?>
                        <tr>
                            <td class="fw-semi-bold"><?= e($c->getSujet()) ?></td>
                            <td><?= e($c->getExploitationNom() ?: '—') ?></td>
                            <td><?= e(fmtDate($c->getDateDemande())) ?></td>
                            <td><?= e($c->getExpertNom() ?: 'En attente') ?></td>
                            <td><?= $c->getNbConseils() ?></td>
                            <td><?= consultationBadge($c) ?></td>
                            <td class="text-end text-nowrap">
                                <a href="<?= url('consultation', ['id' => $c->getId()]) ?>" class="btn btn-sm btn-outline-primary">Ouvrir</a>
                                <?php if ($c->getStatut() !== Consultation::STATUT_EN_COURS): ?>
                                    <form method="post" action="<?= url('consultation-delete') ?>" class="d-inline js-confirm" data-confirm="Supprimer la consultation « <?= e($c->getSujet()) ?> » ?">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= $c->getId() ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa fa-trash-alt"></i></button>
                                    </form>
                                <?php endif; ?>
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
