<?php
/** Consultation : demande d'un agriculteur à un expert (diagramme de classes - Membre 2). Modèle pur. */
class Consultation
{
    public const STATUT_OUVERTE  = 'ouverte';    // soumise, aucun expert n'en est chargé
    public const STATUT_EN_COURS = 'en_cours';   // prise en charge par un expert
    public const STATUT_CLOTUREE = 'cloturee';
    public const STATUTS = [self::STATUT_OUVERTE, self::STATUT_EN_COURS, self::STATUT_CLOTUREE];
    public const LIBELLES = ['ouverte' => 'Ouverte', 'en_cours' => 'En cours', 'cloturee' => 'Clôturée'];
    public const COULEURS = ['ouverte' => 'warning', 'en_cours' => 'info', 'cloturee' => 'secondary'];

    private int $id = 0;
    private int $agriculteurId = 0;
    private ?int $expertId = null;
    private ?int $exploitationId = null;
    private string $sujet = '';
    private string $description = '';
    private string $dateDemande = '';
    private string $statut = self::STATUT_OUVERTE;
    // Données d'affichage issues de jointures (non stockées dans la table consultation)
    private string $agriculteurNom = '';
    private string $expertNom = '';
    private string $exploitationNom = '';
    private int $nbConseils = 0;

    public function getId(): int { return $this->id; }
    public function getAgriculteurId(): int { return $this->agriculteurId; }
    public function getExpertId(): ?int { return $this->expertId; }
    public function getExploitationId(): ?int { return $this->exploitationId; }
    public function getSujet(): string { return $this->sujet; }
    public function getDescription(): string { return $this->description; }
    public function getDateDemande(): string { return $this->dateDemande; }
    public function getStatut(): string { return $this->statut; }
    public function getAgriculteurNom(): string { return $this->agriculteurNom; }
    public function getExpertNom(): string { return $this->expertNom; }
    public function getExploitationNom(): string { return $this->exploitationNom; }
    public function getNbConseils(): int { return $this->nbConseils; }
    public function getLibelle(): string { return self::LIBELLES[$this->statut] ?? $this->statut; }
    public function getCouleur(): string { return self::COULEURS[$this->statut] ?? 'secondary'; }

    public function setId(int $v): void { $this->id = $v; }
    public function setAgriculteurId(int $v): void { $this->agriculteurId = $v; }
    public function setExpertId(?int $v): void { $this->expertId = $v; }
    public function setExploitationId(?int $v): void { $this->exploitationId = $v; }
    public function setSujet(string $v): void { $this->sujet = $v; }
    public function setDescription(string $v): void { $this->description = $v; }
    public function setDateDemande(string $v): void { $this->dateDemande = $v; }
    public function setStatut(string $v): void { $this->statut = $v; }
    public function setAgriculteurNom(string $v): void { $this->agriculteurNom = $v; }
    public function setExpertNom(string $v): void { $this->expertNom = $v; }
    public function setExploitationNom(string $v): void { $this->exploitationNom = $v; }
    public function setNbConseils(int $v): void { $this->nbConseils = $v; }

    public static function fromArray(array $r): self
    {
        $c = new self();
        $c->setId((int) ($r['id'] ?? 0));
        $c->setAgriculteurId((int) ($r['agriculteur_id'] ?? 0));
        $c->setExpertId(isset($r['expert_id']) ? (int) $r['expert_id'] : null);
        $c->setExploitationId(isset($r['exploitation_id']) ? (int) $r['exploitation_id'] : null);
        $c->setSujet((string) ($r['sujet'] ?? ''));
        $c->setDescription((string) ($r['description'] ?? ''));
        $c->setDateDemande((string) ($r['date_demande'] ?? ''));
        $c->setStatut((string) ($r['statut'] ?? self::STATUT_OUVERTE));
        $c->setAgriculteurNom((string) ($r['agriculteur_nom'] ?? ''));
        $c->setExpertNom((string) ($r['expert_nom'] ?? ''));
        $c->setExploitationNom((string) ($r['exploitation_nom'] ?? ''));
        $c->setNbConseils((int) ($r['nb_conseils'] ?? 0));
        return $c;
    }
}
