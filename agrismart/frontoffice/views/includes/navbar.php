<?php /* includes/navbar.php : barre de navigation du template.
         Variable attendue : $currentPage (clé de la page courante) -> ajoute .active au bon lien (workshop étape 5). */
$currentPage = $currentPage ?? '';
$flash = flash();
?>
<!-- Navbar Start -->
<nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
    <a href="<?= url('home') ?>" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
        <img src="img/logo.svg" alt="" width="38" height="38" class="me-2">
        <h2 class="m-0 text-primary">Agri<span class="text-dark">Smart</span></h2>
    </a>
    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-label="Menu">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto p-4 p-lg-0">
            <a href="<?= url('home') ?>" class="nav-item nav-link<?= navActive('home', $currentPage) ?>">Accueil</a>
            <?php if (isExpert()): ?>
                <a href="<?= url('consultations') ?>" class="nav-item nav-link<?= navActive('consultations', $currentPage) ?>">Consultations</a>
                <a href="<?= url('profile') ?>" class="nav-item nav-link<?= navActive('profile', $currentPage) ?>">Mon profil</a>
            <?php elseif (isLoggedIn()): ?>
                <a href="<?= url('dashboard') ?>" class="nav-item nav-link<?= navActive('dashboard', $currentPage) ?>">Tableau de bord</a>
                <a href="<?= url('exploitations') ?>" class="nav-item nav-link<?= navActive('exploitations', $currentPage) ?>">Mes exploitations</a>
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle<?= in_array($currentPage, ['cultures', 'irrigations'], true) ? ' active' : '' ?>" data-bs-toggle="dropdown">Suivi agricole</a>
                    <div class="dropdown-menu fade-down m-0">
                        <a href="<?= url('cultures') ?>" class="dropdown-item">Cultures</a>
                        <a href="<?= url('irrigations') ?>" class="dropdown-item">Irrigation</a>
                    </div>
                </div>
                <a href="<?= url('consultations') ?>" class="nav-item nav-link<?= navActive('consultations', $currentPage) ?>">Consultations</a>
                <a href="<?= url('profile') ?>" class="nav-item nav-link<?= navActive('profile', $currentPage) ?>">Mon profil</a>
            <?php else: ?>
                <a href="<?= url('login') ?>" class="nav-item nav-link<?= navActive('login', $currentPage) ?>">Connexion</a>
            <?php endif; ?>
        </div>
        <?php if (isLoggedIn()): ?>
            <form method="post" action="<?= url('logout') ?>" class="d-flex m-0">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-primary rounded-0 py-4 px-lg-5 w-100">Déconnexion<i class="fa fa-sign-out-alt ms-3"></i></button>
            </form>
        <?php else: ?>
            <a href="<?= url('register') ?>" class="btn btn-primary rounded-0 py-4 px-lg-5">Rejoindre<i class="fa fa-arrow-right ms-3"></i></a>
        <?php endif; ?>
    </div>
</nav>
<!-- Navbar End -->
<?php if ($flash): ?>
    <div class="container mt-3">
        <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show mb-0" role="alert">
            <?= e($flash['msg']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    </div>
<?php endif; ?>
