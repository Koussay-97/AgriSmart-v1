<?php
/** pages/suivi.php : suivi agricole global. Variables : $stats, $cultures (Culture[]), $proprietaires (id => nom) */
?>
<section class="page-content">
    <h2 class="text-primary mb-1">Suivi agricole</h2>
    <nav aria-label="breadcrumb"><ol class="breadcrumb">
        <li class="breadcrumb-item">Back Office</li>
        <li class="breadcrumb-item active" aria-current="page">Suivi agricole</li>
    </ol></nav>

    <div class="row g-3 mb-4">
        <?php foreach ([
            ['Cultures suivies', $stats['cultures'], 'bi-flower2'],
            ['Irrigations enregistrées', $stats['irrigations'], 'bi-droplet'],
            ['Irrigations planifiées', $stats['planifiees'], 'bi-calendar-check'],
            ['Eau utilisée (réalisée)', number_format($stats['eau'], 0, ',', ' ') . ' L', 'bi-water'],
        ] as [$label, $value, $icon]): ?>
            <div class="col-6 col-lg-3">
                <div class="card p-3 h-100"><div class="d-flex align-items-center">
                    <i class="bi <?= e($icon) ?> fs-2 me-3 text-primary"></i>
                    <div><div class="fs-4 fw-bold"><?= e($value) ?></div><div class="text-muted small"><?= e($label) ?></div></div>
                </div></div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="card p-3">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Culture</th><th>Agriculteur</th><th>Exploitation · Parcelle</th><th>Semis</th><th>Récolte</th><th>Eau utilisée</th></tr></thead>
                <tbody>
                <?php if (!$cultures): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Aucune culture enregistrée.</td></tr>
                <?php endif; ?>
                <?php foreach ($cultures as $c): ?>
                    <tr>
                        <td class="fw-semibold"><?= e($c->getNom()) ?></td>
                        <td><?= e($proprietaires[$c->getId()] ?? '—') ?></td>
                        <td><?= e($c->getExploitationNom()) ?> · <?= e($c->getParcelleNom()) ?></td>
                        <td><?= e($c->getDateSemis()) ?></td>
                        <td><?= e($c->getDateRecolte() ?? '—') ?></td>
                        <td><?= e(number_format($c->getEauTotale(), 0, ',', ' ')) ?> L</td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
