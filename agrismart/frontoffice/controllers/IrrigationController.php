<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/CultureController.php';

/** Module Irrigation (Membre 3) : planifier puis enregistrer les irrigations des cultures de l'agriculteur connecté. */
class IrrigationController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnexion();
    }

    /** Liste + filtres (?culture=ID ?statut=planifiee|realisee) + total d'eau */
    public function index(): void
    {
        $user = $this->requireLogin();
        $cultureId = (int) ($_GET['culture'] ?? 0);
        $statut = (string) ($_GET['statut'] ?? '');
        if (!in_array($statut, Irrigation::STATUTS, true)) {
            $statut = '';
        }
        $where = 'WHERE e.agriculteur_id = :a';
        $params = ['a' => $user->getId()];
        if ($cultureId) {
            $where .= ' AND i.culture_id = :c';
            $params['c'] = $cultureId;
        }
        if ($statut !== '') {
            $where .= ' AND i.statut = :s';
            $params['s'] = $statut;
        }
        $stmt = $this->db->prepare($this->select() . $where . ' ORDER BY i.date_irrigation DESC, i.id DESC');
        $stmt->execute($params);
        $irrigations = array_map(fn(array $r) => Irrigation::fromArray($r), $stmt->fetchAll());

        $eauRealisee = 0.0;
        $nbPlanifiees = 0;
        foreach ($irrigations as $i) {
            if ($i->isRealisee()) {
                $eauRealisee += $i->getQuantiteEau();
            } else {
                $nbPlanifiees++;
            }
        }
        $cultures = $this->culturesOf($user->getId());
        $this->render('irrigations', compact('irrigations', 'cultures', 'cultureId', 'statut', 'eauRealisee', 'nbPlanifiees'));
    }

    /** Ajout (sans id) ou modification (?id=) ; ?culture=ID pré-sélectionne la culture */
    public function form(): void
    {
        $user = $this->requireLogin();
        $id = (int) ($_GET['id'] ?? 0);
        $irrigation = $id ? $this->findOwned($id, $user->getId()) : null;
        if ($id && !$irrigation) {
            flash('Irrigation introuvable.', 'danger');
            redirect('irrigations');
        }
        $cultures = $this->culturesOf($user->getId());
        if (!$cultures) {
            flash('Ajoutez d\'abord une culture : une irrigation concerne toujours une culture.', 'warning');
            redirect('cultures');
        }
        $preselect = (int) ($_GET['culture'] ?? 0);

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cultureId = (int) ($_POST['culture_id'] ?? 0);
            $date = trim((string) ($_POST['date_irrigation'] ?? ''));
            $quantite = trim(str_replace(',', '.', (string) ($_POST['quantite_eau'] ?? '')));
            $methode = (string) ($_POST['methode'] ?? '');
            $dureeRaw = trim((string) ($_POST['duree'] ?? ''));
            $statut = (string) ($_POST['statut'] ?? '');

            if (!in_array($cultureId, array_map(fn($c) => (int) $c['id'], $cultures), true)) {
                $errors['culture_id'] = 'Veuillez choisir une de vos cultures.';
            }
            if (!CultureController::isDate($date)) {
                $errors['date_irrigation'] = 'Date invalide.';
            }
            if (!preg_match('/^\d{1,6}(\.\d{1,2})?$/', $quantite) || (float) $quantite <= 0) {
                $errors['quantite_eau'] = 'Quantité d\'eau invalide (litres, nombre positif, 2 décimales maximum).';
            }
            if (!in_array($methode, Irrigation::METHODES, true)) {
                $errors['methode'] = 'Choisissez une méthode.';
            }
            if (!preg_match('/^\d{1,4}$/', $dureeRaw) || (int) $dureeRaw < 1 || (int) $dureeRaw > 1440) {
                $errors['duree'] = 'Durée : entier entre 1 et 1440 minutes.';
            }
            if (!in_array($statut, Irrigation::STATUTS, true)) {
                $errors['statut'] = 'Choisissez un statut.';
            }
            if (!isset($errors['date_irrigation']) && $statut === Irrigation::STATUT_REALISEE && $date > date('Y-m-d')) {
                $errors['date_irrigation'] = 'Une irrigation réalisée ne peut pas avoir une date dans le futur.';
            }
            if (!$errors) {
                if ($id) {
                    $stmt = $this->db->prepare('UPDATE irrigation SET culture_id = :c, date_irrigation = :d, quantite_eau = :q, methode = :m, duree = :u, statut = :s WHERE id = :id');
                    $stmt->execute(['c' => $cultureId, 'd' => $date, 'q' => $quantite, 'm' => $methode, 'u' => (int) $dureeRaw, 's' => $statut, 'id' => $id]);
                    flash('Irrigation modifiée.');
                } else {
                    $stmt = $this->db->prepare('INSERT INTO irrigation (culture_id, date_irrigation, quantite_eau, methode, duree, statut) VALUES (:c, :d, :q, :m, :u, :s)');
                    $stmt->execute(['c' => $cultureId, 'd' => $date, 'q' => $quantite, 'm' => $methode, 'u' => (int) $dureeRaw, 's' => $statut]);
                    flash($statut === Irrigation::STATUT_PLANIFIEE ? 'Irrigation planifiée.' : 'Irrigation enregistrée.');
                }
                redirect('irrigations');
            }
        }
        $this->render('irrigation_form', compact('irrigation', 'cultures', 'errors', 'preselect'));
    }

    /** Marque une irrigation planifiée comme réalisée (POST) */
    public function done(): void
    {
        $user = $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('irrigations');
        }
        $i = $this->findOwned((int) ($_POST['id'] ?? 0), $user->getId());
        if (!$i) {
            flash('Irrigation introuvable.', 'danger');
        } else {
            // si la date prévue est dans le futur, l'irrigation réalisée est datée d'aujourd'hui
            $stmt = $this->db->prepare("UPDATE irrigation SET statut = 'realisee', date_irrigation = LEAST(date_irrigation, CURDATE()) WHERE id = :id");
            $stmt->execute(['id' => $i->getId()]);
            flash('Irrigation marquée comme réalisée.');
        }
        redirect('irrigations');
    }

    public function delete(): void
    {
        $user = $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('irrigations');
        }
        $i = $this->findOwned((int) ($_POST['id'] ?? 0), $user->getId());
        if (!$i) {
            flash('Irrigation introuvable.', 'danger');
        } else {
            $stmt = $this->db->prepare('DELETE FROM irrigation WHERE id = :id');
            $stmt->execute(['id' => $i->getId()]);
            flash('Irrigation supprimée.');
        }
        redirect('irrigations');
    }

    // ---- Accès aux données ----
    private function select(): string
    {
        return 'SELECT i.*, c.nom AS culture_nom, p.nom AS parcelle_nom
                FROM irrigation i
                JOIN culture c ON c.id = i.culture_id
                JOIN parcelle p ON p.id = c.parcelle_id
                JOIN exploitation e ON e.id = p.exploitation_id ';
    }

    /** Irrigation appartenant à cet agriculteur (via culture -> parcelle -> exploitation), sinon null. */
    private function findOwned(int $id, int $agriculteurId): ?Irrigation
    {
        $stmt = $this->db->prepare($this->select() . 'WHERE i.id = :id AND e.agriculteur_id = :a');
        $stmt->execute(['id' => $id, 'a' => $agriculteurId]);
        $row = $stmt->fetch();
        return $row ? Irrigation::fromArray($row) : null;
    }

    /** Cultures de l'agriculteur (lignes SQL) pour les listes déroulantes. */
    private function culturesOf(int $agriculteurId): array
    {
        $stmt = $this->db->prepare('SELECT c.id, c.nom, p.nom AS parcelle_nom FROM culture c
                                    JOIN parcelle p ON p.id = c.parcelle_id JOIN exploitation e ON e.id = p.exploitation_id
                                    WHERE e.agriculteur_id = :a ORDER BY c.nom ASC, c.id ASC');
        $stmt->execute(['a' => $agriculteurId]);
        return $stmt->fetchAll();
    }
}
