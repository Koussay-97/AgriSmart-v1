<!-- includes/footer.php : pied de page du template + bouton "Retour en haut" (workshop étape 6) -->
<div class="container-fluid bg-dark text-light footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container py-5">
        <div class="row g-5">
            <div class="col-lg-3 col-md-6">
                <h4 class="text-white mb-3">Liens rapides</h4>
                <a class="btn btn-link" href="<?= url('home') ?>">Accueil</a>
                <?php if (isExpert()): ?>
                    <a class="btn btn-link" href="<?= url('consultations') ?>">Consultations</a>
                    <a class="btn btn-link" href="<?= url('profile') ?>">Mon profil</a>
                <?php elseif (isLoggedIn()): ?>
                    <a class="btn btn-link" href="<?= url('dashboard') ?>">Tableau de bord</a>
                    <a class="btn btn-link" href="<?= url('exploitations') ?>">Mes exploitations</a>
                    <a class="btn btn-link" href="<?= url('cultures') ?>">Cultures</a>
                    <a class="btn btn-link" href="<?= url('irrigations') ?>">Irrigation</a>
                    <a class="btn btn-link" href="<?= url('consultations') ?>">Consultations</a>
                    <a class="btn btn-link" href="<?= url('profile') ?>">Mon profil</a>
                <?php else: ?>
                    <a class="btn btn-link" href="<?= url('login') ?>">Connexion</a>
                    <a class="btn btn-link" href="<?= url('register') ?>">Inscription</a>
                <?php endif; ?>
            </div>
            <div class="col-lg-3 col-md-6">
                <h4 class="text-white mb-3">Contact</h4>
                <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>ESPRIT, Tunis, Tunisie</p>
                <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>+216 00 000 000</p>
                <p class="mb-2"><i class="fa fa-envelope me-3"></i>contact@agrismart.tn</p>
                <div class="d-flex pt-2">
                    <a class="btn btn-outline-light btn-social" href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-outline-light btn-social" href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <a class="btn btn-outline-light btn-social" href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <h4 class="text-white mb-3">Galerie</h4>
                <div class="row g-2 pt-2">
                    <div class="col-4"><img class="img-fluid bg-light p-1" src="img/solar.jpg" alt="Champ avec panneaux solaires" loading="lazy"></div>
                    <div class="col-4"><img class="img-fluid bg-light p-1" src="img/farmer.jpg" alt="Agriculteur avec tablette" loading="lazy"></div>
                    <div class="col-4"><img class="img-fluid bg-light p-1" src="img/irrigation.jpg" alt="Irrigation au goutte-à-goutte" loading="lazy"></div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <h4 class="text-white mb-3">Notre mission</h4>
                <p>Cultivons mieux, gaspillons moins. Une plateforme guidée par l'IA pour économiser l'eau et préserver les sols.</p>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="copyright">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    &copy; <?= date('Y') ?> <a class="border-bottom" href="<?= url('home') ?>">AgriSmart</a> · Projet Technologies Web, ESPRIT
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <!-- Crédit du template (obligatoire pour la version gratuite) -->
                    Template <a class="border-bottom" href="https://htmlcodex.com" target="_blank" rel="noopener">HTML Codex</a>
                    · <a class="border-bottom" href="https://themewagon.com" target="_blank" rel="noopener">ThemeWagon</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Retour en haut -->
<a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top" aria-label="Retour en haut"><i class="fa fa-arrow-up"></i></a>
