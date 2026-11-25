<?php
/** Agriculteur hérite de Utilisateur (diagramme de classes). Modèle pur : attributs + getters/setters. */
class Agriculteur extends Utilisateur
{
    /** Régions (gouvernorats) proposées dans les formulaires et validées côté serveur. */
    public const REGIONS = ['Tunis', 'Ariana', 'Ben Arous', 'Bizerte', 'Béja', 'Jendouba', 'Le Kef', 'Siliana', 'Zaghouan',
        'Nabeul', 'Sousse', 'Monastir', 'Mahdia', 'Kairouan', 'Kasserine', 'Sidi Bouzid', 'Sfax', 'Gafsa', 'Tozeur',
        'Kébili', 'Gabès', 'Médenine', 'Tataouine', 'Manouba'];

    private string $telephone = '';
    private string $adresse = '';
    private string $region = '';

    public function getTelephone(): string { return $this->telephone; }
    public function getAdresse(): string { return $this->adresse; }
    public function getRegion(): string { return $this->region; }
    public function getRole(): string { return self::ROLE_AGRICULTEUR; }

    public function setTelephone(string $telephone): void { $this->telephone = $telephone; }
    public function setAdresse(string $adresse): void { $this->adresse = $adresse; }
    public function setRegion(string $region): void { $this->region = $region; }
}
