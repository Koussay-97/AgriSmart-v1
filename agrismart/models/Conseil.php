<?php
/**
 * Conseil donné par un expert dans une consultation (diagramme de classes - Membre 2). Modèle pur.
 * Un conseil "sensible" n'est visible par l'agriculteur qu'après validation par un administrateur.
 */
class Conseil
{
    private int $id = 0;
    private int $consultationId = 0;
    private ?int $expertId = null;
    private string $contenu = '';
    private string $dateEnvoi = '';
    private bool $sensible = false;
    private bool $valide = false;
    private string $expertNom = '';
    // Données d'affichage (jointures) pour la page de validation du Back Office
    private string $consultationSujet = '';
    private string $agriculteurNom = '';

    public function getId(): int { return $this->id; }
    public function getConsultationId(): int { return $this->consultationId; }
    public function getExpertId(): ?int { return $this->expertId; }
    public function getContenu(): string { return $this->contenu; }
    public function getDateEnvoi(): string { return $this->dateEnvoi; }
    public function isSensible(): bool { return $this->sensible; }
    public function isValide(): bool { return $this->valide; }
    public function getExpertNom(): string { return $this->expertNom; }
    public function getConsultationSujet(): string { return $this->consultationSujet; }
    public function getAgriculteurNom(): string { return $this->agriculteurNom; }

    public function setId(int $v): void { $this->id = $v; }
    public function setConsultationId(int $v): void { $this->consultationId = $v; }
    public function setExpertId(?int $v): void { $this->expertId = $v; }
    public function setContenu(string $v): void { $this->contenu = $v; }
    public function setDateEnvoi(string $v): void { $this->dateEnvoi = $v; }
    public function setSensible(bool $v): void { $this->sensible = $v; }
    public function setValide(bool $v): void { $this->valide = $v; }
    public function setExpertNom(string $v): void { $this->expertNom = $v; }
    public function setConsultationSujet(string $v): void { $this->consultationSujet = $v; }
    public function setAgriculteurNom(string $v): void { $this->agriculteurNom = $v; }

    public static function fromArray(array $r): self
    {
        $c = new self();
        $c->setId((int) ($r['id'] ?? 0));
        $c->setConsultationId((int) ($r['consultation_id'] ?? 0));
        $c->setExpertId(isset($r['expert_id']) ? (int) $r['expert_id'] : null);
        $c->setContenu((string) ($r['contenu'] ?? ''));
        $c->setDateEnvoi((string) ($r['date_envoi'] ?? ''));
        $c->setSensible((bool) ($r['sensible'] ?? 0));
        $c->setValide((bool) ($r['valide'] ?? 0));
        $c->setExpertNom((string) ($r['expert_nom'] ?? ''));
        $c->setConsultationSujet((string) ($r['consultation_sujet'] ?? ''));
        $c->setAgriculteurNom((string) ($r['agriculteur_nom'] ?? ''));
        return $c;
    }
}
