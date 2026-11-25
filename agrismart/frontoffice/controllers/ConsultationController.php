<?php
require_once __DIR__ . '/../../config/database.php';

/**
 * Module Consultations (Membre 2).
 *  - Agriculteur : soumettre, consulter, modifier (tant qu'elle est ouverte), clôturer et supprimer SES consultations.
 *  - Expert      : voir les consultations à traiter, en prendre une en charge, la consulter et la clôturer.
 */
class ConsultationController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnexion();
    }

    // ================= LISTE =================
    public function index(): void
    {
        $account = $this->requireAccount();
        if ($account instanceof Expert) {
            $aTraiter = $this->fetchAll($this->select(false) . "WHERE c.expert_id IS NULL AND c.statut = 'ouverte' ORDER BY c.date_demande ASC, c.id ASC");
            $mesConsultations = $this->fetchAll($this->select(false) . "WHERE c.expert_id = :e ORDER BY (c.statut = 'cloturee') ASC, c.date_demande DESC, c.id DESC", ['e' => $account->getId()]);
            $this->render('consultations_expert', compact('account', 'aTraiter', 'mesConsultations'));
            return;
        }
        $consultations = $this->fetchAll($this->select(true) . 'WHERE c.agriculteur_id = :a ORDER BY c.date_demande DESC, c.id DESC', ['a' => $account->getId()]);
        $this->render('consultations', compact('account', 'consultations'));
    }

    // ================= DÉTAIL =================
    public function show(): void
    {
        $account = $this->requireAccount();
        $isExpert = $account instanceof Expert;
        $consultation = $this->findFor($account, (int) ($_GET['id'] ?? 0));
        if (!$consultation) {
            flash('Consultation introuvable.', 'danger');
            redirect('consultations');
        }
        // L'agriculteur ne voit que les conseils validés ; l'expert voit aussi ses conseils en attente de validation.
        $conseils = $this->conseilsOf($consultation->getId(), !$isExpert);
        $this->render('consultation_show', compact('account', 'isExpert', 'consultation', 'conseils'));
    }

    // ================= AGRICULTEUR : AJOUT / MODIFICATION =================
    public function form(): void
    {
        $user = $this->requireLogin();
        $id = (int) ($_GET['id'] ?? 0);
        $consultation = null;
        if ($id) {
            $consultation = $this->findFor($user, $id);
            if (!$consultation || $consultation->getStatut() !== Consultation::STATUT_OUVERTE) {
                flash('Cette consultation ne peut plus être modifiée (elle est déjà prise en charge ou clôturée).', 'warning');
                redirect('consultations');
            }
        }
        $exploitations = $this->exploitationsOf($user->getId());

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sujet = trim((string) ($_POST['sujet'] ?? ''));
            $description = trim((string) ($_POST['description'] ?? ''));
            $exploitationId = (int) ($_POST['exploitation_id'] ?? 0);

            $len = mb_strlen($sujet);
            if ($len < 5 || $len > 150) {
                $errors['sujet'] = 'Le sujet doit contenir entre 5 et 150 caractères.';
            }
            $len = mb_strlen($description);
            if ($len < 10 || $len > 2000) {
                $errors['description'] = 'La description doit contenir entre 10 et 2000 caractères.';
            }
            if ($exploitationId !== 0 && !in_array($exploitationId, array_map(fn($x) => (int) $x['id'], $exploitations), true)) {
                $errors['exploitation_id'] = 'Exploitation invalide.';
            }
            if (!$errors) {
                $exp = $exploitationId ?: null;
                if ($id) {
                    $stmt = $this->db->prepare("UPDATE consultation SET sujet = :s, description = :d, exploitation_id = :x WHERE id = :id AND agriculteur_id = :a AND statut = 'ouverte'");
                    $stmt->execute(['s' => $sujet, 'd' => $description, 'x' => $exp, 'id' => $id, 'a' => $user->getId()]);
                    flash('Consultation modifiée.');
                    redirect('consultation', ['id' => $id]);
                }
                $stmt = $this->db->prepare('INSERT INTO consultation (agriculteur_id, exploitation_id, sujet, description) VALUES (:a, :x, :s, :d)');
                $stmt->execute(['a' => $user->getId(), 'x' => $exp, 's' => $sujet, 'd' => $description]);
                flash('Consultation envoyée : un expert va la prendre en charge.');
                redirect('consultation', ['id' => (int) $this->db->lastInsertId()]);
            }
        }
        $this->render('consultation_form', compact('consultation', 'exploitations', 'errors'));
    }

    // ================= ACTIONS (POST) =================
    /** Clôture : l'agriculteur propriétaire ou l'expert en charge. */
    public function close(): void
    {
        $account = $this->requireAccount();
        $this->postOnly();
        $id = (int) ($_POST['id'] ?? 0);
        if ($account instanceof Expert) {
            $stmt = $this->db->prepare("UPDATE consultation SET statut = 'cloturee' WHERE id = :id AND expert_id = :u AND statut <> 'cloturee'");
        } else {
            $stmt = $this->db->prepare("UPDATE consultation SET statut = 'cloturee' WHERE id = :id AND agriculteur_id = :u AND statut <> 'cloturee'");
        }
        $stmt->execute(['id' => $id, 'u' => $account->getId()]);
        $stmt->rowCount() > 0 ? flash('Consultation clôturée.') : flash('Action impossible.', 'danger');
        redirect('consultation', ['id' => $id]);
    }

    /** L'agriculteur supprime sa consultation (tant qu'elle n'est pas en cours de traitement). */
    public function delete(): void
    {
        $user = $this->requireLogin();
        $this->postOnly();
        $stmt = $this->db->prepare("DELETE FROM consultation WHERE id = :id AND agriculteur_id = :a AND statut IN ('ouverte', 'cloturee')");
        $stmt->execute(['id' => (int) ($_POST['id'] ?? 0), 'a' => $user->getId()]);
        $stmt->rowCount() > 0 ? flash('Consultation supprimée.') : flash('Suppression impossible : la consultation est en cours de traitement.', 'danger');
        redirect('consultations');
    }

    /** L'expert prend en charge une consultation ouverte (atomique : une seule personne peut la prendre). */
    public function take(): void
    {
        $expert = $this->requireExpert();
        $this->postOnly();
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = $this->db->prepare("UPDATE consultation SET expert_id = :e, statut = 'en_cours' WHERE id = :id AND expert_id IS NULL AND statut = 'ouverte'");
        $stmt->execute(['e' => $expert->getId(), 'id' => $id]);
        if ($stmt->rowCount() > 0) {
            flash('Consultation prise en charge : vous pouvez maintenant donner vos conseils.');
        } else {
            flash('Cette consultation a déjà été prise en charge.', 'warning');
        }
        redirect('consultation', ['id' => $id]);
    }

    private function postOnly(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('consultations');
        }
    }

    // ================= ACCÈS AUX DONNÉES =================
    private function select(bool $validOnly): string
    {
        return 'SELECT c.*, ua.nom AS agriculteur_nom, ue.nom AS expert_nom, ex.nom AS exploitation_nom,
                       (SELECT COUNT(*) FROM conseil k WHERE k.consultation_id = c.id' . ($validOnly ? ' AND k.valide = 1' : '') . ') AS nb_conseils
                FROM consultation c
                JOIN utilisateur ua ON ua.id = c.agriculteur_id
                LEFT JOIN utilisateur ue ON ue.id = c.expert_id
                LEFT JOIN exploitation ex ON ex.id = c.exploitation_id ';
    }

    /** @return Consultation[] */
    private function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return array_map(fn(array $r) => Consultation::fromArray($r), $stmt->fetchAll());
    }

    /**
     * Consultation visible par ce compte : l'agriculteur ne voit que les siennes ; l'expert voit celles dont il est chargé
     * et celles qui sont encore ouvertes (pour décider de les prendre en charge).
     */
    private function findFor(Utilisateur $account, int $id): ?Consultation
    {
        if ($account instanceof Expert) {
            $sql = $this->select(false) . "WHERE c.id = :id AND (c.expert_id = :u OR (c.expert_id IS NULL AND c.statut = 'ouverte'))";
        } else {
            $sql = $this->select(true) . 'WHERE c.id = :id AND c.agriculteur_id = :u';
        }
        $list = $this->fetchAll($sql, ['id' => $id, 'u' => $account->getId()]);
        return $list[0] ?? null;
    }

    /** @return Conseil[] */
    private function conseilsOf(int $consultationId, bool $validOnly): array
    {
        $sql = 'SELECT k.*, u.nom AS expert_nom FROM conseil k LEFT JOIN utilisateur u ON u.id = k.expert_id WHERE k.consultation_id = :c'
             . ($validOnly ? ' AND k.valide = 1' : '') . ' ORDER BY k.date_envoi ASC, k.id ASC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['c' => $consultationId]);
        return array_map(fn(array $r) => Conseil::fromArray($r), $stmt->fetchAll());
    }

    private function exploitationsOf(int $agriculteurId): array
    {
        $stmt = $this->db->prepare('SELECT id, nom FROM exploitation WHERE agriculteur_id = :a ORDER BY nom ASC');
        $stmt->execute(['a' => $agriculteurId]);
        return $stmt->fetchAll();
    }
}
