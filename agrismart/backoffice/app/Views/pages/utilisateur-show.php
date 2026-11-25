<?php
/** pages/utilisateur-show.php : consultation. Variables : $target (Utilisateur), $exploitations (array de lignes) */
$isAgri = $target instanceof Agriculteur;
?>
<section class="page-content">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h2 class="text-primary mb-0"><?= e($target->getNom()) ?></h2>
            <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= url('utilisateurs') ?>">Utilisateurs</a></li>
                <li class="breadcrumb-item active" aria-current="page">Détail</li>
            </ol></nav>
        </div>
        <a href="<?= url('utilisateur-form', ['id' => $target->getId()]) ?>" class="btn btn-primary rounded-pill"><i class="bi bi-pencil"></i> Modifier</a>
    </div>

    <div class="card p-4 mb-4" style="max-width: 820px;">
        <table class="table mb-0">
            <tr><th style="width:220px">ID</th><td><?= $target->getId() ?></td></tr>
            <tr><th>Nom</th><td><?= e($target->getNom()) ?></td></tr>
            <tr><th>Email</th><td><?= e($target->getEmail()) ?></td></tr>
            <tr><th>Rôle</th><td><span class="badge <?= $isAgri ? 'text-bg-success' : ($target instanceof Expert ? 'text-bg-info' : 'text-bg-danger') ?>"><?= e($target->getRole()) ?></span></td></tr>
            <tr><th>Statut</th><td><span class="badge <?= $target->isActif() ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= $target->isActif() ? 'Actif' : 'Bloqué' ?></span></td></tr>
            <?php if ($isAgri): ?>
                <tr><th>Téléphone</th><td><?= e($target->getTelephone()) ?: '—' ?></td></tr>
                <tr><th>Région</th><td><?= e($target->getRegion()) ?: '—' ?></td></tr>
                <tr><th>Adresse</th><td><?= e($target->getAdresse()) ?: '—' ?></td></tr>
            <?php endif; ?>
            <?php if ($target instanceof Expert): ?>
                <tr><th>Spécialité</th><td><?= e($target->getSpecialite()) ?></td></tr>
                <tr><th>Expérience</th><td><?= $target->getExperience() ?> an(s)</td></tr>
                <tr><th>Disponibilité</th><td><?= $target->isDisponible() ? 'Disponible' : 'Indisponible' ?></td></tr>
            <?php endif; ?>
            <tr><th>Inscrit le</th><td><?= e($target->getCreatedAt()) ?></td></tr>
            <tr><th>Dernière connexion</th><td><?= e($target->getDerniereConnexion() ?? 'Jamais') ?></td></tr>
        </table>
    </div>

    <?php if ($isAgri): ?>
        <div class="card p-4" style="max-width: 820px;">
            <h5 class="mb-3">Exploitations (<?= count($exploitations) ?>)</h5>
            <?php if (!$exploitations): ?>
                <p class="text-muted mb-0">Aucune exploitation.</p>
            <?php else: ?>
                <div class="table-responsive"><table class="table align-middle mb-0">
                    <thead><tr><th>Nom</th><th>Localisation</th><th>Parcelles</th><th>Surface</th></tr></thead>
                    <tbody>
                    <?php foreach ($exploitations as $x): ?>
                        <tr>
                            <td class="fw-semibold"><?= e($x['nom']) ?></td>
                            <td><?= e($x['localisation']) ?></td>
                            <td><?= (int) $x['nb_parcelles'] ?></td>
                            <td><?= number_format((float) $x['surface'], 1, ',', ' ') ?> ha</td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>
