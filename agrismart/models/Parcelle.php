<?php
/** Parcelle (diagramme de classes). Modèle pur : attributs + getters/setters. */
class Parcelle
{
    private int $id = 0;
    private int $exploitationId = 0;
    private string $nom = '';
    private float $surface = 0.0;
    private string $typeSol = '';

    public function getId(): int { return $this->id; }
    public function getExploitationId(): int { return $this->exploitationId; }
    public function getNom(): string { return $this->nom; }
    public function getSurface(): float { return $this->surface; }
    public function getTypeSol(): string { return $this->typeSol; }

    public function setId(int $id): void { $this->id = $id; }
    public function setExploitationId(int $id): void { $this->exploitationId = $id; }
    public function setNom(string $nom): void { $this->nom = $nom; }
    public function setSurface(float $surface): void { $this->surface = $surface; }
    public function setTypeSol(string $typeSol): void { $this->typeSol = $typeSol; }

    public static function fromArray(array $r): self
    {
        $p = new self();
        $p->setId((int) ($r['id'] ?? 0));
        $p->setExploitationId((int) ($r['exploitation_id'] ?? 0));
        $p->setNom((string) ($r['nom'] ?? ''));
        $p->setSurface((float) ($r['surface'] ?? 0));
        $p->setTypeSol((string) ($r['type_sol'] ?? ''));
        return $p;
    }
}
