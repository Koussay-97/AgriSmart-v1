<?php
/** pages/profil.php : profil de l'administrateur connecté. Variables : $user, $errors, $pwdErrors */
?>
<section class="page-content">
    <h2 class="text-primary mb-1">Mon profil</h2>
    <nav aria-label="breadcrumb"><ol class="breadcrumb">
        <li class="breadcrumb-item">Back Office</li>
        <li class="breadcrumb-item active" aria-current="page">Mon profil</li>
    </ol></nav>

    <div class="card p-4 mb-4" style="max-width: 760px;">
        <h5 class="mb-3">Informations</h5>
        <form method="post" action="<?= url('profil') ?>" novalidate data-validate>
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="nom" class="form-label">Nom complet</label>
                    <input type="text" id="nom" name="nom" data-rules="required|min:3|max:100" value="<?= e($_POST['nom'] ?? $user->getNom()) ?>" class="form-control<?= isset($errors['nom']) ? ' is-invalid' : '' ?>">
                    <div class="invalid-feedback"><?= e($errors['nom'] ?? '') ?></div>
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" data-rules="required|email" value="<?= e($_POST['email'] ?? $user->getEmail()) ?>" class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>">
                    <div class="invalid-feedback"><?= e($errors['email'] ?? '') ?></div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary rounded-pill px-5 mt-4">Enregistrer</button>
        </form>
    </div>

    <div class="card p-4" style="max-width: 760px;">
        <h5 class="mb-3">Changer mon mot de passe</h5>
        <form method="post" action="<?= url('profil-password') ?>" novalidate data-validate>
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-12">
                    <label for="current_password" class="form-label">Mot de passe actuel</label>
                    <input type="password" id="current_password" name="current_password" data-rules="required" autocomplete="current-password" class="form-control<?= isset($pwdErrors['current_password']) ? ' is-invalid' : '' ?>">
                    <div class="invalid-feedback"><?= e($pwdErrors['current_password'] ?? '') ?></div>
                </div>
                <div class="col-md-6">
                    <label for="new_password" class="form-label">Nouveau mot de passe</label>
                    <input type="password" id="new_password" name="password" data-rules="required|password" autocomplete="new-password" class="form-control<?= isset($pwdErrors['password']) ? ' is-invalid' : '' ?>">
                    <div class="invalid-feedback"><?= e($pwdErrors['password'] ?? '') ?></div>
                </div>
                <div class="col-md-6">
                    <label for="new_confirm" class="form-label">Confirmer</label>
                    <input type="password" id="new_confirm" name="confirm" data-rules="required|match:#new_password" autocomplete="new-password" class="form-control<?= isset($pwdErrors['confirm']) ? ' is-invalid' : '' ?>">
                    <div class="invalid-feedback"><?= e($pwdErrors['confirm'] ?? '') ?></div>
                </div>
            </div>
            <button type="submit" class="btn btn-outline-primary rounded-pill px-5 mt-4">Modifier le mot de passe</button>
        </form>
    </div>
</section>
