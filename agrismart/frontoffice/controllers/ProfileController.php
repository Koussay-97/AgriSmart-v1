<?php
/** Mon profil (agriculteur ou expert) : modification des informations, changement de mot de passe, suppression du compte. */
class ProfileController extends Controller
{
    public function index(): void
    {
        $user = $this->requireAccount();
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = users()->updateOwnProfile($user, $_POST);
            if (!$errors) {
                flash('Profil mis à jour.');
                redirect('profile');
            }
        }
        $this->show($user, $errors, [], []);
    }

    public function password(): void
    {
        $user = $this->requireAccount();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('profile');
        }
        $pwdErrors = users()->changePassword($user, $_POST);
        if (!$pwdErrors) {
            flash('Mot de passe modifié avec succès.');
            redirect('profile');
        }
        $this->show($user, [], $pwdErrors, []);
    }

    public function delete(): void
    {
        $user = $this->requireAccount();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('profile');
        }
        $error = users()->deleteOwnAccount($user, (string) ($_POST['password'] ?? ''));
        if ($error === null) {
            flash('Votre compte a été supprimé.');
            redirect('home');
        }
        $this->show($user, [], [], ['delete_password' => $error]);
    }

    /** Affiche la vue du profil selon le rôle (l'expert a une spécialité, pas de région). */
    private function show(Utilisateur $user, array $errors, array $pwdErrors, array $delErrors): void
    {
        $view = $user instanceof Expert ? 'profile_expert' : 'profile';
        $this->render($view, compact('user', 'errors', 'pwdErrors', 'delErrors'));
    }
}
