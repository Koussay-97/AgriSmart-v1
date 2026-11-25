<?php
$editing = $parcelle !== null;
$pageTitle = $editing ? 'Modifier la parcelle' : 'Nouvelle parcelle';
$currentPage = 'exploitations';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once __DIR__ . '/includes/head.php'; ?>
</head>
<body>
<?php require_once __DIR__ . '/includes/spinner.php'; ?>
<?php require_once __DIR__ . '/includes/navbar.php'; ?>
<?php $breadcrumb = [['Mes exploitations', url('exploitations')], [$exploitation->getNom(), url('parcelles', ['exploitation' => $exploitation->getId()])], [$pageTitle, null]]; ?>
<?php require_once __DIR__ . '/includes/page-header.php'; ?>

<section class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="bg-light p-4 p-md-5">
                <p class="text-muted mb-4 fs-5">Exploitation : <?= e($exploitation->getNom()) ?></p>
                <form method="post" action="<?= url('parcelle-form', ['exploitation' => $exploitation->getId()] + ($editing ? ['id' => $parcelle->getId()] : [])) ?>" novalidate data-validate>
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom de la parcelle</label>
                        <input type="text" id="nom" name="nom" data-rules="required|min:3|max:100" value="<?= e($_POST['nom'] ?? ($editing ? $parcelle->getNom() : '')) ?>" class="form-control<?= isset($errors['nom']) ? ' is-invalid' : '' ?>">
                        <div class="invalid-feedback"><?= e($errors['nom'] ?? '') ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="surface" class="form-label">Surface (hectares)</label>
                        <input type="text" id="surface" name="surface" data-rules="required|decimal" value="<?= e($_POST['surface'] ?? ($editing ? $parcelle->getSurface() : '')) ?>" class="form-control<?= isset($errors['surface']) ? ' is-invalid' : '' ?>">
                        <div class="invalid-feedback"><?= e($errors['surface'] ?? '') ?></div>
                    </div>
                    <div class="mb-4">
                        <label for="typeSol" class="form-label">Type de sol</label>
                        <?php $selected = $_POST['typeSol'] ?? ($editing ? $parcelle->getTypeSol() : ''); ?>
                        <select id="typeSol" name="typeSol" data-rules="required" class="form-select<?= isset($errors['typeSol']) ? ' is-invalid' : '' ?>">
                            <option value="">Choisir...</option>
                            <?php foreach ($sols as $s): ?>
                                <option value="<?= e($s) ?>"<?= $selected === $s ? ' selected' : '' ?>><?= e($s) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback"><?= e($errors['typeSol'] ?? '') ?></div>
                    </div>
                    <button type="submit" class="btn btn-primary px-5">Enregistrer</button>
                    <a href="<?= url('parcelles', ['exploitation' => $exploitation->getId()]) ?>" class="btn btn-link">Annuler</a>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<?php require_once __DIR__ . '/includes/scripts.php'; ?>
</body>
</html>
