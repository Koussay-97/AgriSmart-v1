<?php
require_once __DIR__ . '/../../../config/database.php';

/** Tableau de bord administrateur : statistiques globales. */
class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireAdmin();
        $db = Database::getConnexion();

        $stats = [
            'total'         => users()->countAll(),
            'agriculteurs'  => users()->countByRole(Utilisateur::ROLE_AGRICULTEUR),
            'admins'        => users()->countByRole(Utilisateur::ROLE_ADMIN),
            'experts'       => users()->countByRole(Utilisateur::ROLE_EXPERT),
            'consultations_ouvertes' => (int) $db->query("SELECT COUNT(*) FROM consultation WHERE statut = 'ouverte'")->fetchColumn(),
            'conseils_a_valider'     => (int) $db->query('SELECT COUNT(*) FROM conseil WHERE sensible = 1 AND valide = 0')->fetchColumn(),
            'bloques'       => users()->countBlocked(),
            'exploitations' => (int) $db->query('SELECT COUNT(*) FROM exploitation')->fetchColumn(),
            'parcelles'     => (int) $db->query('SELECT COUNT(*) FROM parcelle')->fetchColumn(),
            'surface'       => (float) $db->query('SELECT COALESCE(SUM(surface), 0) FROM parcelle')->fetchColumn(),
        ];
        $recent = users()->recent(5);
        $byRegion = users()->countByRegion();

        $this->render('dashboard', compact('stats', 'recent', 'byRegion'), 'Tableau de bord', 'dashboard');
    }
}
