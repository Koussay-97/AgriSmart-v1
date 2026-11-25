<?php
$pageTitle = 'Page introuvable';
$currentPage = '';
$breadcrumb = [['404', null]];
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

<section class="container pb-5 text-center">
    <i class="fa fa-exclamation-triangle fa-4x text-primary mb-3"></i>
    <h1 class="display-1">404</h1>
    <h3 class="mb-3">Page introuvable</h3>
    <p class="text-muted mb-4">La page demandée n'existe pas ou n'est pas encore disponible.</p>
    <a href="<?= url('home') ?>" class="btn btn-primary py-3 px-5">Retour à l'accueil</a>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<?php require_once __DIR__ . '/includes/scripts.php'; ?>
</body>
</html>
