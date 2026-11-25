<?php
$pageTitle = 'Accueil';
$currentPage = 'home';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once __DIR__ . '/includes/head.php'; ?>
</head>
<body>
<?php require_once __DIR__ . '/includes/spinner.php'; ?>
<?php require_once __DIR__ . '/includes/navbar.php'; ?>

<!-- Hero Start -->
<div class="container-fluid p-0 mb-5">
    <div class="hero-wrap">
        <img src="img/hero.jpg" alt="Agriculture durable et technologie" fetchpriority="high">
        <div class="hero-overlay">
            <div class="container">
                <div class="row justify-content-start">
                    <div class="col-sm-10 col-lg-8">
                        <h5 class="text-ochre text-uppercase mb-3 animated slideInDown">Transition écologique · Agriculture · Ressources</h5>
                        <h1 class="display-3 text-white animated slideInDown">Agri<span class="text-ochre">Smart</span></h1>
                        <p class="fs-5 text-white mb-4 pb-2">Cultivons mieux, gaspillons moins. Une plateforme web guidée par l'IA qui aide les agriculteurs à économiser l'eau, préserver les sols et produire durablement.</p>
                        <?php if (isLoggedIn()): ?>
                            <a href="<?= url('dashboard') ?>" class="btn btn-primary py-md-3 px-md-5 me-3 animated slideInLeft">Mon tableau de bord</a>
                            <a href="<?= url('exploitations') ?>" class="btn btn-light py-md-3 px-md-5 animated slideInRight">Mes exploitations</a>
                        <?php else: ?>
                            <a href="<?= url('register') ?>" class="btn btn-primary py-md-3 px-md-5 me-3 animated slideInLeft">Commencer</a>
                            <a href="<?= url('login') ?>" class="btn btn-light py-md-3 px-md-5 animated slideInRight">Se connecter</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Hero End -->

<!-- Constats Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title bg-white text-center text-primary px-3">Contexte</h6>
            <h1 class="mb-5">Pourquoi AgriSmart ?</h1>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="service-item text-center pt-3">
                    <div class="p-4">
                        <i class="fa fa-3x fa-tint text-primary mb-4"></i>
                        <h5 class="mb-3">Pression sur l'eau</h5>
                        <p>L'irrigation consomme beaucoup, et la ressource est limitée dans un climat de plus en plus sec.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm-6 wow fadeInUp" data-wow-delay="0.3s">
                <div class="service-item text-center pt-3">
                    <div class="p-4">
                        <i class="fa fa-3x fa-seedling text-primary mb-4"></i>
                        <h5 class="mb-3">Sols fragilisés</h5>
                        <p>Un excès d'engrais et de mauvaises pratiques appauvrissent la terre et la biodiversité.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm-6 wow fadeInUp" data-wow-delay="0.5s">
                <div class="service-item text-center pt-3">
                    <div class="p-4">
                        <i class="fa fa-3x fa-tools text-primary mb-4"></i>
                        <h5 class="mb-3">Outils peu accessibles</h5>
                        <p>Beaucoup d'agriculteurs n'ont ni conseils fiables ni outils adaptés à leur taille.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Constats End -->

<!-- Concept Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s" style="min-height: 400px;">
                <div class="position-relative h-100">
                    <img class="img-fluid position-absolute w-100 h-100" src="img/farmer.jpg" alt="Agriculteur utilisant une tablette dans son champ" style="object-fit: cover;" loading="lazy">
                </div>
            </div>
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                <h6 class="section-title bg-white text-start text-primary pe-3">Le concept</h6>
                <h1 class="mb-4">La technologie verte au service des agriculteurs</h1>
                <p class="mb-4">AgriSmart s'inscrit dans le développement web à impact écologique. Nous combinons technologie web, intelligence artificielle et savoir agronomique pour aider les exploitants à économiser l'eau, préserver les sols et produire durablement.</p>
                <div class="row gy-2 gx-4 mb-4">
                    <div class="col-sm-6"><p class="mb-0"><i class="fa fa-check text-primary me-2"></i>Réduire le gaspillage d'eau, d'engrais et d'énergie</p></div>
                    <div class="col-sm-6"><p class="mb-0"><i class="fa fa-check text-primary me-2"></i>Encourager les pratiques agroécologiques</p></div>
                    <div class="col-sm-6"><p class="mb-0"><i class="fa fa-check text-primary me-2"></i>Offrir des conseils fiables et accessibles</p></div>
                    <div class="col-sm-6"><p class="mb-0"><i class="fa fa-check text-primary me-2"></i>Partager les bonnes pratiques entre agriculteurs</p></div>
                </div>
                <?php if (!isLoggedIn()): ?>
                    <a class="btn btn-primary py-3 px-5 mt-2" href="<?= url('register') ?>">Créer mon compte</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<!-- Concept End -->

<!-- Étapes Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title bg-white text-center text-primary px-3">Fonctionnement</h6>
            <h1 class="mb-5">Comment ça marche ?</h1>
        </div>
        <div class="row g-5 align-items-center">
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                <?php
                $etapes = [
                    "L'agriculteur crée son compte et décrit son exploitation (cultures, surface, région).",
                    "Il renseigne ses parcelles : sol, irrigation, intrants, consommation d'eau.",
                    "L'IA analyse les données et propose un plan : irrigation, rotation, alertes météo.",
                    "Un expert agronome valide les conseils sensibles avant leur diffusion.",
                    "Le tableau de bord mesure l'eau économisée et l'impact écologique.",
                ];
                foreach ($etapes as $i => $texte): ?>
                    <div class="d-flex align-items-start mb-4">
                        <div class="step-number me-3"><?= $i + 1 ?></div>
                        <p class="mb-0 fs-5"><?= e($texte) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                <img class="img-fluid w-100" src="img/irrigation.jpg" alt="Irrigation goutte-à-goutte sur des jeunes plants" loading="lazy" style="max-height: 460px; object-fit: cover;">
            </div>
        </div>
    </div>
</div>
<!-- Étapes End -->

<!-- Transition écologique Start -->
<div class="container-xxl py-5 category">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title bg-white text-center text-primary px-3">Engagement</h6>
            <h1 class="mb-5">Transition écologique</h1>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6 wow zoomIn" data-wow-delay="0.1s">
                <div class="bg-light p-4 h-100 text-center">
                    <i class="fa fa-3x fa-tint text-primary mb-3"></i>
                    <h5 class="mb-2">Eau</h5>
                    <p class="mb-0">Irrigation ajustée aux besoins réels et à la météo.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 wow zoomIn" data-wow-delay="0.3s">
                <div class="bg-light p-4 h-100 text-center">
                    <i class="fa fa-3x fa-leaf text-primary mb-3"></i>
                    <h5 class="mb-2">Sols &amp; biodiversité</h5>
                    <p class="mb-0">Rotation des cultures, couverture des sols, moins d'intrants chimiques.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 wow zoomIn" data-wow-delay="0.5s">
                <div class="bg-light p-4 h-100 text-center">
                    <i class="fa fa-3x fa-solar-panel text-primary mb-3"></i>
                    <h5 class="mb-2">Numérique sobre</h5>
                    <p class="mb-0">Site éco-conçu : pages légères, images optimisées, zéro papier.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Transition écologique End -->

<?php if (!isLoggedIn()): ?>
<!-- Appel à l'action -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="bg-dark text-center p-5 wow fadeInUp" data-wow-delay="0.1s">
            <h2 class="text-white mb-3">Prêt à cultiver durablement ?</h2>
            <p class="text-white-50 mb-4">Parce que chaque goutte et chaque graine comptent.</p>
            <a href="<?= url('register') ?>" class="btn btn-ochre py-3 px-5">Créer mon compte gratuitement</a>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<?php require_once __DIR__ . '/includes/scripts.php'; ?>
</body>
</html>
