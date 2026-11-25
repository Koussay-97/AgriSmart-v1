<?php
/** Irrigation d'une culture (diagramme de classes - Membre 3) : planifiée puis réalisée. Modèle pur. */
class Irrigation
{
    public const STATUT_PLANIFIEE = 'planifiee';
    public const STATUT_REALISEE  = 'realisee';
    public const STATUTS = [self::STATUT_PLANIFIEE, self::STATUT_REALISEE];
    public const LIBELLES = ['planifiee' => 'Planifiée', 'realisee' => 'Réalisée'];
    public const METHODES = ['Goutte-à-goutte', 'Aspersion', 'Gravitaire', 'Manuelle'];

    private int $id = 0;
    private int $cultureId = 0;
    private string $date = '';
    private float $quantiteEau = 0.0;   // litres
    private string $methode = '';
    private int $duree = 0;             // minutes
    private string $statut = self::STATUT_PLANIFIEE;
    private string $cultureNom = '';
    private string $parcelleNom = '';

    public function getId(): int { return $this->id; }
    public function getCultureId(): int { return $this->cultureId; }
    public function getDate(): string { return $this->date; }
    public function getQuantiteEau(): float { return $this->quantiteEau; }
    public function getMethode(): string { return $this->methode; }
    public function getDuree(): int { return $this->duree; }
    public function getStatut(): string { return $this->statut; }
    public function getCultureNom(): string { return $this->cultureNom; }
    public function getParcelleNom(): string { return $this->parcelleNom; }
    public function isRealisee(): bool { return $this->statut === self::STATUT_REALISEE; }

    public function setId(int $v): void { $this->id = $v; }
    public function setCultureId(int $v): void { $this->cultureId = $v; }
    public function setDate(string $v): void { $this->date = $v; }
    public function setQuantiteEau(float $v): void { $this->quantiteEau = $v; }
    public function setMethode(string $v): void { $this->methode = $v; }
    public function setDuree(int $v): void { $this->duree = $v; }
    public function setStatut(string $v): void { $this->statut = $v; }
    public function setCultureNom(string $v): void { $this->cultureNom = $v; }
    public function setParcelleNom(string $v): void { $this->parcelleNom = $v; }

    public static function fromArray(array $r): self
    {
        $i = new self();
        $i->setId((int) ($r['id'] ?? 0));
        $i->setCultureId((int) ($r['culture_id'] ?? 0));
        $i->setDate((string) ($r['date_irrigation'] ?? ''));
        $i->setQuantiteEau((float) ($r['quantite_eau'] ?? 0));
        $i->setMethode((string) ($r['methode'] ?? ''));
        $i->setDuree((int) ($r['duree'] ?? 0));
        $i->setStatut((string) ($r['statut'] ?? self::STATUT_PLANIFIEE));
        $i->setCultureNom((string) ($r['culture_nom'] ?? ''));
        $i->setParcelleNom((string) ($r['parcelle_nom'] ?? ''));
        return $i;
    }
}
