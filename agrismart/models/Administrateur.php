<?php
/** Administrateur hérite de Utilisateur (diagramme de classes). Modèle pur : attributs + getters/setters. */
class Administrateur extends Utilisateur
{
    private int $niveauAcces = 1;

    public function getNiveauAcces(): int { return $this->niveauAcces; }
    public function getRole(): string { return self::ROLE_ADMIN; }

    public function setNiveauAcces(int $niveauAcces): void { $this->niveauAcces = $niveauAcces; }
}
