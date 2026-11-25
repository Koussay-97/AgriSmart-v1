<?php
/**
 * controllers/UserController.php
 * Contrôleur du module "Gestion des utilisateurs", PARTAGÉ par le Front Office et le Back Office.
 * MVC : c'est ici (et pas dans les modèles ni dans les vues) que se trouvent TOUTE la logique métier,
 * les requêtes SQL préparées, le hachage des mots de passe, les sessions, la validation et les contrôles d'accès.
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/bootstrap.php';

class UserController
{
    /** Hachage factice : permet de faire un password_verify même si l'email n'existe pas (évite de révéler quels emails existent par le temps de réponse). */
    private const DUMMY_HASH = '$2y$12$TiqTxL6rFVbf9iENBpU8MOtCIkrrU0etwJPmsAitVfkdCuoEFX5MG';

    private PDO $db;
    private array $config;
    private ?Utilisateur $current = null;
    private bool $currentLoaded = false;

    public function __construct()
    {
        $this->db = Database::getConnexion();
        $this->config = require __DIR__ . '/../config/config.php';
    }

    // ==============================================================
    // ÉTAT DE SESSION, AUTHENTIFICATION ET AUTORISATION
    // ==============================================================
    public static function isAuthenticated(): bool
    {
        return !empty($_SESSION['user_id']);
    }

    /**
     * Utilisateur connecté, relu en base À CHAQUE REQUÊTE : si son compte a été supprimé, bloqué ou si son rôle a changé,
     * la décision d'accès tient compte de l'état réel (et pas seulement de ce qui était vrai au moment de la connexion).
     */
    public function currentUser(): ?Utilisateur
    {
        if (!self::isAuthenticated()) {
            return null;
        }
        if (!$this->currentLoaded) {
            $this->currentLoaded = true;
            $this->current = $this->findById((int) $_SESSION['user_id']);
            if ($this->current === null || !$this->current->isActif()) {
                $this->current = null;
                $this->logout();
                flash('Votre session n\'est plus valide. Veuillez vous reconnecter.', 'warning');
            }
        }
        return $this->current;
    }

    /** Redirige vers la connexion si personne n'est connecté. Retourne l'utilisateur connecté. */
    public function requireAuth(): Utilisateur
    {
        $user = $this->currentUser();
        if ($user === null) {
            if (!isset($_SESSION['flash'])) {
                flash('Veuillez vous connecter pour accéder à cette page.', 'warning');
            }
            redirect('login');
        }
        return $user;
    }

    /** Autorisation : erreur 403 si l'utilisateur connecté n'a pas le rôle exigé. À appeler sur CHAQUE page sensible. */
    public function requireRole(string $role): Utilisateur
    {
        $user = $this->requireAuth();
        if ($user->getRole() !== $role) {
            abortWith(403, 'Accès refusé', 'Vous n\'avez pas les permissions nécessaires pour accéder à cette page.', 'login');
        }
        return $user;
    }

    /**
     * Connexion. $allowedRoles = rôle (ou liste de rôles) autorisé dans l'espace appelant
     * (FO : agriculteur + expert, BO : administrateur).
     * @param string|array $allowedRoles
     * @return array erreurs (vide = succès)
     */
    public function login(array $post, $allowedRoles): array
    {
        $allowedRoles = (array) $allowedRoles;
        $email = mb_strtolower(trim((string) ($post['email'] ?? '')));
        $password = (string) ($post['password'] ?? '');

        $errors = [];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email invalide.';
        }
        if ($password === '') {
            $errors['password'] = 'Mot de passe obligatoire.';
        }
        if ($errors) {
            return $errors;
        }

        if ($this->isLockedOut($email)) {
            return ['global' => 'Trop de tentatives échouées. Réessayez dans ' . (int) $this->config['lockout_minutes'] . ' minutes.'];
        }

        $user = $this->findByEmail($email);
        $valid = password_verify($password, $user ? $user->getMotDePasse() : self::DUMMY_HASH);
        if ($user === null || !$valid) {
            $this->recordFailedAttempt($email);
            return ['global' => 'Email ou mot de passe incorrect.']; // message volontairement générique
        }
        if (!$user->isActif()) {
            return ['global' => 'Ce compte est désactivé. Contactez un administrateur.'];
        }
        if (!in_array($user->getRole(), $allowedRoles, true)) {
            return ['global' => in_array(Utilisateur::ROLE_ADMIN, $allowedRoles, true)
                ? 'Accès réservé aux administrateurs.'
                : 'Ce compte est un compte administrateur : connectez-vous depuis le Back Office.'];
        }

        // Succès
        $stmt = $this->db->prepare('DELETE FROM tentative_connexion WHERE email = :email');
        $stmt->execute(['email' => $email]);

        if (password_needs_rehash($user->getMotDePasse(), PASSWORD_DEFAULT)) {
            $this->updatePasswordHash($user->getId(), password_hash($password, PASSWORD_DEFAULT));
        }
        $stmt = $this->db->prepare('UPDATE utilisateur SET derniere_connexion = NOW() WHERE id = :id');
        $stmt->execute(['id' => $user->getId()]);

        session_regenerate_id(true);      // protège contre la fixation de session
        unset($_SESSION['csrf_token']);   // nouveau jeton CSRF pour la nouvelle session
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['last_activity'] = time();
        return [];
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        session_start(); // nouvelle session vierge (permet d'afficher un message flash après la déconnexion)
        $this->current = null;
        $this->currentLoaded = false;
    }

    // ---- Protection contre la force brute ----
    private function isLockedOut(string $email): bool
    {
        $minutes = (int) $this->config['lockout_minutes'];
        $max = (int) $this->config['max_login_tries'];
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM tentative_connexion WHERE email = :email AND created_at > (NOW() - INTERVAL ' . $minutes . ' MINUTE)');
        $stmt->execute(['email' => $email]);
        if ((int) $stmt->fetchColumn() >= $max) {
            return true;
        }
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM tentative_connexion WHERE ip = :ip AND created_at > (NOW() - INTERVAL ' . $minutes . ' MINUTE)');
        $stmt->execute(['ip' => $this->clientIp()]);
        return (int) $stmt->fetchColumn() >= $max * 4;
    }

    private function recordFailedAttempt(string $email): void
    {
        $stmt = $this->db->prepare('INSERT INTO tentative_connexion (email, ip) VALUES (:email, :ip)');
        $stmt->execute(['email' => $email, 'ip' => $this->clientIp()]);
    }

    private function clientIp(): string
    {
        return substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45);
    }

    // ==============================================================
    // ACCÈS AUX DONNÉES (requêtes préparées : protection contre l'injection SQL)
    // ==============================================================
    public function findById(int $id): ?Utilisateur
    {
        $stmt = $this->db->prepare('SELECT * FROM utilisateur WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? Utilisateur::fromArray($row) : null;
    }

    public function findByEmail(string $email): ?Utilisateur
    {
        $stmt = $this->db->prepare('SELECT * FROM utilisateur WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        return $row ? Utilisateur::fromArray($row) : null;
    }

    private function emailExists(string $email, int $exceptId = 0): bool
    {
        $stmt = $this->db->prepare('SELECT id FROM utilisateur WHERE email = :email AND id <> :id LIMIT 1');
        $stmt->execute(['email' => $email, 'id' => $exceptId]);
        return (bool) $stmt->fetch();
    }

    private function updatePasswordHash(int $id, string $hash): void
    {
        $stmt = $this->db->prepare('UPDATE utilisateur SET mot_de_passe = :h WHERE id = :id');
        $stmt->execute(['h' => $hash, 'id' => $id]);
    }

    /** Vrai si $id est le dernier administrateur actif (on ne doit jamais le supprimer, le bloquer ni le rétrograder). */
    private function isLastActiveAdmin(int $id): bool
    {
        $target = $this->findById($id);
        if ($target === null || $target->getRole() !== Utilisateur::ROLE_ADMIN || !$target->isActif()) {
            return false;
        }
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM utilisateur WHERE role = :role AND actif = 1 AND id <> :id');
        $stmt->execute(['role' => Utilisateur::ROLE_ADMIN, 'id' => $id]);
        return (int) $stmt->fetchColumn() === 0;
    }

    // ==============================================================
    // VALIDATION DES SAISIES (côté serveur : indispensable, le JavaScript n'est qu'un confort)
    // ==============================================================
    /**
     * @return array [données nettoyées, erreurs]
     */
    private function validateProfile(array $post, string $role): array
    {
        $isAgriculteur = $role === Utilisateur::ROLE_AGRICULTEUR;
        $d = [
            'nom'       => trim((string) ($post['nom'] ?? '')),
            'email'     => mb_strtolower(trim((string) ($post['email'] ?? ''))),
            'telephone' => trim((string) ($post['telephone'] ?? '')),
            'region'    => trim((string) ($post['region'] ?? '')),
            'adresse'   => trim((string) ($post['adresse'] ?? '')),
            'specialite'    => '',
            'experience'    => 0,
            'disponibilite' => true,
        ];
        $errors = [];

        $len = mb_strlen($d['nom']);
        if ($len < 3 || $len > 100) {
            $errors['nom'] = 'Le nom doit contenir entre 3 et 100 caractères.';
        }
        if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($d['email']) > 150) {
            $errors['email'] = 'Email invalide.';
        }
        if ($isAgriculteur) {
            if (!preg_match('/^[0-9 +]{8,15}$/', $d['telephone'])) {
                $errors['telephone'] = 'Téléphone invalide (8 à 15 chiffres).';
            }
            if (!in_array($d['region'], Agriculteur::REGIONS, true)) {
                $errors['region'] = 'Veuillez choisir une région.';
            }
        } else {
            if ($d['telephone'] !== '' && !preg_match('/^[0-9 +]{8,15}$/', $d['telephone'])) {
                $errors['telephone'] = 'Téléphone invalide (8 à 15 chiffres).';
            }
            if ($d['region'] !== '' && !in_array($d['region'], Agriculteur::REGIONS, true)) {
                $errors['region'] = 'Région invalide.';
            }
        }
        if (mb_strlen($d['adresse']) > 255) {
            $errors['adresse'] = 'Adresse trop longue (255 caractères maximum).';
        }
        if ($role === Utilisateur::ROLE_EXPERT) {
            $d['specialite'] = trim((string) ($post['specialite'] ?? ''));
            $len = mb_strlen($d['specialite']);
            if ($len < 3 || $len > 100) {
                $errors['specialite'] = 'La spécialité doit contenir entre 3 et 100 caractères.';
            }
            $exp = trim((string) ($post['experience'] ?? ''));
            if (!preg_match('/^\d{1,2}$/', $exp) || (int) $exp > 60) {
                $errors['experience'] = 'Expérience : nombre d\'années entre 0 et 60.';
            } else {
                $d['experience'] = (int) $exp;
            }
            $d['disponibilite'] = !empty($post['disponibilite']);
        }
        return [$d, $errors];
    }

    private function passwordErrors(string $password, string $confirm): array
    {
        $errors = [];
        if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $errors['password'] = 'Mot de passe : 8 caractères minimum, avec au moins une lettre et un chiffre.';
        } elseif (strlen($password) > 72) {
            $errors['password'] = 'Mot de passe trop long (72 caractères maximum).';
        }
        if ($password !== $confirm) {
            $errors['confirm'] = 'Les mots de passe ne correspondent pas.';
        }
        return $errors;
    }

    // ==============================================================
    // INSCRIPTION PUBLIQUE (Front Office)
    // ==============================================================
    /** @return array erreurs (vide = compte créé). Le rôle est FORCÉ à 'agriculteur' : aucune auto-promotion possible. */
    public function register(array $post): array
    {
        [$d, $errors] = $this->validateProfile($post, Utilisateur::ROLE_AGRICULTEUR);
        $errors += $this->passwordErrors((string) ($post['password'] ?? ''), (string) ($post['confirm'] ?? ''));
        if (!$errors && $this->emailExists($d['email'])) {
            $errors['email'] = 'Cet email est déjà utilisé.';
        }
        if ($errors) {
            return $errors;
        }
        $this->insertUser($d, (string) $post['password'], Utilisateur::ROLE_AGRICULTEUR, true, 1);
        return [];
    }

    private function insertUser(array $d, string $password, string $role, bool $actif, int $niveau): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO utilisateur (nom, email, mot_de_passe, role, telephone, adresse, region, niveau_acces, actif, specialite, experience, disponibilite)
             VALUES (:nom, :email, :pwd, :role, :tel, :adr, :region, :niveau, :actif, :spec, :exp, :dispo)'
        );
        $stmt->execute([
            'nom' => $d['nom'], 'email' => $d['email'],
            'pwd' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role, 'tel' => $d['telephone'], 'adr' => $d['adresse'], 'region' => $d['region'],
            'niveau' => $niveau, 'actif' => $actif ? 1 : 0,
            'spec' => $d['specialite'], 'exp' => $d['experience'], 'dispo' => $d['disponibilite'] ? 1 : 0,
        ]);
    }

    // ==============================================================
    // CRUD ADMINISTRATEUR (Back Office)
    // ==============================================================
    /**
     * Liste paginée avec recherche et filtres.
     * @param array $f filtres : q, role, region, actif
     * @return array ['users' => Utilisateur[], 'total' => int, 'pages' => int, 'page' => int]
     */
    public function search(array $f, int $page, int $perPage = 10): array
    {
        $where = [];
        $params = [];
        $q = trim((string) ($f['q'] ?? ''));
        if ($q !== '') {
            $where[] = '(nom LIKE :q1 OR email LIKE :q2)';
            $like = '%' . addcslashes($q, '%_\\') . '%';
            $params['q1'] = $like;
            $params['q2'] = $like;
        }
        $role = (string) ($f['role'] ?? '');
        if (in_array($role, Utilisateur::ROLES, true)) {
            $where[] = 'role = :role';
            $params['role'] = $role;
        }
        $region = (string) ($f['region'] ?? '');
        if ($region !== '') {
            $where[] = 'region = :region';
            $params['region'] = $region;
        }
        $actif = (string) ($f['actif'] ?? '');
        if ($actif === '0' || $actif === '1') {
            $where[] = 'actif = :actif';
            $params['actif'] = (int) $actif;
        }
        $sqlWhere = $where ? ' WHERE ' . implode(' AND ', $where) : '';

        $stmt = $this->db->prepare('SELECT COUNT(*) FROM utilisateur' . $sqlWhere);
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();

        $perPage = max(1, $perPage);
        $pages = max(1, (int) ceil($total / $perPage));
        $page = min(max(1, $page), $pages);
        $offset = ($page - 1) * $perPage; // entiers : insérés directement (LIMIT n'accepte pas de paramètre texte)

        $stmt = $this->db->prepare('SELECT * FROM utilisateur' . $sqlWhere . ' ORDER BY id ASC LIMIT ' . $perPage . ' OFFSET ' . $offset);
        $stmt->execute($params);
        $users = array_map(fn(array $r) => Utilisateur::fromArray($r), $stmt->fetchAll());

        return ['users' => $users, 'total' => $total, 'pages' => $pages, 'page' => $page];
    }

    public function countAll(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM utilisateur')->fetchColumn();
    }

    public function countByRole(string $role): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM utilisateur WHERE role = :role');
        $stmt->execute(['role' => $role]);
        return (int) $stmt->fetchColumn();
    }

    /** @return Expert[] experts actifs (pour l'affectation des consultations) */
    public function activeExperts(): array
    {
        $stmt = $this->db->prepare('SELECT * FROM utilisateur WHERE role = :role AND actif = 1 ORDER BY nom ASC');
        $stmt->execute(['role' => Utilisateur::ROLE_EXPERT]);
        return array_map(fn(array $r) => Utilisateur::fromArray($r), $stmt->fetchAll());
    }

    public function countBlocked(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM utilisateur WHERE actif = 0')->fetchColumn();
    }

    /** @return Utilisateur[] */
    public function recent(int $limit = 5): array
    {
        $stmt = $this->db->query('SELECT * FROM utilisateur ORDER BY created_at DESC, id DESC LIMIT ' . max(1, $limit));
        return array_map(fn(array $r) => Utilisateur::fromArray($r), $stmt->fetchAll());
    }

    /** @return array [région => nombre d'agriculteurs] */
    public function countByRegion(): array
    {
        $stmt = $this->db->prepare("SELECT region, COUNT(*) AS n FROM utilisateur WHERE role = :role AND region <> '' GROUP BY region ORDER BY n DESC, region ASC");
        $stmt->execute(['role' => Utilisateur::ROLE_AGRICULTEUR]);
        $out = [];
        foreach ($stmt->fetchAll() as $r) {
            $out[$r['region']] = (int) $r['n'];
        }
        return $out;
    }

    /** Création d'un utilisateur par un administrateur (rôle choisi dans la liste fermée des rôles). */
    public function createUser(array $post): array
    {
        $role = (string) ($post['role'] ?? '');
        $errors = [];
        if (!in_array($role, Utilisateur::ROLES, true)) {
            $errors['role'] = 'Rôle invalide.';
        }
        [$d, $e] = $this->validateProfile($post, $role);
        $errors += $e;
        $errors += $this->passwordErrors((string) ($post['password'] ?? ''), (string) ($post['confirm'] ?? ''));
        if (!isset($errors['email']) && $this->emailExists($d['email'])) {
            $errors['email'] = 'Cet email est déjà utilisé.';
        }
        if ($errors) {
            return $errors;
        }
        $this->insertUser($d, (string) $post['password'], $role, !empty($post['actif']), 1);
        return [];
    }

    /** Modification complète d'un utilisateur par un administrateur ($actorId = administrateur connecté). */
    public function updateUser(int $id, array $post, int $actorId): array
    {
        $target = $this->findById($id);
        if ($target === null) {
            return ['global' => 'Utilisateur introuvable.'];
        }
        $role = (string) ($post['role'] ?? '');
        $actif = !empty($post['actif']);
        $errors = [];
        if (!in_array($role, Utilisateur::ROLES, true)) {
            $errors['role'] = 'Rôle invalide.';
        }
        [$d, $e] = $this->validateProfile($post, $role);
        $errors += $e;

        $password = (string) ($post['password'] ?? '');
        if ($password !== '' || (string) ($post['confirm'] ?? '') !== '') {
            $errors += $this->passwordErrors($password, (string) ($post['confirm'] ?? ''));
        }
        if (!isset($errors['email']) && $this->emailExists($d['email'], $id)) {
            $errors['email'] = 'Cet email est déjà utilisé.';
        }
        if (!$errors) {
            $errors += $this->roleStatusGuards($target, $role, $actif, $actorId);
        }
        if ($errors) {
            return $errors;
        }

        $sql = 'UPDATE utilisateur SET nom = :nom, email = :email, role = :role, telephone = :tel, adresse = :adr, region = :region, actif = :actif,
                specialite = :spec, experience = :exp, disponibilite = :dispo';
        $params = ['nom' => $d['nom'], 'email' => $d['email'], 'role' => $role, 'tel' => $d['telephone'],
            'adr' => $d['adresse'], 'region' => $d['region'], 'actif' => $actif ? 1 : 0,
            'spec' => $d['specialite'], 'exp' => $d['experience'], 'dispo' => $d['disponibilite'] ? 1 : 0, 'id' => $id];
        if ($password !== '') {
            $sql .= ', mot_de_passe = :pwd';
            $params['pwd'] = password_hash($password, PASSWORD_DEFAULT);
        }
        $stmt = $this->db->prepare($sql . ' WHERE id = :id');
        $stmt->execute($params);
        return [];
    }

    /** Règles de sécurité sur le rôle et le statut : un admin ne peut pas se rétrograder/bloquer, et le dernier admin actif est intouchable. */
    private function roleStatusGuards(Utilisateur $target, string $newRole, bool $newActif, int $actorId): array
    {
        $roleChanged = $newRole !== $target->getRole();
        $deactivated = $target->isActif() && !$newActif;
        if ($target->getId() === $actorId && $roleChanged) {
            return ['role' => 'Vous ne pouvez pas modifier votre propre rôle.'];
        }
        if ($target->getId() === $actorId && $deactivated) {
            return ['global' => 'Vous ne pouvez pas désactiver votre propre compte.'];
        }
        if (($roleChanged || $deactivated) && $this->isLastActiveAdmin($target->getId())) {
            return ['global' => 'Impossible : c\'est le dernier administrateur actif.'];
        }
        return [];
    }

    /** Si un expert disparaît, ses consultations en cours redeviennent "ouvertes" (un autre expert pourra les prendre en charge). */
    private function releaseConsultations(int $expertId): void
    {
        $stmt = $this->db->prepare("UPDATE consultation SET statut = 'ouverte', expert_id = NULL WHERE expert_id = :id AND statut = 'en_cours'");
        $stmt->execute(['id' => $expertId]);
    }

    /** Suppression. @return ?string message d'erreur, null si supprimé */
    public function deleteUser(int $id, int $actorId): ?string
    {
        $target = $this->findById($id);
        if ($target === null) {
            return 'Utilisateur introuvable.';
        }
        if ($id === $actorId) {
            return 'Vous ne pouvez pas supprimer votre propre compte depuis le Back Office.';
        }
        if ($this->isLastActiveAdmin($id)) {
            return 'Impossible de supprimer le dernier administrateur actif.';
        }
        $this->releaseConsultations($id);
        $stmt = $this->db->prepare('DELETE FROM utilisateur WHERE id = :id'); // exploitations, parcelles, consultations : ON DELETE CASCADE
        $stmt->execute(['id' => $id]);
        return null;
    }

    /** Changement rapide de rôle. @return ?string erreur */
    public function setRole(int $id, string $role, int $actorId): ?string
    {
        $target = $this->findById($id);
        if ($target === null) {
            return 'Utilisateur introuvable.';
        }
        if (!in_array($role, Utilisateur::ROLES, true)) {
            return 'Rôle invalide.';
        }
        $err = $this->roleStatusGuards($target, $role, $target->isActif(), $actorId);
        if ($err) {
            return reset($err);
        }
        $stmt = $this->db->prepare('UPDATE utilisateur SET role = :role WHERE id = :id');
        $stmt->execute(['role' => $role, 'id' => $id]);
        if ($role === Utilisateur::ROLE_EXPERT) {   // un expert doit avoir une spécialité : valeur par défaut modifiable ensuite
            $stmt = $this->db->prepare("UPDATE utilisateur SET specialite = 'Généraliste' WHERE id = :id AND specialite = ''");
            $stmt->execute(['id' => $id]);
        }
        return null;
    }

    /** Active / bloque un compte. @return ?string erreur */
    public function setActive(int $id, bool $actif, int $actorId): ?string
    {
        $target = $this->findById($id);
        if ($target === null) {
            return 'Utilisateur introuvable.';
        }
        $err = $this->roleStatusGuards($target, $target->getRole(), $actif, $actorId);
        if ($err) {
            return reset($err);
        }
        $stmt = $this->db->prepare('UPDATE utilisateur SET actif = :actif WHERE id = :id');
        $stmt->execute(['actif' => $actif ? 1 : 0, 'id' => $id]);
        return null;
    }

    // ==============================================================
    // MON PROFIL (agriculteur en FO, administrateur en BO)
    // ==============================================================
    public function updateOwnProfile(Utilisateur $user, array $post): array
    {
        $role = $user->getRole();
        [$d, $errors] = $this->validateProfile($post, $role);
        if (!isset($errors['email']) && $this->emailExists($d['email'], $user->getId())) {
            $errors['email'] = 'Cet email est déjà utilisé.';
        }
        if ($errors) {
            return $errors;
        }
        if ($role === Utilisateur::ROLE_AGRICULTEUR) {
            $stmt = $this->db->prepare('UPDATE utilisateur SET nom = :nom, email = :email, telephone = :tel, adresse = :adr, region = :region WHERE id = :id');
            $stmt->execute(['nom' => $d['nom'], 'email' => $d['email'], 'tel' => $d['telephone'], 'adr' => $d['adresse'], 'region' => $d['region'], 'id' => $user->getId()]);
        } elseif ($role === Utilisateur::ROLE_EXPERT) {
            $stmt = $this->db->prepare('UPDATE utilisateur SET nom = :nom, email = :email, specialite = :spec, experience = :exp, disponibilite = :dispo WHERE id = :id');
            $stmt->execute(['nom' => $d['nom'], 'email' => $d['email'], 'spec' => $d['specialite'], 'exp' => $d['experience'], 'dispo' => $d['disponibilite'] ? 1 : 0, 'id' => $user->getId()]);
        } else {
            $stmt = $this->db->prepare('UPDATE utilisateur SET nom = :nom, email = :email WHERE id = :id');
            $stmt->execute(['nom' => $d['nom'], 'email' => $d['email'], 'id' => $user->getId()]);
        }
        return [];
    }

    public function changePassword(Utilisateur $user, array $post): array
    {
        $current = (string) ($post['current_password'] ?? '');
        $new = (string) ($post['password'] ?? '');
        $errors = [];
        if (!password_verify($current, $user->getMotDePasse())) {
            $errors['current_password'] = 'Mot de passe actuel incorrect.';
        }
        $errors += $this->passwordErrors($new, (string) ($post['confirm'] ?? ''));
        if (!$errors && $new === $current) {
            $errors['password'] = 'Le nouveau mot de passe doit être différent de l\'ancien.';
        }
        if ($errors) {
            return $errors;
        }
        $this->updatePasswordHash($user->getId(), password_hash($new, PASSWORD_DEFAULT));
        session_regenerate_id(true); // changement de privilège sensible : nouvel identifiant de session
        return [];
    }

    /** Suppression de son propre compte (confirmée par le mot de passe). @return ?string erreur */
    public function deleteOwnAccount(Utilisateur $user, string $password): ?string
    {
        if (!password_verify($password, $user->getMotDePasse())) {
            return 'Mot de passe incorrect : le compte n\'a pas été supprimé.';
        }
        if ($this->isLastActiveAdmin($user->getId())) {
            return 'Impossible de supprimer le dernier administrateur actif.';
        }
        $this->releaseConsultations($user->getId());
        $stmt = $this->db->prepare('DELETE FROM utilisateur WHERE id = :id');
        $stmt->execute(['id' => $user->getId()]);
        $this->logout();
        return null;
    }
}
