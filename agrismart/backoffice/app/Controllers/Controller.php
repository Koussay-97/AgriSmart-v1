<?php
/**
 * Contrôleur de base (workshop étape 8) : capture la vue de la page dans $content,
 * puis inclut layout.php qui assemble head + sidebar + header + contenu + footer + scripts.
 */
abstract class Controller
{
    protected function render(string $view, array $data = [], string $title = 'AgriSmart Admin', string $currentPage = '', string $extraScripts = ''): void
    {
        $admin = users()->currentUser();   // affiché dans l'en-tête (nom, déconnexion)
        extract($data, EXTR_SKIP);

        ob_start();
        require __DIR__ . '/../Views/pages/' . $view . '.php';
        $content = ob_get_clean();

        require __DIR__ . '/../Views/layout.php';
    }

    /** Page autonome (sans sidebar ni en-tête) : formulaire de connexion. */
    protected function renderBare(string $view, array $data = [], string $title = 'Connexion'): void
    {
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../Views/auth/' . $view . '.php';
    }

    /** Autorisation : réservé aux administrateurs (redirige vers la connexion, ou 403 pour un autre rôle). */
    protected function requireAdmin(): Administrateur
    {
        $user = users()->requireRole(Utilisateur::ROLE_ADMIN);
        return $user;
    }
}
