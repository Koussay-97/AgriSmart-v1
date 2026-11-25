<?php
$pageTitle = 'Mon profil';
$currentPage = 'profile';
/** Attendu : $user (Agriculteur), $errors (infos), $pwdErrors (mot de passe), $delErrors (suppression) */
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
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <!-- 1. Informations personnelles -->
            <div class="bg-light p-4 p-md-5 mb-4">
                <h4 class="text-primary mb-4">Mes informations</h4>
                <form method="post" action="<?= url('profile') ?>" novalidate data-validate>
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom complet</label>
                        <input type="text" id="nom" name="nom" data-rules="required|min:3|max:100" value="<?= e($_POST['nom'] ?? $user->getNom()) ?>" class="form-control<?= isset($errors['nom']) ? ' is-invalid' : '' ?>">
                        <div class="invalid-feedback"><?= e($errors['nom'] ?? '') ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" data-rules="required|email" value="<?= e($_POST['email'] ?? $user->getEmail()) ?>" class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>">
                        <div class="invalid-feedback"><?= e($errors['email'] ?? '') ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <input type="text" id="telephone" name="telephone" data-rules="required|phone" value="<?= e($_POST['telephone'] ?? $user->getTelephone()) ?>" class="form-control<?= isset($errors['telephone']) ? ' is-invalid' : '' ?>">
                            <div class="invalid-feedback"><?= e($errors['telephone'] ?? '') ?></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="region" class="form-label">Région</label>
                            <?php $selRegion = $_POST['region'] ?? $user->getRegion(); ?>
                            <select id="region" name="region" data-rules="required" class="form-select<?= isset($errors['region']) ? ' is-invalid' : '' ?>">
                                <option value="">Choisir...</option>
                                <?php foreach (Agriculteur::REGIONS as $r): ?>
                                    <option value="<?= e($r) ?>"<?= $selRegion === $r ? ' selected' : '' ?>><?= e($r) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback"><?= e($errors['region'] ?? '') ?></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="adresse" class="form-label">Adresse <span class="text-muted">(optionnel)</span></label>
                        <input type="text" id="adresse" name="adresse" data-rules="max:255" value="<?= e($_POST['adresse'] ?? $user->getAdresse()) ?>" class="form-control<?= isset($errors['adresse']) ? ' is-invalid' : '' ?>">
                        <div class="invalid-feedback"><?= e($errors['adresse'] ?? '') ?></div>
                    </div>
                    <button type="submit" class="btn btn-primary px-5">Enregistrer</button>
                </form>
            </div>

            <!-- 2. Changer le mot de passe -->
            <div class="bg-light p-4 p-md-5 mb-4">
                <h4 class="text-primary mb-4">Changer mon mot de passe</h4>
                <form method="post" action="<?= url('profile-password') ?>" novalidate data-validate>
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Mot de passe actuel</label>
                        <input type="password" id="current_password" name="current_password" data-rules="required" autocomplete="current-password" class="form-control<?= isset($pwdErrors['current_password']) ? ' is-invalid' : '' ?>">
                        <div class="invalid-feedback"><?= e($pwdErrors['current_password'] ?? '') ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="new_password" class="form-label">Nouveau mot de passe</label>
                            <input type="password" id="new_password" name="password" data-rules="required|password" autocomplete="new-password" class="form-control<?= isset($pwdErrors['password']) ? ' is-invalid' : '' ?>">
                            <div class="invalid-feedback"><?= e($pwdErrors['password'] ?? '') ?></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="new_confirm" class="form-label">Confirmer</label>
                            <input type="password" id="new_confirm" name="confirm" data-rules="required|match:#new_password" autocomplete="new-password" class="form-control<?= isset($pwdErrors['confirm']) ? ' is-invalid' : '' ?>">
                            <div class="invalid-feedback"><?= e($pwdErrors['confirm'] ?? '') ?></div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-outline-primary px-5">Modifier le mot de passe</button>
                </form>
            </div>

            <!-- 3. Supprimer mon compte -->
            <div class="bg-light p-4 p-md-5 border border-danger">
                <h4 class="text-danger mb-2">Supprimer mon compte</h4>
                <p class="text-muted">Action définitive : votre compte, vos exploitations et vos parcelles seront supprimés. Confirmez avec votre mot de passe.</p>
                <form method="post" action="<?= url('profile-delete') ?>" class="js-confirm" data-confirm="Supprimer définitivement votre compte et toutes vos données ?" novalidate data-validate>
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="delete_password" class="form-label">Mot de passe</label>
                        <input type="password" id="delete_password" name="password" data-rules="required" autocomplete="current-password" class="form-control<?= isset($delErrors['delete_password']) ? ' is-invalid' : '' ?>">
                        <div class="invalid-feedback"><?= e($delErrors['delete_password'] ?? '') ?></div>
                    </div>
                    <button type="submit" class="btn btn-danger px-5">Supprimer mon compte</button>
                </form>
            </div>

        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<?php require_once __DIR__ . '/includes/scripts.php'; ?>
</body>
</html>
