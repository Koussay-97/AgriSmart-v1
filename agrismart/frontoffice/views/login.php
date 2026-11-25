<?php
$pageTitle = 'Connexion';
$currentPage = 'login';
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
        <div class="col-md-6 col-lg-5">
            <div class="bg-light p-4 p-md-5">
                <p class="text-center text-muted mb-4 fs-5">Accédez à votre espace agriculteur</p>

                <?php if (!empty($errors['global'])): ?>
                    <div class="alert alert-danger"><?= e($errors['global']) ?></div>
                <?php endif; ?>

                <form method="post" action="<?= url('login') ?>" novalidate data-validate>
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" data-rules="required|email" value="<?= e($email) ?>" class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>" required>
                        <div class="invalid-feedback"><?= e($errors['email'] ?? '') ?></div>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" id="password" name="password" data-rules="required" class="form-control<?= isset($errors['password']) ? ' is-invalid' : '' ?>" required>
                        <div class="invalid-feedback"><?= e($errors['password'] ?? '') ?></div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2">Se connecter</button>
                </form>
                <p class="text-center mt-4 mb-0">Pas encore de compte ? <a href="<?= url('register') ?>">S'inscrire</a></p>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<?php require_once __DIR__ . '/includes/scripts.php'; ?>
</body>
</html>
