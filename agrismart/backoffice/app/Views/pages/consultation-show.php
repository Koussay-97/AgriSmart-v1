<?php
/** pages/consultation-show.php : détail + affectation + conseils. Variables : $consultation, $conseils, $experts */
$c = $consultation;
?>
<section class="page-content">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h2 class="text-primary mb-0"><?= e($c->getSujet()) ?></h2>
            <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= url('consultations') ?>">Consultations</a></li>
                <li class="breadcrumb-item active" aria-current="page">Détail</li>
            </ol></nav>
        </div>
        <span class="badge text-bg-<?= e($c->getCouleur()) ?> fs-6"><?= e($c->getLibelle()) ?></span>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card p-4 h-100">
                <p style="white-space: pre-line;"><?= e($c->getDescription()) ?></p>
                <table class="table table-sm mb-3">
                    <tr><th style="width:160px">Agriculteur</th><td><?= e($c->getAgriculteurNom()) ?></td></tr>
                    <tr><th>Exploitation</th><td><?= e($c->getExploitationNom() ?: '—') ?></td></tr>
                    <tr><th>Demandée le</th><td><?= e($c->getDateDemande()) ?></td></tr>
                    <tr><th>Expert</th><td><?= e($c->getExpertNom() ?: '—') ?></td></tr>
                </table>
                <?php if ($c->getStatut() !== Consultation::STATUT_CLOTUREE): ?>
                    <form method="post" action="<?= url('consultation-assign') ?>" class="row g-2">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= $c->getId() ?>">
                        <div class="col-8">
                            <select name="expert_id" class="form-select" aria-label="Expert">
                                <option value="0">— Aucun (remettre en « ouverte ») —</option>
                                <?php foreach ($experts as $x): ?>
                                    <option value="<?= $x->getId() ?>"<?= $c->getExpertId() === $x->getId() ? ' selected' : '' ?>><?= e($x->getNom()) ?><?= $x instanceof Expert ? ' · ' . e($x->getSpecialite()) : '' ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-4 d-grid"><button type="submit" class="btn btn-primary">Affecter</button></div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card p-4 h-100">
                <h5 class="mb-3">Conseils (<?= count($conseils) ?>)</h5>
                <?php if (!$conseils): ?><p class="text-muted mb-0">Aucun conseil pour le moment.</p><?php endif; ?>
                <?php foreach ($conseils as $k): ?>
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <strong><?= e($k->getExpertNom() ?: 'Expert supprimé') ?></strong><span><?= e(substr($k->getDateEnvoi(), 0, 10)) ?></span>
                        </div>
                        <p class="mb-2" style="white-space: pre-line;"><?= e($k->getContenu()) ?></p>
                        <?php if ($k->isSensible() && !$k->isValide()): ?>
                            <span class="badge text-bg-warning">Sensible : en attente de validation</span>
                            <form method="post" action="<?= url('conseil-validate') ?>" class="d-inline">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= $k->getId() ?>">
                                <input type="hidden" name="from" value="consultation">
                                <input type="hidden" name="consultation" value="<?= $c->getId() ?>">
                                <button type="submit" class="btn btn-sm btn-success ms-2">Valider</button>
                            </form>
                        <?php elseif ($k->isSensible()): ?>
                            <span class="badge text-bg-success">Sensible : validé</span>
                        <?php else: ?>
                            <span class="badge text-bg-light border">Publié</span>
                        <?php endif; ?>
                        <form method="post" action="<?= url('conseil-delete') ?>" class="d-inline js-confirm" data-confirm="Supprimer ce conseil ?">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= $k->getId() ?>">
                            <input type="hidden" name="from" value="consultation">
                            <input type="hidden" name="consultation" value="<?= $c->getId() ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger ms-1"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
