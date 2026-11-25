<?php
require_once __DIR__ . '/../../config/database.php';

/** Module Cultures (Membre 3) : CRUD des cultures plantées sur les parcelles de l'agriculteur connecté. */
class CultureController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnexion();
    }

    /** Liste des cultures (filtre optionnel ?parcelle=ID) */
    public function index(): void
    {
        $user = $this->requireLogin();
        $parcelleId = (int) ($_GET['parcelle'] ?? 0);
        $sql = $this->select() . 'WHERE e.agriculteur_id = :a';
        $params = ['a' => $user->getId()];
        if ($parcelleId) {
            $sql .= ' AND c.parcelle_id = :p';
            $params['p'] = $parcelleId;
        }
        $stmt = $this->db->prepare($sql . ' ORDER BY c.date_semis DESC, c.id DESC');
        $stmt->execute($params);
        $cultures = array_map(fn(array $r) => Culture::fromArray($r), $stmt->fetchAll());
        $parcelles = $this->parcellesOf($user->getId());
        $this->render('cultures', compact('cultures', 'parcelles', 'parcelleId'));
    }

    /** Ajout (sans id) ou modification (?id=) */
    public function form(): void
    {
        $user = $this->requireLogin();
        $id = (int) ($_GET['id'] ?? 0);
        $culture = $id ? $this->findOwned($id, $user->getId()) : null;
        if ($id && !$culture) {
            flash('Culture introuvable.', 'danger');
            redirect('cultures');
        }
        $parcelles = $this->parcellesOf($user->getId());
        if (!$parcelles) {
            flash('Ajoutez d\'abord une exploitation et une parcelle : une culture est toujours plantée sur une parcelle.', 'warning');
            redirect('exploitations');
        }

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim((string) ($_POST['nom'] ?? ''));
            $parcelleId = (int) ($_POST['parcelle_id'] ?? 0);
            $semis = trim((string) ($_POST['date_semis'] ?? ''));
            $recolte = trim((string) ($_POST['date_recolte'] ?? ''));

            $len = mb_strlen($nom);
            if ($len < 2 || $len > 100) {
                $errors['nom'] = 'Le nom doit contenir entre 2 et 100 caractères.';
            }
            if (!in_array($parcelleId, array_map(fn($p) => (int) $p['id'], $parcelles), true)) {
                $errors['parcelle_id'] = 'Veuillez choisir une de vos parcelles.';
            }
            if (!self::isDate($semis)) {
                $errors['date_semis'] = 'Date de semis invalide.';
            }
            if ($recolte !== '') {
                if (!self::isDate($recolte)) {
                    $errors['date_recolte'] = 'Date de récolte invalide.';
                } elseif (self::isDate($semis) && $recolte < $semis) {
                    $errors['date_recolte'] = 'La récolte ne peut pas précéder le semis.';
                }
            }
            if (!$errors) {
                $recolteSql = $recolte !== '' ? $recolte : null;
                if ($id) {
                    $stmt = $this->db->prepare('UPDATE culture SET parcelle_id = :p, nom = :n, date_semis = :s, date_recolte = :r WHERE id = :id');
                    $stmt->execute(['p' => $parcelleId, 'n' => $nom, 's' => $semis, 'r' => $recolteSql, 'id' => $id]);
                    flash('Culture modifiée.');
                } else {
                    $stmt = $this->db->prepare('INSERT INTO culture (parcelle_id, nom, date_semis, date_recolte) VALUES (:p, :n, :s, :r)');
                    $stmt->execute(['p' => $parcelleId, 'n' => $nom, 's' => $semis, 'r' => $recolteSql]);
                    flash('Culture ajoutée.');
                }
                redirect('cultures');
            }
        }
        $this->render('culture_form', compact('culture', 'parcelles', 'errors'));
    }

    /** Suppression (POST + CSRF) : les irrigations de la culture sont supprimées en cascade. */
    public function delete(): void
    {
        $user = $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('cultures');
        }
        $culture = $this->findOwned((int) ($_POST['id'] ?? 0), $user->getId());
        if (!$culture) {
            flash('Culture introuvable.', 'danger');
        } else {
            $stmt = $this->db->prepare('DELETE FROM culture WHERE id = :id');
            $stmt->execute(['id' => $culture->getId()]);
            flash('Culture supprimée (ainsi que ses irrigations).');
        }
        redirect('cultures');
    }

    // ---- Accès aux données ----
    private function select(): string
    {
        return "SELECT c.*, p.nom AS parcelle_nom, e.nom AS exploitation_nom,
                       COALESCE((SELECT SUM(i.quantite_eau) FROM irrigation i WHERE i.culture_id = c.id AND i.statut = 'realisee'), 0) AS eau_totale
                FROM culture c
                JOIN parcelle p ON p.id = c.parcelle_id
                JOIN exploitation e ON e.id = p.exploitation_id ";
    }

    /** Culture appartenant à cet agriculteur (via sa parcelle puis son exploitation), sinon null. */
    private function findOwned(int $id, int $agriculteurId): ?Culture
    {
        $stmt = $this->db->prepare($this->select() . 'WHERE c.id = :id AND e.agriculteur_id = :a');
        $stmt->execute(['id' => $id, 'a' => $agriculteurId]);
        $row = $stmt->fetch();
        return $row ? Culture::fromArray($row) : null;
    }

    /** Parcelles de l'agriculteur (lignes SQL) pour les listes déroulantes. */
    private function parcellesOf(int $agriculteurId): array
    {
        $stmt = $this->db->prepare('SELECT p.id, p.nom, e.nom AS exploitation_nom FROM parcelle p JOIN exploitation e ON e.id = p.exploitation_id
                                    WHERE e.agriculteur_id = :a ORDER BY e.nom ASC, p.nom ASC');
        $stmt->execute(['a' => $agriculteurId]);
        return $stmt->fetchAll();
    }

    public static function isDate(string $s): bool
    {
        $d = DateTime::createFromFormat('Y-m-d', $s);
        return $d !== false && $d->format('Y-m-d') === $s;
    }
}
