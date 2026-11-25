<?php
require_once __DIR__ . '/../../config/database.php';

/**
 * Module Conseils (Membre 2) : un expert donne des conseils dans les consultations dont il est chargé.
 * Un conseil marqué "sensible" n'est pas visible par l'agriculteur tant qu'un administrateur ne l'a pas validé (Back Office).
 */
class ConseilController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnexion();
    }

    /** Ajout (?consultation=ID) ou modification (?id=ID) d'un conseil. */
    public function form(): void
    {
        $expert = $this->requireExpert();
        $id = (int) ($_GET['id'] ?? 0);
        $conseil = null;

        if ($id) {
            $stmt = $this->db->prepare('SELECT * FROM conseil WHERE id = :id AND expert_id = :e');
            $stmt->execute(['id' => $id, 'e' => $expert->getId()]);
            $row = $stmt->fetch();
            $conseil = $row ? Conseil::fromArray($row) : null;
            $consultationId = $conseil ? $conseil->getConsultationId() : 0;
        } else {
            $consultationId = (int) ($_GET['consultation'] ?? 0);
        }
        $consultation = $this->ownedOpenConsultation($consultationId, $expert->getId());
        if (!$consultation || ($id && !$conseil)) {
            flash('Action impossible : consultation introuvable, non prise en charge par vous, ou déjà clôturée.', 'danger');
            redirect('consultations');
        }

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $contenu = trim((string) ($_POST['contenu'] ?? ''));
            $sensible = !empty($_POST['sensible']);
            $len = mb_strlen($contenu);
            if ($len < 10 || $len > 3000) {
                $errors['contenu'] = 'Le conseil doit contenir entre 10 et 3000 caractères.';
            }
            if (!$errors) {
                $valide = $sensible ? 0 : 1;   // conseil non sensible : publié tout de suite
                if ($id) {
                    $stmt = $this->db->prepare('UPDATE conseil SET contenu = :c, sensible = :s, valide = :v WHERE id = :id AND expert_id = :e');
                    $stmt->execute(['c' => $contenu, 's' => $sensible ? 1 : 0, 'v' => $valide, 'id' => $id, 'e' => $expert->getId()]);
                    flash($sensible ? 'Conseil modifié : il sera visible après validation par un administrateur.' : 'Conseil modifié.');
                } else {
                    $stmt = $this->db->prepare('INSERT INTO conseil (consultation_id, expert_id, contenu, sensible, valide) VALUES (:k, :e, :c, :s, :v)');
                    $stmt->execute(['k' => $consultationId, 'e' => $expert->getId(), 'c' => $contenu, 's' => $sensible ? 1 : 0, 'v' => $valide]);
                    flash($sensible ? 'Conseil enregistré : il sera visible par l\'agriculteur après validation par un administrateur.' : 'Conseil publié.');
                }
                redirect('consultation', ['id' => $consultationId]);
            }
        }
        $this->render('conseil_form', compact('consultation', 'conseil', 'errors'));
    }

    public function delete(): void
    {
        $expert = $this->requireExpert();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('consultations');
        }
        $consultationId = (int) ($_POST['consultation'] ?? 0);
        $stmt = $this->db->prepare('DELETE FROM conseil WHERE id = :id AND expert_id = :e');
        $stmt->execute(['id' => (int) ($_POST['id'] ?? 0), 'e' => $expert->getId()]);
        $stmt->rowCount() > 0 ? flash('Conseil supprimé.') : flash('Conseil introuvable.', 'danger');
        redirect('consultation', ['id' => $consultationId]);
    }

    /** Consultation prise en charge par cet expert et non clôturée (seule situation où l'on peut donner un conseil). */
    private function ownedOpenConsultation(int $id, int $expertId): ?Consultation
    {
        $stmt = $this->db->prepare("SELECT c.*, ua.nom AS agriculteur_nom FROM consultation c JOIN utilisateur ua ON ua.id = c.agriculteur_id
                                    WHERE c.id = :id AND c.expert_id = :e AND c.statut = 'en_cours'");
        $stmt->execute(['id' => $id, 'e' => $expertId]);
        $row = $stmt->fetch();
        return $row ? Consultation::fromArray($row) : null;
    }
}
