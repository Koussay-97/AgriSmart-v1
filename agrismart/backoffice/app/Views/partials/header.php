<!-- partials/header.php : barre du haut, identique sur toutes les pages. Variable : $admin (Utilisateur connecté) -->
<header class="header">
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between py-3">
            <button type="button" id="menu-toggle" class="btn btn-light" aria-label="Menu"><i class="bi bi-list fs-5"></i></button>
            <form class="d-none d-md-block flex-grow-1 mx-4" role="search" method="get" action="index.php">
                <input type="hidden" name="page" value="utilisateurs">
                <div class="input-group" style="max-width: 360px;">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                    <input type="search" name="q" class="form-control border-start-0" placeholder="Rechercher un utilisateur..." aria-label="Rechercher">
                </div>
            </form>
            <div class="d-flex align-items-center gap-3">
                <a href="<?= url('profil') ?>" class="d-flex align-items-center text-decoration-none text-dark">
                    <span class="avatar me-2"><?= e(mb_strtoupper(mb_substr($admin ? $admin->getNom() : 'A', 0, 1))) ?></span>
                    <div class="lh-sm d-none d-sm-block"><strong><?= e($admin ? $admin->getNom() : 'Admin') ?></strong><br><small class="text-muted">Administrateur</small></div>
                </a>
                <form method="post" action="<?= url('logout') ?>" class="m-0">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill"><i class="bi bi-box-arrow-right"></i> Déconnexion</button>
                </form>
            </div>
        </div>
    </div>
</header>
