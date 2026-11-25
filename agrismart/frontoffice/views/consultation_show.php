<?php
$pageTitle = 'Consultation';
$currentPage = 'consultations';
/** Attendu : $account, $isExpert (bool), $consultation (Consultation), $conseils (Conseil[]) */
$statut = $consultation->getStatut();
$mine = $isExpert && $consultation->getExpertId() === $account->getId();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once __DIR__ . '/includes/head.php'; ?>
</head>
<body>
<?php require_once __DIR__ . '/includes/spinner.php'; ?>
<?php require_once __DIR__ . '/includes/navbar.php'; ?>
<?php $breadcrumb = [['Consultations', url('consultations')], [$consultation->getSujet(), null]]; ?>
<?php require_once __DIR__ . '/includes/page-header.php'; ?>

<section class="container pb-5">
    <div class="row g-4">
        <!-- Demande -->
        <div class="col-lg-5">
            <div class="bg-light p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h4 class="text-primary mb-0"><?= e($consultation->getSujet()) ?></h4>
                    <?= consultationBadge($consultation) ?>
                </div>
                <p class="mb-3" style="white-space: pre-line;"><?= e($consultation->getDescription()) ?></p>
                <ul class="list-unstyled text-muted small mb-4">
                    <li><i class="fa fa-user me-2"></i>Agriculteur : <?= e($consultation->getAgriculteurNom()) ?></li>
                    <li><i class="fa fa-home me-2"></i>Exploitation : <?= e($consultation->getExploitationNom() ?: 'non précisée') ?></li>
                    <li><i class="fa fa-calendar-alt me-2"></i>Demandée le <?= e(fmtDate($consultation->getDateDemande())) ?></li>
                    <li><i class="fa fa-user-tie me-2"></i>Expert : <?= e($consultation->getExpertNom() ?: 'pas encore pris en charge') ?></li>
                </ul>

                <div class="d-flex flex-wrap gap-2">
                    <?php if ($isExpert): ?>
                        <?php if ($consultation->getExpertId() === null && $statut === Consultation::STATUT_OUVERTE): ?>
                            <form method="post" action="<?= url('consultation-take') ?>">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= $consultation->getId() ?>">
                                <button type="submit" class="btn btn-primary">Prendre en charge</button>
                            </form>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if ($statut === Consultation::STATUT_OUVERTE): ?>
                            <a href="<?= url('consultation-form', ['id' => $consultation->getId()]) ?>" class="btn btn-outline-secondary">Modifier</a>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (($mine || !$isExpert) && $statut !== Consultation::STATUT_CLOTUREE): ?>
                        <form method="post" action="<?= url('consultation-close') ?>" class="js-confirm" data-confirm="Clôturer cette consultation ? Plus aucun conseil ne pourra être ajouté.">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= $consultation->getId() ?>">
                            <button type="submit" class="btn btn-outline-primary"><i class="fa fa-check me-1"></i> Clôturer</button>
                        </form>
                    <?php endif; ?>

                    <?php if (!$isExpert && $statut !== Consultation::STATUT_EN_COURS): ?>
                        <form method="post" action="<?= url('consultation-delete') ?>" class="js-confirm" data-confirm="Supprimer définitivement cette consultation ?">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= $consultation->getId() ?>">
                            <button type="submit" class="btn btn-outline-danger"><i class="fa fa-trash-alt me-1"></i> Supprimer</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Conseils -->
        <div class="col-lg-7">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Conseils <span class="badge bg-secondary"><?= count($conseils) ?></span></h4>
                <?php if ($mine && $statut === Consultation::STATUT_EN_COURS): ?>
                    <a href="<?= url('conseil-form', ['consultation' => $consultation->getId()]) ?>" class="btn btn-primary"><i class="fa fa-plus"></i> Donner un conseil</a>
                <?php endif; ?>
            </div>

            <?php if (!$conseils): ?>
                <div class="alert alert-info">
                    <?= $isExpert ? 'Aucun conseil pour le moment.' : 'Aucun conseil publié pour le moment. Vous serez informé dès qu\'un expert aura répondu.' ?>
                </div>
            <?php endif; ?>

            <?php foreach ($conseils as $k): $own = $isExpert && $k->getExpertId() === $account->getId(); ?>
                <div class="bg-light p-4 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong class="text-primary"><i class="fa fa-user-tie me-2"></i><?= e($k->getExpertNom() ?: 'Expert supprimé') ?></strong>
                        <small class="text-muted"><?= e(fmtDate($k->getDateEnvoi())) ?></small>
                    </div>
                    <p class="mb-2" style="white-space: pre-line;"><?= e($k->getContenu()) ?></p>
                    <?php if ($isExpert && $k->isSensible()): ?>
                        <span class="badge <?= $k->isValide() ? 'bg-success' : 'bg-warning text-dark' ?>">
                            <?= $k->isValide() ? 'Conseil sensible validé' : 'Conseil sensible : en attente de validation (invisible pour l\'agriculteur)' ?>
                        </span>
                    <?php endif; ?>
                    <?php if ($own && $statut !== Consultation::STATUT_CLOTUREE): ?>
                        <div class="mt-3">
                            <a href="<?= url('conseil-form', ['id' => $k->getId()]) ?>" class="btn btn-sm btn-outline-secondary">Modifier</a>
                            <form method="post" action="<?= url('conseil-delete') ?>" class="d-inline js-confirm" data-confirm="Supprimer ce conseil ?">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= $k->getId() ?>">
                                <input type="hidden" name="consultation" value="<?= $consultation->getId() ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa fa-trash-alt"></i></button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<?php require_once __DIR__ . '/includes/scripts.php'; ?>
</body>
</html>
