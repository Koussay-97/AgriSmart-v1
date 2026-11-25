<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/ExploitationController.php';

/** CRUD des parcelles d'une exploitation appartenant à l'agriculteur connecté. */
class ParcelleController extends Controller
{
    private const SOLS = ['Argileux', 'Sablonneux', 'Limoneux', 'Calcaire'];
    private PDO $db;
    private ExploitationController $exploitations;

    public function __construct()
    {
        $this->db = Database::getConnexion();
        $this->exploitations = new ExploitationController();
    }

    /** Liste des parcelles d'une exploitation (?exploitation=ID) */
    public function index(): void
    {
        $user = $this->requireLogin();
        $exploitation = $this->ownedExploitation((int) ($_GET['exploitation'] ?? 0), $user);
        $this->render('parcelles', compact('exploitation'));
    }

    public function form(): void
    {
        $user = $this->requireLogin();
        $exploitation = $this->ownedExploitation((int) ($_GET['exploitation'] ?? 0), $user);
        $id = (int) ($_GET['id'] ?? 0);
        $parcelle = null;
        if ($id) {
            foreach ($exploitation->getParcelles() as $p) {
                if ($p->getId() === $id) {
                    $parcelle = $p;
                }
            }
            if (!$parcelle) {
                flash('Parcelle introuvable.', 'danger');
                redirect('parcelles', ['exploitation' => $exploitation->getId()]);
            }
        }

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim((string) ($_POST['nom'] ?? ''));
            $surfaceRaw = trim(str_replace(',', '.', (string) ($_POST['surface'] ?? '')));
            $typeSol = (string) ($_POST['typeSol'] ?? '');
            $len = mb_strlen($nom);
            if ($len < 3 || $len > 100) {
                $errors['nom'] = 'Le nom doit contenir entre 3 et 100 caractères.';
            }
            if (!preg_match('/^\d{1,6}(\.\d{1,2})?$/', $surfaceRaw) || (float) $surfaceRaw <= 0) {
                $errors['surface'] = 'La surface doit être un nombre positif (hectares, 2 décimales maximum).';
            }
            if (!in_array($typeSol, self::SOLS, true)) {
                $errors['typeSol'] = 'Choisissez un type de sol.';
            }
            if (!$errors) {
                if ($id) {
                    $stmt = $this->db->prepare('UPDATE parcelle SET nom = :nom, surface = :s, type_sol = :t WHERE id = :id AND exploitation_id = :e');
                    $stmt->execute(['nom' => $nom, 's' => $surfaceRaw, 't' => $typeSol, 'id' => $id, 'e' => $exploitation->getId()]);
                    flash('Parcelle modifiée.');
                } else {
                    $stmt = $this->db->prepare('INSERT INTO parcelle (exploitation_id, nom, surface, type_sol) VALUES (:e, :nom, :s, :t)');
                    $stmt->execute(['e' => $exploitation->getId(), 'nom' => $nom, 's' => $surfaceRaw, 't' => $typeSol]);
                    flash('Parcelle ajoutée.');
                }
                redirect('parcelles', ['exploitation' => $exploitation->getId()]);
            }
        }
        $sols = self::SOLS;
        $this->render('parcelle_form', compact('exploitation', 'parcelle', 'errors', 'sols'));
    }

    public function delete(): void
    {
        $user = $this->requireLogin();
        $exploitation = $this->ownedExploitation((int) ($_POST['exploitation'] ?? $_GET['exploitation'] ?? 0), $user);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('parcelles', ['exploitation' => $exploitation->getId()]);
        }
        $stmt = $this->db->prepare('DELETE FROM parcelle WHERE id = :id AND exploitation_id = :e');
        $stmt->execute(['id' => (int) ($_POST['id'] ?? 0), 'e' => $exploitation->getId()]);
        if ($stmt->rowCount() > 0) {
            flash('Parcelle supprimée.');
        } else {
            flash('Parcelle introuvable.', 'danger');
        }
        redirect('parcelles', ['exploitation' => $exploitation->getId()]);
    }

    /** Charge l'exploitation et vérifie qu'elle appartient à l'agriculteur connecté. */
    private function ownedExploitation(int $id, Agriculteur $user): Exploitation
    {
        $ex = $id ? $this->exploitations->findOwned($id, $user->getId()) : null;
        if (!$ex) {
            flash('Exploitation introuvable.', 'danger');
            redirect('exploitations');
        }
        return $ex;
    }
}
