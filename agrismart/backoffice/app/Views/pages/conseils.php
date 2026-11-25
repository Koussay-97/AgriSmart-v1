<?php
/** pages/conseils.php : conseils sensibles à valider. Variables : $conseils (Conseil[]) */
?>
<section class="page-content">
    <h2 class="text-primary mb-1">Conseils à valider</h2>
    <nav aria-label="breadcrumb"><ol class="breadcrumb">
        <li class="breadcrumb-item">Back Office</li>
        <li class="breadcrumb-item active" aria-current="page">Conseils à valider</li>
    </ol></nav>
    <p class="text-muted">Les conseils marqués « sensibles » par l'expert ne sont visibles par l'agriculteur qu'après votre validation.</p>

    <?php if (!$conseils): ?>
        <div class="alert alert-success">Aucun conseil en attente de validation.</div>
    <?php endif; ?>
    <?php foreach ($conseils as $k): ?>
        <div class="card p-4 mb-3">
            <div class="d-flex flex-wrap justify-content-between text-muted small mb-2">
                <span>Expert : <strong><?= e($k->getExpertNom() ?: 'supprimé') ?></strong> · Agriculteur : <strong><?= e($k->getAgriculteurNom()) ?></strong></span>
                <span><?= e(substr($k->getDateEnvoi(), 0, 16)) ?></span>
            </div>
            <h6 class="mb-2"><a href="<?= url('consultation-show', ['id' => $k->getConsultationId()]) ?>" class="text-decoration-none"><?= e($k->getConsultationSujet()) ?></a></h6>
            <p style="white-space: pre-line;"><?= e($k->getContenu()) ?></p>
            <div>
                <form method="post" action="<?= url('conseil-validate') ?>" class="d-inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= $k->getId() ?>">
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Valider</button>
                </form>
                <form method="post" action="<?= url('conseil-delete') ?>" class="d-inline js-confirm" data-confirm="Rejeter et supprimer ce conseil ?">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= $k->getId() ?>">
                    <button type="submit" class="btn btn-outline-danger"><i class="bi bi-x-lg"></i> Rejeter</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</section>
