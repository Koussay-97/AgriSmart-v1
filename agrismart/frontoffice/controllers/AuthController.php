<?php
class AuthController extends Controller
{
    public function login(): void
    {
        if (isLoggedIn()) {
            redirect(homeRoute());
        }
        $errors = [];
        $email = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim((string) ($_POST['email'] ?? ''));
            $errors = users()->login($_POST, [Utilisateur::ROLE_AGRICULTEUR, Utilisateur::ROLE_EXPERT]);
            if (!$errors) {
                $account = currentAccount();
                flash('Bienvenue ' . ($account ? $account->getNom() : '') . ' !');
                redirect(homeRoute());
            }
        }
        $this->render('login', compact('errors', 'email'));
    }

    public function register(): void
    {
        if (isLoggedIn()) {
            redirect(homeRoute());
        }
        $errors = [];
        $old = ['nom' => '', 'email' => '', 'telephone' => '', 'adresse' => '', 'region' => ''];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($old as $k => $_) {
                $old[$k] = trim((string) ($_POST[$k] ?? ''));
            }
            $errors = users()->register($_POST);   // hachage password_hash() + rôle forcé à "agriculteur"
            if (!$errors) {
                flash('Compte créé avec succès. Vous pouvez maintenant vous connecter.');
                redirect('login');
            }
        }
        $this->render('register', compact('errors', 'old'));
    }

    /** Déconnexion : POST uniquement (protégé par le jeton CSRF vérifié dans index.php). */
    public function logout(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('home');
        }
        users()->logout();
        flash('Vous êtes déconnecté.');
        redirect('home');
    }
}
