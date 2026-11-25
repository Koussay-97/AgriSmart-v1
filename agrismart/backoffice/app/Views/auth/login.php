<?php
/** auth/login.php : connexion administrateur (page autonome). Variables : $errors, $email */
require_once __DIR__ . '/../partials/head.php';
?>
<main class="d-flex align-items-center justify-content-center" style="min-height:100vh; background:#F4F1E8;">
    <div class="card p-4 p-md-5" style="width:100%; max-width:440px;">
        <h2 class="text-primary text-center mb-1"><i class="bi bi-flower2 text-warning"></i> Agri<span class="text-dark">Smart</span></h2>
        <p class="text-center text-muted mb-4">Back Office · Accès administrateur</p>

        <?php if ($f = flash()): ?>
            <div class="alert alert-<?= e($f['type']) ?>"><?= e($f['msg']) ?></div>
        <?php endif; ?>
        <?php if (!empty($errors['global'])): ?>
            <div class="alert alert-danger"><?= e($errors['global']) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= url('login') ?>" novalidate data-validate>
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" data-rules="required|email" value="<?= e($email) ?>" autocomplete="username" class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>">
                <div class="invalid-feedback"><?= e($errors['email'] ?? '') ?></div>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" id="password" name="password" data-rules="required" autocomplete="current-password" class="form-control<?= isset($errors['password']) ? ' is-invalid' : '' ?>">
                <div class="invalid-feedback"><?= e($errors['password'] ?? '') ?></div>
            </div>
            <button type="submit" class="btn btn-primary w-100 rounded-pill py-2">Se connecter</button>
        </form>
    </div>
</main>
<?php require_once __DIR__ . '/../partials/scripts.php'; ?>
