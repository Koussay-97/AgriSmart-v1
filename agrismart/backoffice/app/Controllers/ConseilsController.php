<?php
require_once __DIR__ . '/../../../config/database.php';

/** Back Office - conseils : validation des conseils « sensibles » avant leur diffusion aux agriculteurs. */
class ConseilsController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnexion();
    }

    /** Conseils sensibles en attente de validation */
    public function index(): void
    {
        $this->requireAdmin();
        $stmt = $this->db->query('SELECT k.*, u.nom AS expert_nom, c.sujet AS consultation_sujet, ua.nom AS agriculteur_nom
                                  FROM conseil k
                                  JOIN consultation c ON c.id = k.consultation_id
                                  JOIN utilisateur ua ON ua.id = c.agriculteur_id
                                  LEFT JOIN utilisateur u ON u.id = k.expert_id
                                  WHERE k.sensible = 1 AND k.valide = 0
                                  ORDER BY k.date_envoi ASC, k.id ASC');
        $conseils = array_map(fn(array $r) => Conseil::fromArray($r), $stmt->fetchAll());
        $this->render('conseils', compact('conseils'), 'Conseils à valider', 'conseils');
    }

    public function validate(): void
    {
        $this->requireAdmin();
        $this->postOnly();
        $stmt = $this->db->prepare('UPDATE conseil SET valide = 1 WHERE id = :id AND sensible = 1 AND valide = 0');
        $stmt->execute(['id' => (int) ($_POST['id'] ?? 0)]);
        $stmt->rowCount() > 0 ? flash('Conseil validé : il est maintenant visible par l\'agriculteur.') : flash('Conseil introuvable ou déjà validé.', 'warning');
        $this->back();
    }

    /** Rejet / suppression d'un conseil */
    public function delete(): void
    {
        $this->requireAdmin();
        $this->postOnly();
        $stmt = $this->db->prepare('DELETE FROM conseil WHERE id = :id');
        $stmt->execute(['id' => (int) ($_POST['id'] ?? 0)]);
        $stmt->rowCount() > 0 ? flash('Conseil supprimé.') : flash('Conseil introuvable.', 'danger');
        $this->back();
    }

    private function postOnly(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('conseils');
        }
    }

    /** Retour : détail de la consultation d'où vient l'action, sinon liste des conseils à valider. */
    private function back(): void
    {
        if (($_POST['from'] ?? '') === 'consultation') {
            redirect('consultation-show', ['id' => (int) ($_POST['consultation'] ?? 0)]);
        }
        redirect('conseils');
    }
}
