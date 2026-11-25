<?php
/** Culture plantée sur une parcelle (diagramme de classes - Membre 3). Modèle pur. */
class Culture
{
    private int $id = 0;
    private int $parcelleId = 0;
    private string $nom = '';
    private string $dateSemis = '';
    private ?string $dateRecolte = null;
    // Données d'affichage (jointures)
    private string $parcelleNom = '';
    private string $exploitationNom = '';
    private float $eauTotale = 0.0;   // litres d'irrigations réalisées

    public function getId(): int { return $this->id; }
    public function getParcelleId(): int { return $this->parcelleId; }
    public function getNom(): string { return $this->nom; }
    public function getDateSemis(): string { return $this->dateSemis; }
    public function getDateRecolte(): ?string { return $this->dateRecolte; }
    public function getParcelleNom(): string { return $this->parcelleNom; }
    public function getExploitationNom(): string { return $this->exploitationNom; }
    public function getEauTotale(): float { return $this->eauTotale; }

    public function setId(int $v): void { $this->id = $v; }
    public function setParcelleId(int $v): void { $this->parcelleId = $v; }
    public function setNom(string $v): void { $this->nom = $v; }
    public function setDateSemis(string $v): void { $this->dateSemis = $v; }
    public function setDateRecolte(?string $v): void { $this->dateRecolte = $v; }
    public function setParcelleNom(string $v): void { $this->parcelleNom = $v; }
    public function setExploitationNom(string $v): void { $this->exploitationNom = $v; }
    public function setEauTotale(float $v): void { $this->eauTotale = $v; }

    public static function fromArray(array $r): self
    {
        $c = new self();
        $c->setId((int) ($r['id'] ?? 0));
        $c->setParcelleId((int) ($r['parcelle_id'] ?? 0));
        $c->setNom((string) ($r['nom'] ?? ''));
        $c->setDateSemis((string) ($r['date_semis'] ?? ''));
        $c->setDateRecolte(!empty($r['date_recolte']) ? (string) $r['date_recolte'] : null);
        $c->setParcelleNom((string) ($r['parcelle_nom'] ?? ''));
        $c->setExploitationNom((string) ($r['exploitation_nom'] ?? ''));
        $c->setEauTotale((float) ($r['eau_totale'] ?? 0));
        return $c;
    }
}
