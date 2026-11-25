<?php
/** pages/utilisateur-form.php : ajout / modification. Variables : $target (?Utilisateur), $errors, $regions, $admin */
$editing = $target !== null;
$isAgri = $editing && $target instanceof Agriculteur;
$isExpert = $editing && $target instanceof Expert;
$val = function (string $k, string $fromModel) use ($editing) {
    return $_POST[$k] ?? ($editing ? $fromModel : '');
};
$curRole = $_POST['role'] ?? ($editing ? $target->getRole() : Utilisateur::ROLE_AGRICULTEUR);
$curActif = $_SERVER['REQUEST_METHOD'] === 'POST' ? !empty($_POST['actif']) : ($editing ? $target->isActif() : true);
$isSelf = $editing && $target->getId() === $admin->getId();
?>
<section class="page-content">
    <h2 class="text-primary mb-1"><?= $editing ? 'Modifier un utilisateur' : 'Ajouter un utilisateur' ?></h2>
    <nav aria-label="breadcrumb"><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= url('utilisateurs') ?>">Utilisateurs</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= $editing ? e($target->getNom()) : 'Nouveau' ?></li>
    </ol></nav>

    <div class="card p-4" style="max-width: 820px;">
        <?php if (!empty($errors['global'])): ?>
            <div class="alert alert-danger"><?= e($errors['global']) ?></div>
        <?php endif; ?>
        <form method="post" action="<?= url('utilisateur-form', $editing ? ['id' => $target->getId()] : []) ?>" novalidate data-validate>
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="nom" class="form-label">Nom complet</label>
                    <input type="text" id="nom" name="nom" data-rules="required|min:3|max:100" value="<?= e($val('nom', $editing ? $target->getNom() : '')) ?>" class="form-control<?= isset($errors['nom']) ? ' is-invalid' : '' ?>">
                    <div class="invalid-feedback"><?= e($errors['nom'] ?? '') ?></div>
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" data-rules="required|email" value="<?= e($val('email', $editing ? $target->getEmail() : '')) ?>" class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>">
                    <div class="invalid-feedback"><?= e($errors['email'] ?? '') ?></div>
                </div>
                <div class="col-md-6">
                    <label for="role" class="form-label">Rôle</label>
                    <select id="role" name="role" data-rules="required" class="form-select<?= isset($errors['role']) ? ' is-invalid' : '' ?>"<?= $isSelf ? ' disabled' : '' ?>>
                        <?php foreach (Utilisateur::ROLES as $r): ?>
                            <option value="<?= e($r) ?>"<?= $curRole === $r ? ' selected' : '' ?>><?= e(ucfirst($r)) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($isSelf): ?>
                        <input type="hidden" name="role" value="<?= e($target->getRole()) ?>">
                        <div class="form-text">Vous ne pouvez pas modifier votre propre rôle.</div>
                    <?php endif; ?>
                    <div class="invalid-feedback"><?= e($errors['role'] ?? '') ?></div>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" role="switch" id="actif" name="actif" value="1"<?= $curActif ? ' checked' : '' ?><?= $isSelf ? ' disabled' : '' ?>>
                        <label class="form-check-label" for="actif">Compte actif</label>
                        <?php if ($isSelf): ?><input type="hidden" name="actif" value="1"><?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="telephone" class="form-label">Téléphone <span class="text-muted">(obligatoire pour un agriculteur)</span></label>
                    <input type="text" id="telephone" name="telephone" data-rules="reqif:#role=agriculteur|phone" value="<?= e($val('telephone', $isAgri ? $target->getTelephone() : '')) ?>" class="form-control<?= isset($errors['telephone']) ? ' is-invalid' : '' ?>">
                    <div class="invalid-feedback"><?= e($errors['telephone'] ?? '') ?></div>
                </div>
                <div class="col-md-6">
                    <label for="region" class="form-label">Région <span class="text-muted">(obligatoire pour un agriculteur)</span></label>
                    <?php $curRegion = $val('region', $isAgri ? $target->getRegion() : ''); ?>
                    <select id="region" name="region" data-rules="reqif:#role=agriculteur" class="form-select<?= isset($errors['region']) ? ' is-invalid' : '' ?>">
                        <option value="">Choisir...</option>
                        <?php foreach ($regions as $r): ?>
                            <option value="<?= e($r) ?>"<?= $curRegion === $r ? ' selected' : '' ?>><?= e($r) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback"><?= e($errors['region'] ?? '') ?></div>
                </div>
                <div class="col-md-6">
                    <label for="specialite" class="form-label">Spécialité <span class="text-muted">(obligatoire pour un expert)</span></label>
                    <input type="text" id="specialite" name="specialite" data-rules="reqif:#role=expert|min:3|max:100" value="<?= e($val('specialite', $isExpert ? $target->getSpecialite() : '')) ?>" class="form-control<?= isset($errors['specialite']) ? ' is-invalid' : '' ?>">
                    <div class="invalid-feedback"><?= e($errors['specialite'] ?? '') ?></div>
                </div>
                <div class="col-md-3">
                    <label for="experience" class="form-label">Expérience (ans)</label>
                    <input type="text" id="experience" name="experience" data-rules="reqif:#role=expert|intrange:0-60" value="<?= e($val('experience', $isExpert ? (string) $target->getExperience() : '')) ?>" class="form-control<?= isset($errors['experience']) ? ' is-invalid' : '' ?>">
                    <div class="invalid-feedback"><?= e($errors['experience'] ?? '') ?></div>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check mb-2">
                        <?php $curDispo = $_SERVER['REQUEST_METHOD'] === 'POST' ? !empty($_POST['disponibilite']) : ($isExpert ? $target->isDisponible() : true); ?>
                        <input class="form-check-input" type="checkbox" id="disponibilite" name="disponibilite" value="1"<?= $curDispo ? ' checked' : '' ?>>
                        <label class="form-check-label" for="disponibilite">Disponible</label>
                    </div>
                </div>
                <div class="col-12">
                    <label for="adresse" class="form-label">Adresse <span class="text-muted">(optionnel)</span></label>
                    <input type="text" id="adresse" name="adresse" data-rules="max:255" value="<?= e($val('adresse', $isAgri ? $target->getAdresse() : '')) ?>" class="form-control<?= isset($errors['adresse']) ? ' is-invalid' : '' ?>">
                    <div class="invalid-feedback"><?= e($errors['adresse'] ?? '') ?></div>
                </div>
                <div class="col-md-6">
                    <label for="password" class="form-label">Mot de passe <?= $editing ? '<span class="text-muted">(laisser vide pour ne pas changer)</span>' : '' ?></label>
                    <input type="password" id="password" name="password" data-rules="<?= $editing ? 'password' : 'required|password' ?>" autocomplete="new-password" class="form-control<?= isset($errors['password']) ? ' is-invalid' : '' ?>">
                    <div class="invalid-feedback"><?= e($errors['password'] ?? '') ?></div>
                </div>
                <div class="col-md-6">
                    <label for="confirm" class="form-label">Confirmer le mot de passe</label>
                    <input type="password" id="confirm" name="confirm" data-rules="<?= $editing ? 'match:#password' : 'required|match:#password' ?>" autocomplete="new-password" class="form-control<?= isset($errors['confirm']) ? ' is-invalid' : '' ?>">
                    <div class="invalid-feedback"><?= e($errors['confirm'] ?? '') ?></div>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary rounded-pill px-5">Enregistrer</button>
                <a href="<?= url('utilisateurs') ?>" class="btn btn-link">Annuler</a>
            </div>
        </form>
    </div>
</section>
