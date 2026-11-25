<?php
/** Profil de l'administrateur connecté : informations + changement de mot de passe. */
class ProfilController extends Controller
{
    public function index(): void
    {
        $admin = $this->requireAdmin();
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = users()->updateOwnProfile($admin, $_POST);
            if (!$errors) {
                flash('Profil mis à jour.');
                redirect('profil');
            }
        }
        $this->render('profil', ['user' => $admin, 'errors' => $errors, 'pwdErrors' => []], 'Mon profil', 'profil');
    }

    public function password(): void
    {
        $admin = $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('profil');
        }
        $pwdErrors = users()->changePassword($admin, $_POST);
        if (!$pwdErrors) {
            flash('Mot de passe modifié avec succès.');
            redirect('profil');
        }
        $this->render('profil', ['user' => $admin, 'errors' => [], 'pwdErrors' => $pwdErrors], 'Mon profil', 'profil');
    }
}
