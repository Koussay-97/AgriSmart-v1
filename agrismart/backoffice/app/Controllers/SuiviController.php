<?php
require_once __DIR__ . '/../../../config/database.php';

/** Back Office - suivi agricole : vue globale des cultures et de l'eau utilisée (indicateur d'impact). */
class SuiviController extends Controller
{
    public function index(): void
    {
        $this->requireAdmin();
        $db = Database::getConnexion();

        $stats = [
            'cultures'    => (int) $db->query('SELECT COUNT(*) FROM culture')->fetchColumn(),
            'irrigations' => (int) $db->query('SELECT COUNT(*) FROM irrigation')->fetchColumn(),
            'planifiees'  => (int) $db->query("SELECT COUNT(*) FROM irrigation WHERE statut = 'planifiee'")->fetchColumn(),
            'eau'         => (float) $db->query("SELECT COALESCE(SUM(quantite_eau), 0) FROM irrigation WHERE statut = 'realisee'")->fetchColumn(),
        ];

        $stmt = $db->query("SELECT c.*, p.nom AS parcelle_nom, e.nom AS exploitation_nom, u.nom AS agriculteur_nom,
                                   COALESCE((SELECT SUM(i.quantite_eau) FROM irrigation i WHERE i.culture_id = c.id AND i.statut = 'realisee'), 0) AS eau_totale
                            FROM culture c
                            JOIN parcelle p ON p.id = c.parcelle_id
                            JOIN exploitation e ON e.id = p.exploitation_id
                            JOIN utilisateur u ON u.id = e.agriculteur_id
                            ORDER BY c.date_semis DESC, c.id DESC");
        $rows = $stmt->fetchAll();
        $cultures = array_map(fn(array $r) => Culture::fromArray($r), $rows);
        $proprietaires = [];   // culture id => nom de l'agriculteur
        foreach ($rows as $r) {
            $proprietaires[(int) $r['id']] = $r['agriculteur_nom'];
        }
        $this->render('suivi', compact('stats', 'cultures', 'proprietaires'), 'Suivi agricole', 'suivi');
    }
}
