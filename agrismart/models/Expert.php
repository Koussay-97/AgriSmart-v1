<?php
/** Expert (agronome) hérite de Utilisateur (diagramme de classes - Membre 2). Modèle pur : attributs + getters/setters. */
class Expert extends Utilisateur
{
    private string $specialite = '';
    private int $experience = 0;       // années d'expérience
    private bool $disponibilite = true;

    public function getSpecialite(): string { return $this->specialite; }
    public function getExperience(): int { return $this->experience; }
    public function isDisponible(): bool { return $this->disponibilite; }
    public function getRole(): string { return self::ROLE_EXPERT; }

    public function setSpecialite(string $specialite): void { $this->specialite = $specialite; }
    public function setExperience(int $experience): void { $this->experience = $experience; }
    public function setDisponibilite(bool $disponibilite): void { $this->disponibilite = $disponibilite; }
}
