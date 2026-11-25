<?php
require_once __DIR__ . '/../../../config/database.php';

/** CRUD des utilisateurs (Back Office) : liste/recherche, consultation, ajout, modification, suppression, rôle, statut. */
class UtilisateursController extends Controller
{
    /** Liste + recherche (?q=) + filtres (?role= ?region= ?actif=) + pagination (?p=) */
    public function index(): void
    {
        $admin = $this->requireAdmin();
        $filters = [
            'q'      => trim((string) ($_GET['q'] ?? '')),
            'role'   => (string) ($_GET['role'] ?? ''),
            'region' => (string) ($_GET['region'] ?? ''),
            'actif'  => (string) ($_GET['actif'] ?? ''),
        ];
        $result = users()->search($filters, (int) ($_GET['p'] ?? 1), 10);
        $regions = Agriculteur::REGIONS;
        $this->render('utilisateurs', compact('result', 'filters', 'regions', 'admin'), 'Utilisateurs', 'utilisateurs');
    }

    /** Consultation d'un utilisateur (+ ses exploitations s'il est agriculteur). */
    public function show(): void
    {
        $this->requireAdmin();
        $target = users()->findById((int) ($_GET['id'] ?? 0));
        if (!$target) {
            flash('Utilisateur introuvable.', 'danger');
            redirect('utilisateurs');
        }
        $exploitations = [];
        if ($target->getRole() === Utilisateur::ROLE_AGRICULTEUR) {
            $stmt = Database::getConnexion()->prepare(
                'SELECT e.id, e.nom, e.localisation, COUNT(p.id) AS nb_parcelles, COALESCE(SUM(p.surface), 0) AS surface
                 FROM exploitation e LEFT JOIN parcelle p ON p.exploitation_id = e.id
                 WHERE e.agriculteur_id = :id
                 GROUP BY e.id, e.nom, e.localisation ORDER BY e.id ASC'
            );
            $stmt->execute(['id' => $target->getId()]);
            $exploitations = $stmt->fetchAll();
        }
        $this->render('utilisateur-show', compact('target', 'exploitations'), 'Détail utilisateur', 'utilisateurs');
    }

    /** Ajout (sans id) ou modification (?id=) */
    public function form(): void
    {
        $admin = $this->requireAdmin();
        $id = (int) ($_GET['id'] ?? 0);
        $target = $id ? users()->findById($id) : null;
        if ($id && !$target) {
            flash('Utilisateur introuvable.', 'danger');
            redirect('utilisateurs');
        }

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $id ? users()->updateUser($id, $_POST, $admin->getId()) : users()->createUser($_POST);
            if (!$errors) {
                flash($id ? 'Utilisateur modifié.' : 'Utilisateur ajouté.');
                redirect('utilisateurs');
            }
        }
        $title = $id ? 'Modifier un utilisateur' : 'Ajouter un utilisateur';
        $regions = Agriculteur::REGIONS;
        $this->render('utilisateur-form', compact('target', 'errors', 'regions', 'admin'), $title, 'utilisateurs');
    }

    /** Suppression (POST uniquement, jamais en GET) */
    public function delete(): void
    {
        $admin = $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('utilisateurs');
        }
        $error = users()->deleteUser((int) ($_POST['id'] ?? 0), $admin->getId());
        $error === null ? flash('Utilisateur supprimé.') : flash($error, 'danger');
        redirect('utilisateurs');
    }

    /** Active / bloque un compte (POST) */
    public function toggle(): void
    {
        $admin = $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('utilisateurs');
        }
        $actif = ($_POST['actif'] ?? '') === '1';
        $error = users()->setActive((int) ($_POST['id'] ?? 0), $actif, $admin->getId());
        $error === null ? flash($actif ? 'Compte activé.' : 'Compte bloqué.') : flash($error, 'danger');
        redirect('utilisateurs');
    }

    /** Changement rapide de rôle depuis la liste (POST) */
    public function role(): void
    {
        $admin = $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('utilisateurs');
        }
        $error = users()->setRole((int) ($_POST['id'] ?? 0), (string) ($_POST['role'] ?? ''), $admin->getId());
        $error === null ? flash('Rôle modifié.') : flash($error, 'danger');
        redirect('utilisateurs');
    }
}
