<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/ExploitationController.php';

class DashboardController extends Controller
{
    public function index(): void
    {
        $account = $this->requireAccount();
        if ($account instanceof Expert) {
            redirect('consultations');   // l'expert travaille depuis sa liste de consultations
        }
        $user = $account;
        $db = Database::getConnexion();

        $exploitations = (new ExploitationController())->listFor($user->getId());
        $nbParcelles = 0;
        $surface = 0.0;
        foreach ($exploitations as $ex) {
            $nbParcelles += count($ex->getParcelles());
            $surface += $ex->calculerSurface();
        }

        // Suivi agricole : cultures, eau utilisée, prochaines irrigations
        $joins = 'FROM irrigation i JOIN culture c ON c.id = i.culture_id JOIN parcelle p ON p.id = c.parcelle_id JOIN exploitation e ON e.id = p.exploitation_id';
        $stmt = $db->prepare('SELECT COUNT(*) FROM culture c JOIN parcelle p ON p.id = c.parcelle_id JOIN exploitation e ON e.id = p.exploitation_id WHERE e.agriculteur_id = :a');
        $stmt->execute(['a' => $user->getId()]);
        $nbCultures = (int) $stmt->fetchColumn();

        $stmt = $db->prepare("SELECT COALESCE(SUM(i.quantite_eau), 0) $joins WHERE e.agriculteur_id = :a AND i.statut = 'realisee'");
        $stmt->execute(['a' => $user->getId()]);
        $eauTotale = (float) $stmt->fetchColumn();

        $stmt = $db->prepare("SELECT i.*, c.nom AS culture_nom, p.nom AS parcelle_nom $joins
                              WHERE e.agriculteur_id = :a AND i.statut = 'planifiee' ORDER BY i.date_irrigation ASC, i.id ASC LIMIT 3");
        $stmt->execute(['a' => $user->getId()]);
        $prochaines = array_map(fn(array $r) => Irrigation::fromArray($r), $stmt->fetchAll());

        $stmt = $db->prepare("SELECT COUNT(*) FROM consultation WHERE agriculteur_id = :a AND statut <> 'cloturee'");
        $stmt->execute(['a' => $user->getId()]);
        $nbConsultations = (int) $stmt->fetchColumn();

        $this->render('dashboard', compact('user', 'exploitations', 'nbParcelles', 'surface', 'nbCultures', 'eauTotale', 'prochaines', 'nbConsultations'));
    }
}
