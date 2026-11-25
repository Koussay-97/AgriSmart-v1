<?php
class AuthController extends Controller
{
    public function login(): void
    {
        $current = users()->currentUser();
        if ($current !== null && $current->getRole() === Utilisateur::ROLE_ADMIN) {
            redirect('dashboard');
        }
        $errors = [];
        $email = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim((string) ($_POST['email'] ?? ''));
            $errors = users()->login($_POST, Utilisateur::ROLE_ADMIN);
            if (!$errors) {
                $admin = users()->currentUser();
                flash('Bienvenue ' . ($admin ? $admin->getNom() : '') . ' !');
                redirect('dashboard');
            }
        }
        $this->renderBare('login', compact('errors', 'email'), 'Connexion');
    }

    /** Déconnexion : POST uniquement (jeton CSRF vérifié dans index.php). */
    public function logout(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('dashboard');
        }
        users()->logout();
        flash('Vous êtes déconnecté.');
        redirect('login');
    }
}
