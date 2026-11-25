<?php
$editing = $culture !== null;
$pageTitle = $editing ? 'Modifier la culture' : 'Nouvelle culture';
$currentPage = 'cultures';
/** Attendu : $culture (?Culture), $parcelles (lignes SQL), $errors */
$selParcelle = (int) ($_POST['parcelle_id'] ?? ($editing ? $culture->getParcelleId() : (int) ($_GET['parcelle'] ?? 0)));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once __DIR__ . '/includes/head.php'; ?>
</head>
<body>
<?php require_once __DIR__ . '/includes/spinner.php'; ?>
<?php require_once __DIR__ . '/includes/navbar.php'; ?>
<?php $breadcrumb = [['Mes cultures', url('cultures')], [$pageTitle, null]]; ?>
<?php require_once __DIR__ . '/includes/page-header.php'; ?>

<section class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="bg-light p-4 p-md-5">
                <form method="post" action="<?= url('culture-form', $editing ? ['id' => $culture->getId()] : []) ?>" novalidate data-validate>
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="nom" class="form-label">Culture</label>
                        <input type="text" id="nom" name="nom" data-rules="required|min:2|max:100" placeholder="Ex. : Tomates, Blé dur, Oliviers" value="<?= e($_POST['nom'] ?? ($editing ? $culture->getNom() : '')) ?>" class="form-control<?= isset($errors['nom']) ? ' is-invalid' : '' ?>">
                        <div class="invalid-feedback"><?= e($errors['nom'] ?? '') ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="parcelle_id" class="form-label">Parcelle</label>
                        <select id="parcelle_id" name="parcelle_id" data-rules="required" class="form-select<?= isset($errors['parcelle_id']) ? ' is-invalid' : '' ?>">
                            <option value="">Choisir...</option>
                            <?php foreach ($parcelles as $p): ?>
                                <option value="<?= (int) $p['id'] ?>"<?= $selParcelle === (int) $p['id'] ? ' selected' : '' ?>><?= e($p['exploitation_nom'] . ' · ' . $p['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback"><?= e($errors['parcelle_id'] ?? '') ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="date_semis" class="form-label">Date de semis</label>
                            <input type="date" id="date_semis" name="date_semis" data-rules="required|date" value="<?= e($_POST['date_semis'] ?? ($editing ? $culture->getDateSemis() : '')) ?>" class="form-control<?= isset($errors['date_semis']) ? ' is-invalid' : '' ?>">
                            <div class="invalid-feedback"><?= e($errors['date_semis'] ?? '') ?></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="date_recolte" class="form-label">Récolte prévue <span class="text-muted">(optionnel)</span></label>
                            <input type="date" id="date_recolte" name="date_recolte" data-rules="date|afterdate:#date_semis" value="<?= e($_POST['date_recolte'] ?? ($editing ? ($culture->getDateRecolte() ?? '') : '')) ?>" class="form-control<?= isset($errors['date_recolte']) ? ' is-invalid' : '' ?>">
                            <div class="invalid-feedback"><?= e($errors['date_recolte'] ?? '') ?></div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary px-5">Enregistrer</button>
                    <a href="<?= url('cultures') ?>" class="btn btn-link">Annuler</a>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<?php require_once __DIR__ . '/includes/scripts.php'; ?>
</body>
</html>
