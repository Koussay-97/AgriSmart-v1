<?php
/**
 * Classe mère Utilisateur (diagramme de classes). Agriculteur et Administrateur en héritent.
 * MVC : un Modèle ne contient QUE des attributs privés/protégés + getters/setters (aucun SQL, aucune logique métier).
 * Toute la logique (requêtes SQL, hachage, sessions...) est dans controllers/UserController.php.
 */
class Utilisateur
{
    public const ROLE_AGRICULTEUR = 'agriculteur';
    public const ROLE_EXPERT      = 'expert';
    public const ROLE_ADMIN       = 'administrateur';
    public const ROLES            = [self::ROLE_AGRICULTEUR, self::ROLE_EXPERT, self::ROLE_ADMIN];

    protected int $id = 0;
    protected string $nom = '';
    protected string $email = '';
    protected string $motDePasse = '';
    protected bool $actif = true;
    protected ?string $derniereConnexion = null;
    protected string $createdAt = '';

    public function getId(): int { return $this->id; }
    public function getNom(): string { return $this->nom; }
    public function getEmail(): string { return $this->email; }
    public function getMotDePasse(): string { return $this->motDePasse; }
    public function isActif(): bool { return $this->actif; }
    public function getDerniereConnexion(): ?string { return $this->derniereConnexion; }
    public function getCreatedAt(): string { return $this->createdAt; }
    public function getRole(): string { return 'utilisateur'; }

    public function setId(int $id): void { $this->id = $id; }
    public function setNom(string $nom): void { $this->nom = $nom; }
    public function setEmail(string $email): void { $this->email = $email; }
    public function setMotDePasse(string $motDePasse): void { $this->motDePasse = $motDePasse; }
    public function setActif(bool $actif): void { $this->actif = $actif; }
    public function setDerniereConnexion(?string $d): void { $this->derniereConnexion = $d; }
    public function setCreatedAt(string $createdAt): void { $this->createdAt = $createdAt; }

    /** Transforme une ligne SQL en objet du bon type (Agriculteur, Expert ou Administrateur) selon la colonne "role". */
    public static function fromArray(array $r): Utilisateur
    {
        $role = $r['role'] ?? '';
        $u = $role === self::ROLE_ADMIN ? new Administrateur() : ($role === self::ROLE_EXPERT ? new Expert() : new Agriculteur());
        $u->setId((int) ($r['id'] ?? 0));
        $u->setNom((string) ($r['nom'] ?? ''));
        $u->setEmail((string) ($r['email'] ?? ''));
        $u->setMotDePasse((string) ($r['mot_de_passe'] ?? ''));
        $u->setActif((bool) ($r['actif'] ?? 1));
        $u->setDerniereConnexion($r['derniere_connexion'] ?? null);
        $u->setCreatedAt((string) ($r['created_at'] ?? ''));
        if ($u instanceof Agriculteur) {
            $u->setTelephone((string) ($r['telephone'] ?? ''));
            $u->setAdresse((string) ($r['adresse'] ?? ''));
            $u->setRegion((string) ($r['region'] ?? ''));
        } elseif ($u instanceof Expert) {
            $u->setSpecialite((string) ($r['specialite'] ?? ''));
            $u->setExperience((int) ($r['experience'] ?? 0));
            $u->setDisponibilite((bool) ($r['disponibilite'] ?? 1));
        } elseif ($u instanceof Administrateur) {
            $u->setNiveauAcces((int) ($r['niveau_acces'] ?? 1));
        }
        return $u;
    }
}
