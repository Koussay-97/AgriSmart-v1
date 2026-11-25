<?php
$editing = $consultation !== null;
$pageTitle = $editing ? 'Modifier la consultation' : 'Nouvelle consultation';
$currentPage = 'consultations';
/** Attendu : $consultation (?Consultation), $exploitations (lignes id/nom), $errors */
$selected = (int) ($_POST['exploitation_id'] ?? ($editing ? (int) $consultation->getExploitationId() : 0));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once __DIR__ . '/includes/head.php'; ?>
</head>
<body>
<?php require_once __DIR__ . '/includes/spinner.php'; ?>
<?php require_once __DIR__ . '/includes/navbar.php'; ?>
<?php $breadcrumb = [['Mes consultations', url('consultations')], [$pageTitle, null]]; ?>
<?php require_once __DIR__ . '/includes/page-header.php'; ?>

<section class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="bg-light p-4 p-md-5">
                <form method="post" action="<?= url('consultation-form', $editing ? ['id' => $consultation->getId()] : []) ?>" novalidate data-validate>
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="sujet" class="form-label">Sujet</label>
                        <input type="text" id="sujet" name="sujet" data-rules="required|min:5|max:150" placeholder="Ex. : feuilles de tomates qui jaunissent" value="<?= e($_POST['sujet'] ?? ($editing ? $consultation->getSujet() : '')) ?>" class="form-control<?= isset($errors['sujet']) ? ' is-invalid' : '' ?>">
                        <div class="invalid-feedback"><?= e($errors['sujet'] ?? '') ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="exploitation_id" class="form-label">Exploitation concernée <span class="text-muted">(optionnel)</span></label>
                        <select id="exploitation_id" name="exploitation_id" class="form-select<?= isset($errors['exploitation_id']) ? ' is-invalid' : '' ?>">
                            <option value="0">Aucune en particulier</option>
                            <?php foreach ($exploitations as $x): ?>
                                <option value="<?= (int) $x['id'] ?>"<?= $selected === (int) $x['id'] ? ' selected' : '' ?>><?= e($x['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback"><?= e($errors['exploitation_id'] ?? '') ?></div>
                    </div>
                    <div class="mb-4">
                        <label for="description" class="form-label">Décrivez votre problème</label>
                        <textarea id="description" name="description" rows="7" data-rules="required|min:10|max:2000" class="form-control<?= isset($errors['description']) ? ' is-invalid' : '' ?>"><?= e($_POST['description'] ?? ($editing ? $consultation->getDescription() : '')) ?></textarea>
                        <div class="invalid-feedback"><?= e($errors['description'] ?? '') ?></div>
                        <div class="form-text">Cultures concernées, symptômes, depuis quand, pratiques d'irrigation et d'engrais...</div>
                    </div>
                    <button type="submit" class="btn btn-primary px-5"><?= $editing ? 'Enregistrer' : 'Envoyer à un expert' ?></button>
                    <a href="<?= url('consultations') ?>" class="btn btn-link">Annuler</a>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<?php require_once __DIR__ . '/includes/scripts.php'; ?>
</body>
</html>
