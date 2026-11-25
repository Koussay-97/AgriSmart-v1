<?php
/** Contrôleur de base : charge une vue en lui passant des données + gardes d'accès. */
abstract class Controller
{
    protected function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../views/' . $view . '.php';
    }

    /** Autorisation : page réservée aux agriculteurs connectés (redirige vers la connexion sinon, 403 si autre rôle). */
    protected function requireLogin(): Agriculteur
    {
        $user = users()->requireRole(Utilisateur::ROLE_AGRICULTEUR);
        return $user;
    }

    /** Autorisation : page réservée aux experts. */
    protected function requireExpert(): Expert
    {
        $user = users()->requireRole(Utilisateur::ROLE_EXPERT);
        return $user;
    }

    /** Autorisation : agriculteur OU expert (403 pour un administrateur : il utilise le Back Office). */
    protected function requireAccount(): Utilisateur
    {
        $user = users()->requireAuth();
        if (!($user instanceof Agriculteur || $user instanceof Expert)) {
            abortWith(403, 'Accès refusé', 'Cet espace est réservé aux agriculteurs et aux experts.', 'login');
        }
        return $user;
    }
}

class Controller404 extends Controller
{
    public function index(): void { $this->render('404'); }
}
