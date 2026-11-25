<?php
/** pages/utilisateurs.php : liste + recherche + filtres + pagination. Variables : $result, $filters, $regions, $admin */
$users = $result['users'];
$qs = array_filter($filters, fn($v) => $v !== '');
?>
<section class="page-content">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-primary mb-0">Gestion des utilisateurs</h2>
            <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">Back Office</li>
                <li class="breadcrumb-item active" aria-current="page">Utilisateurs</li>
            </ol></nav>
        </div>
        <a href="<?= url('utilisateur-form') ?>" class="btn btn-primary rounded-pill"><i class="bi bi-plus-lg"></i> Ajouter un utilisateur</a>
    </div>

    <div class="card p-3 mb-4">
        <form method="get" action="index.php" class="row g-2">
            <input type="hidden" name="page" value="utilisateurs">
            <div class="col-md-4"><input type="search" name="q" value="<?= e($filters['q']) ?>" class="form-control" placeholder="Nom ou email..."></div>
            <div class="col-md-2">
                <select name="role" class="form-select">
                    <option value="">Tous les rôles</option>
                    <?php foreach (Utilisateur::ROLES as $r): ?>
                        <option value="<?= e($r) ?>"<?= $filters['role'] === $r ? ' selected' : '' ?>><?= e(ucfirst($r)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="region" class="form-select">
                    <option value="">Toutes les régions</option>
                    <?php foreach ($regions as $r): ?>
                        <option value="<?= e($r) ?>"<?= $filters['region'] === $r ? ' selected' : '' ?>><?= e($r) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="actif" class="form-select">
                    <option value="">Tous statuts</option>
                    <option value="1"<?= $filters['actif'] === '1' ? ' selected' : '' ?>>Actifs</option>
                    <option value="0"<?= $filters['actif'] === '0' ? ' selected' : '' ?>>Bloqués</option>
                </select>
            </div>
            <div class="col-md-1 d-grid"><button class="btn btn-outline-primary" type="submit"><i class="bi bi-funnel"></i></button></div>
        </form>
    </div>

    <div class="card p-3">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>#</th><th>Nom</th><th>Email</th><th>Région</th><th>Rôle</th><th>Statut</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                <?php if (!$users): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">Aucun utilisateur trouvé.</td></tr>
                <?php endif; ?>
                <?php foreach ($users as $u): $isMe = $u->getId() === $admin->getId(); ?>
                    <tr>
                        <td><?= $u->getId() ?></td>
                        <td class="fw-semibold"><?= e($u->getNom()) ?><?= $isMe ? ' <span class="badge text-bg-light border">vous</span>' : '' ?></td>
                        <td><?= e($u->getEmail()) ?></td>
                        <td><?= $u instanceof Agriculteur && $u->getRegion() !== '' ? '<span class="badge text-bg-light border">' . e($u->getRegion()) . '</span>' : '—' ?></td>
                        <td>
                            <?php if ($isMe): ?>
                                <span class="badge text-bg-danger">administrateur</span>
                            <?php else: ?>
                                <form method="post" action="<?= url('utilisateur-role') ?>" class="m-0">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= $u->getId() ?>">
                                    <select name="role" class="form-select form-select-sm" style="min-width:140px" aria-label="Rôle de <?= e($u->getNom()) ?>" data-autosubmit>
                                        <?php foreach (Utilisateur::ROLES as $r): ?>
                                            <option value="<?= e($r) ?>"<?= $u->getRole() === $r ? ' selected' : '' ?>><?= e($r) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge <?= $u->isActif() ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= $u->isActif() ? 'Actif' : 'Bloqué' ?></span></td>
                        <td class="text-end text-nowrap">
                            <a href="<?= url('utilisateur-show', ['id' => $u->getId()]) ?>" class="btn btn-sm btn-outline-primary rounded-pill" title="Consulter"><i class="bi bi-eye"></i></a>
                            <a href="<?= url('utilisateur-form', ['id' => $u->getId()]) ?>" class="btn btn-sm btn-outline-secondary rounded-pill" title="Modifier"><i class="bi bi-pencil"></i></a>
                            <?php if (!$isMe): ?>
                                <form method="post" action="<?= url('utilisateur-toggle') ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= $u->getId() ?>">
                                    <input type="hidden" name="actif" value="<?= $u->isActif() ? '0' : '1' ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill" title="<?= $u->isActif() ? 'Bloquer' : 'Activer' ?>"><i class="bi <?= $u->isActif() ? 'bi-slash-circle' : 'bi-check-circle' ?>"></i></button>
                                </form>
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" title="Supprimer" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                        data-id="<?= $u->getId() ?>" data-nom="<?= e($u->getNom()) ?>"><i class="bi bi-trash"></i></button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small"><?= (int) $result['total'] ?> utilisateur(s) · page <?= (int) $result['page'] ?>/<?= (int) $result['pages'] ?></div>
            <?php if ($result['pages'] > 1): ?>
                <nav aria-label="Pagination"><ul class="pagination pagination-sm mb-0">
                    <?php for ($i = 1; $i <= $result['pages']; $i++): ?>
                        <li class="page-item<?= $i === $result['page'] ? ' active' : '' ?>"><a class="page-link" href="<?= e(url('utilisateurs', $qs + ['p' => $i])) ?>"><?= $i ?></a></li>
                    <?php endfor; ?>
                </ul></nav>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Modale de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" action="<?= url('utilisateur-delete') ?>" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header"><h5 class="modal-title">Confirmer la suppression</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button></div>
            <div class="modal-body">Supprimer l'utilisateur <strong id="deleteName"></strong> ? Ses exploitations et parcelles seront aussi supprimées. Cette action est irréversible.</div>
            <div class="modal-footer">
                <input type="hidden" name="id" id="deleteId">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-danger">Supprimer</button>
            </div>
        </form>
    </div>
</div>
