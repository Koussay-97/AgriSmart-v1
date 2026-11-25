<?php
$editing = $irrigation !== null;
$pageTitle = $editing ? "Modifier l'irrigation" : 'Nouvelle irrigation';
$currentPage = 'irrigations';
/** Attendu : $irrigation (?Irrigation), $cultures (lignes SQL), $errors, $preselect (int) */
$selCulture = (int) ($_POST['culture_id'] ?? ($editing ? $irrigation->getCultureId() : $preselect));
$selMethode = $_POST['methode'] ?? ($editing ? $irrigation->getMethode() : '');
$selStatut = $_POST['statut'] ?? ($editing ? $irrigation->getStatut() : Irrigation::STATUT_PLANIFIEE);
$defDate = $_POST['date_irrigation'] ?? ($editing ? $irrigation->getDate() : date('Y-m-d'));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once __DIR__ . '/includes/head.php'; ?>
</head>
<body>
<?php require_once __DIR__ . '/includes/spinner.php'; ?>
<?php require_once __DIR__ . '/includes/navbar.php'; ?>
<?php $breadcrumb = [['Irrigation', url('irrigations')], [$pageTitle, null]]; ?>
<?php require_once __DIR__ . '/includes/page-header.php'; ?>

<section class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="bg-light p-4 p-md-5">
                <form method="post" action="<?= url('irrigation-form', $editing ? ['id' => $irrigation->getId()] : []) ?>" novalidate data-validate>
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="culture_id" class="form-label">Culture</label>
                        <select id="culture_id" name="culture_id" data-rules="required" class="form-select<?= isset($errors['culture_id']) ? ' is-invalid' : '' ?>">
                            <option value="">Choisir...</option>
                            <?php foreach ($cultures as $c): ?>
                                <option value="<?= (int) $c['id'] ?>"<?= $selCulture === (int) $c['id'] ? ' selected' : '' ?>><?= e($c['nom'] . ' (' . $c['parcelle_nom'] . ')') ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback"><?= e($errors['culture_id'] ?? '') ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="date_irrigation" class="form-label">Date</label>
                            <input type="date" id="date_irrigation" name="date_irrigation" data-rules="required|date" value="<?= e($defDate) ?>" class="form-control<?= isset($errors['date_irrigation']) ? ' is-invalid' : '' ?>">
                            <div class="invalid-feedback"><?= e($errors['date_irrigation'] ?? '') ?></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="statut" class="form-label">Statut</label>
                            <select id="statut" name="statut" data-rules="required" class="form-select<?= isset($errors['statut']) ? ' is-invalid' : '' ?>">
                                <?php foreach (Irrigation::LIBELLES as $key => $label): ?>
                                    <option value="<?= e($key) ?>"<?= $selStatut === $key ? ' selected' : '' ?>><?= e($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback"><?= e($errors['statut'] ?? '') ?></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="quantite_eau" class="form-label">Quantité d'eau (litres)</label>
                            <input type="text" id="quantite_eau" name="quantite_eau" data-rules="required|decimal" value="<?= e($_POST['quantite_eau'] ?? ($editing ? $irrigation->getQuantiteEau() : '')) ?>" class="form-control<?= isset($errors['quantite_eau']) ? ' is-invalid' : '' ?>">
                            <div class="invalid-feedback"><?= e($errors['quantite_eau'] ?? '') ?></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="duree" class="form-label">Durée (minutes)</label>
                            <input type="text" id="duree" name="duree" data-rules="required|intrange:1-1440" value="<?= e($_POST['duree'] ?? ($editing ? $irrigation->getDuree() : '')) ?>" class="form-control<?= isset($errors['duree']) ? ' is-invalid' : '' ?>">
                            <div class="invalid-feedback"><?= e($errors['duree'] ?? '') ?></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="methode" class="form-label">Méthode</label>
                        <select id="methode" name="methode" data-rules="required" class="form-select<?= isset($errors['methode']) ? ' is-invalid' : '' ?>">
                            <option value="">Choisir...</option>
                            <?php foreach (Irrigation::METHODES as $m): ?>
                                <option value="<?= e($m) ?>"<?= $selMethode === $m ? ' selected' : '' ?>><?= e($m) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback"><?= e($errors['methode'] ?? '') ?></div>
                    </div>
                    <button type="submit" class="btn btn-primary px-5">Enregistrer</button>
                    <a href="<?= url('irrigations') ?>" class="btn btn-link">Annuler</a>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<?php require_once __DIR__ . '/includes/scripts.php'; ?>
</body>
</html>
