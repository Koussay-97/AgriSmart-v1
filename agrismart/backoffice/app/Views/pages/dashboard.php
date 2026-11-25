<!-- pages/dashboard.php : tableau de bord administrateur. Variables : $stats, $recent, $byRegion -->
<section class="page-content">
    <h2 class="text-primary mb-1">Tableau de bord</h2>
    <nav aria-label="breadcrumb"><ol class="breadcrumb">
        <li class="breadcrumb-item">Back Office</li>
        <li class="breadcrumb-item active" aria-current="page">Tableau de bord</li>
    </ol></nav>

    <div class="row g-3 mb-4">
        <?php
        $cards = [
            ['Utilisateurs',   $stats['total'],         'bi-people',      'text-primary'],
            ['Agriculteurs',   $stats['agriculteurs'],  'bi-person',      'text-success'],
            ['Experts',         $stats['experts'],      'bi-person-badge', 'text-info'],
            ['Administrateurs', $stats['admins'],       'bi-shield-lock', 'text-danger'],
            ['Consultations ouvertes', $stats['consultations_ouvertes'], 'bi-chat-dots', 'text-warning'],
            ['Conseils à valider', $stats['conseils_a_valider'], 'bi-patch-check', 'text-warning'],
            ['Comptes bloqués', $stats['bloques'],      'bi-slash-circle', 'text-secondary'],
            ['Exploitations',  $stats['exploitations'], 'bi-house-door',  'text-primary'],
            ['Parcelles',      $stats['parcelles'],     'bi-grid-3x3-gap', 'text-primary'],
            ['Surface totale', number_format($stats['surface'], 1, ',', ' ') . ' ha', 'bi-rulers', 'text-primary'],
        ];
        foreach ($cards as [$label, $value, $icon, $cls]): ?>
            <div class="col-6 col-lg-3">
                <div class="card p-3 h-100">
                    <div class="d-flex align-items-center">
                        <i class="bi <?= e($icon) ?> fs-2 me-3 <?= e($cls) ?>"></i>
                        <div><div class="fs-4 fw-bold"><?= e($value) ?></div><div class="text-muted small"><?= e($label) ?></div></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Dernières inscriptions</h5>
                    <a href="<?= url('utilisateurs') ?>" class="btn btn-sm btn-outline-primary rounded-pill">Tout voir</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Nom</th><th>Email</th><th>Rôle</th><th>Inscrit le</th></tr></thead>
                        <tbody>
                        <?php foreach ($recent as $u): ?>
                            <tr>
                                <td class="fw-semibold"><a href="<?= url('utilisateur-show', ['id' => $u->getId()]) ?>" class="text-decoration-none"><?= e($u->getNom()) ?></a></td>
                                <td><?= e($u->getEmail()) ?></td>
                                <td><span class="badge <?= $u->getRole() === 'administrateur' ? 'text-bg-danger' : ($u->getRole() === 'expert' ? 'text-bg-info' : 'text-bg-success') ?>"><?= e($u->getRole()) ?></span></td>
                                <td><?= e($u->getCreatedAt()) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card p-3 h-100">
                <h5 class="mb-3">Agriculteurs par région</h5>
                <?php if (!$byRegion): ?>
                    <p class="text-muted mb-0">Aucune donnée.</p>
                <?php else: $max = max($byRegion); ?>
                    <?php foreach ($byRegion as $region => $n): ?>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between small"><span><?= e($region) ?></span><strong><?= (int) $n ?></strong></div>
                            <div class="progress" style="height:8px;"><div class="progress-bar bg-success" style="width: <?= (int) round($n / $max * 100) ?>%"></div></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
