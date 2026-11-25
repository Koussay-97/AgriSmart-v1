<?php
$pageTitle = 'Inscription';
$currentPage = 'register';
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

<?php $regions = Agriculteur::REGIONS; ?>
<section class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="bg-light p-4 p-md-5">
                <p class="text-center text-muted mb-4 fs-5">Rejoignez les agriculteurs qui cultivent mieux</p>

                <form method="post" action="<?= url('register') ?>" novalidate data-validate>
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nom" class="form-label">Nom complet</label>
                            <input type="text" id="nom" name="nom" data-rules="required|min:3|max:100" value="<?= e($old['nom']) ?>" class="form-control<?= isset($errors['nom']) ? ' is-invalid' : '' ?>" required>
                            <div class="invalid-feedback"><?= e($errors['nom'] ?? '') ?></div>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" data-rules="required|email" value="<?= e($old['email']) ?>" class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>" required>
                            <div class="invalid-feedback"><?= e($errors['email'] ?? '') ?></div>
                        </div>
                        <div class="col-md-6">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <input type="tel" id="telephone" name="telephone" data-rules="required|phone" value="<?= e($old['telephone']) ?>" class="form-control<?= isset($errors['telephone']) ? ' is-invalid' : '' ?>" required>
                            <div class="invalid-feedback"><?= e($errors['telephone'] ?? '') ?></div>
                        </div>
                        <div class="col-md-6">
                            <label for="region" class="form-label">Région</label>
                            <select id="region" name="region" data-rules="required" class="form-select<?= isset($errors['region']) ? ' is-invalid' : '' ?>" required>
                                <option value="">Choisir...</option>
                                <?php foreach ($regions as $r): ?>
                                    <option value="<?= e($r) ?>"<?= $old['region'] === $r ? ' selected' : '' ?>><?= e($r) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback"><?= e($errors['region'] ?? '') ?></div>
                        </div>
                        <div class="col-12">
                            <label for="adresse" class="form-label">Adresse <span class="text-muted">(optionnel)</span></label>
                            <input type="text" id="adresse" name="adresse" data-rules="max:255" value="<?= e($old['adresse']) ?>" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" id="password" name="password" data-rules="required|password" class="form-control<?= isset($errors['password']) ? ' is-invalid' : '' ?>" required>
                            <div class="invalid-feedback"><?= e($errors['password'] ?? '') ?></div>
                        </div>
                        <div class="col-md-6">
                            <label for="confirm" class="form-label">Confirmer le mot de passe</label>
                            <input type="password" id="confirm" name="confirm" data-rules="required|match:#password" class="form-control<?= isset($errors['confirm']) ? ' is-invalid' : '' ?>" required>
                            <div class="invalid-feedback"><?= e($errors['confirm'] ?? '') ?></div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 mt-4">S'inscrire</button>
                </form>
                <p class="text-center mt-4 mb-0">Déjà inscrit ? <a href="<?= url('login') ?>">Se connecter</a></p>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<?php require_once __DIR__ . '/includes/scripts.php'; ?>
</body>
</html>
