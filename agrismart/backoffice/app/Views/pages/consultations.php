<?php
/** pages/consultations.php : liste globale. Variables : $consultations, $experts, $statut, $expertId */
?>
<section class="page-content">
    <h2 class="text-primary mb-1">Consultations</h2>
    <nav aria-label="breadcrumb"><ol class="breadcrumb">
        <li class="breadcrumb-item">Back Office</li>
        <li class="breadcrumb-item active" aria-current="page">Consultations</li>
    </ol></nav>

    <div class="card p-3 mb-4">
        <form method="get" action="index.php" class="row g-2">
            <input type="hidden" name="page" value="consultations">
            <div class="col-md-4">
                <select name="statut" class="form-select" aria-label="Statut">
                    <option value="">Tous les statuts</option>
                    <?php foreach (Consultation::LIBELLES as $k => $label): ?>
                        <option value="<?= e($k) ?>"<?= $statut === $k ? ' selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-5">
                <select name="expert" class="form-select" aria-label="Expert">
                    <option value="0">Tous les experts</option>
                    <?php foreach ($experts as $x): ?>
                        <option value="<?= $x->getId() ?>"<?= $expertId === $x->getId() ? ' selected' : '' ?>><?= e($x->getNom()) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-grid"><button class="btn btn-outline-primary" type="submit"><i class="bi bi-funnel"></i> Filtrer</button></div>
        </form>
    </div>

    <div class="card p-3">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>#</th><th>Sujet</th><th>Agriculteur</th><th>Expert</th><th>Date</th><th>Conseils</th><th>Statut</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                <?php if (!$consultations): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">Aucune consultation.</td></tr>
                <?php endif; ?>
                <?php foreach ($consultations as $c): ?>
                    <tr>
                        <td><?= $c->getId() ?></td>
                        <td class="fw-semibold"><?= e($c->getSujet()) ?></td>
                        <td><?= e($c->getAgriculteurNom()) ?></td>
                        <td><?= e($c->getExpertNom() ?: '—') ?></td>
                        <td><?= e(substr($c->getDateDemande(), 0, 10)) ?></td>
                        <td><?= $c->getNbConseils() ?></td>
                        <td><span class="badge text-bg-<?= e($c->getCouleur()) ?>"><?= e($c->getLibelle()) ?></span></td>
                        <td class="text-end text-nowrap">
                            <a href="<?= url('consultation-show', ['id' => $c->getId()]) ?>" class="btn btn-sm btn-outline-primary rounded-pill" title="Consulter"><i class="bi bi-eye"></i></a>
                            <form method="post" action="<?= url('consultation-delete') ?>" class="d-inline js-confirm" data-confirm="Supprimer cette consultation et ses conseils ?">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= $c->getId() ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" title="Supprimer"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
