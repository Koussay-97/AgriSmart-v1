<?php
$editing = $conseil !== null;
$pageTitle = $editing ? 'Modifier le conseil' : 'Donner un conseil';
$currentPage = 'consultations';
/** Attendu : $consultation (Consultation prise en charge), $conseil (?Conseil), $errors */
$sensible = $_SERVER['REQUEST_METHOD'] === 'POST' ? !empty($_POST['sensible']) : ($editing && $conseil->isSensible());
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once __DIR__ . '/includes/head.php'; ?>
</head>
<body>
<?php require_once __DIR__ . '/includes/spinner.php'; ?>
<?php require_once __DIR__ . '/includes/navbar.php'; ?>
<?php $breadcrumb = [['Consultations', url('consultations')], [$consultation->getSujet(), url('consultation', ['id' => $consultation->getId()])], [$pageTitle, null]]; ?>
<?php require_once __DIR__ . '/includes/page-header.php'; ?>

<section class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="bg-light p-4 p-md-5">
                <p class="text-muted">Pour <strong><?= e($consultation->getAgriculteurNom()) ?></strong> · « <?= e($consultation->getSujet()) ?> »</p>
                <form method="post" action="<?= url('conseil-form', $editing ? ['id' => $conseil->getId()] : ['consultation' => $consultation->getId()]) ?>" novalidate data-validate>
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="contenu" class="form-label">Votre conseil</label>
                        <textarea id="contenu" name="contenu" rows="8" data-rules="required|min:10|max:3000" class="form-control<?= isset($errors['contenu']) ? ' is-invalid' : '' ?>"><?= e($_POST['contenu'] ?? ($editing ? $conseil->getContenu() : '')) ?></textarea>
                        <div class="invalid-feedback"><?= e($errors['contenu'] ?? '') ?></div>
                    </div>
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="sensible" name="sensible" value="1"<?= $sensible ? ' checked' : '' ?>>
                        <label class="form-check-label" for="sensible">Conseil sensible (dosage de produits, traitement...)</label>
                        <div class="form-text">Un conseil sensible n'est montré à l'agriculteur qu'après validation par un administrateur.</div>
                    </div>
                    <button type="submit" class="btn btn-primary px-5">Enregistrer</button>
                    <a href="<?= url('consultation', ['id' => $consultation->getId()]) ?>" class="btn btn-link">Annuler</a>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<?php require_once __DIR__ . '/includes/scripts.php'; ?>
</body>
</html>
