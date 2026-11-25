<?php
require_once __DIR__ . '/../../../config/database.php';

/** Back Office - consultations : liste globale, détail, affectation à un expert, suppression. */
class ConsultationsController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnexion();
    }

    public function index(): void
    {
        $this->requireAdmin();
        $statut = (string) ($_GET['statut'] ?? '');
        $expertId = (int) ($_GET['expert'] ?? 0);
        $where = [];
        $params = [];
        if (in_array($statut, Consultation::STATUTS, true)) {
            $where[] = 'c.statut = :s';
            $params['s'] = $statut;
        }
        if ($expertId) {
            $where[] = 'c.expert_id = :e';
            $params['e'] = $expertId;
        }
        $sql = $this->select() . ($where ? 'WHERE ' . implode(' AND ', $where) . ' ' : '') . 'ORDER BY c.date_demande DESC, c.id DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $consultations = array_map(fn(array $r) => Consultation::fromArray($r), $stmt->fetchAll());
        $experts = users()->activeExperts();
        $this->render('consultations', compact('consultations', 'experts', 'statut', 'expertId'), 'Consultations', 'consultations');
    }

    public function show(): void
    {
        $this->requireAdmin();
        $stmt = $this->db->prepare($this->select() . 'WHERE c.id = :id');
        $stmt->execute(['id' => (int) ($_GET['id'] ?? 0)]);
        $row = $stmt->fetch();
        if (!$row) {
            flash('Consultation introuvable.', 'danger');
            redirect('consultations');
        }
        $consultation = Consultation::fromArray($row);
        $stmt = $this->db->prepare('SELECT k.*, u.nom AS expert_nom FROM conseil k LEFT JOIN utilisateur u ON u.id = k.expert_id WHERE k.consultation_id = :c ORDER BY k.date_envoi ASC, k.id ASC');
        $stmt->execute(['c' => $consultation->getId()]);
        $conseils = array_map(fn(array $r) => Conseil::fromArray($r), $stmt->fetchAll());
        $experts = users()->activeExperts();
        $this->render('consultation-show', compact('consultation', 'conseils', 'experts'), 'Détail consultation', 'consultations');
    }

    /** Affecte (ou retire) l'expert d'une consultation. expert_id = 0 : consultation remise « ouverte ». */
    public function assign(): void
    {
        $this->requireAdmin();
        $this->postOnly();
        $id = (int) ($_POST['id'] ?? 0);
        $expertId = (int) ($_POST['expert_id'] ?? 0);
        if ($expertId === 0) {
            $stmt = $this->db->prepare("UPDATE consultation SET expert_id = NULL, statut = 'ouverte' WHERE id = :id AND statut <> 'cloturee'");
            $stmt->execute(['id' => $id]);
        } else {
            $ids = array_map(fn($x) => $x->getId(), users()->activeExperts());
            if (!in_array($expertId, $ids, true)) {
                flash('Expert invalide.', 'danger');
                redirect('consultation-show', ['id' => $id]);
            }
            $stmt = $this->db->prepare("UPDATE consultation SET expert_id = :e, statut = 'en_cours' WHERE id = :id AND statut <> 'cloturee'");
            $stmt->execute(['e' => $expertId, 'id' => $id]);
        }
        $stmt->rowCount() > 0 ? flash('Affectation mise à jour.') : flash('Aucun changement (consultation clôturée ou déjà affectée à cet expert).', 'warning');
        redirect('consultation-show', ['id' => $id]);
    }

    public function delete(): void
    {
        $this->requireAdmin();
        $this->postOnly();
        $stmt = $this->db->prepare('DELETE FROM consultation WHERE id = :id');
        $stmt->execute(['id' => (int) ($_POST['id'] ?? 0)]);
        $stmt->rowCount() > 0 ? flash('Consultation supprimée.') : flash('Consultation introuvable.', 'danger');
        redirect('consultations');
    }

    private function postOnly(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('consultations');
        }
    }

    private function select(): string
    {
        return 'SELECT c.*, ua.nom AS agriculteur_nom, ue.nom AS expert_nom, ex.nom AS exploitation_nom,
                       (SELECT COUNT(*) FROM conseil k WHERE k.consultation_id = c.id) AS nb_conseils
                FROM consultation c
                JOIN utilisateur ua ON ua.id = c.agriculteur_id
                LEFT JOIN utilisateur ue ON ue.id = c.expert_id
                LEFT JOIN exploitation ex ON ex.id = c.exploitation_id ';
    }
}
