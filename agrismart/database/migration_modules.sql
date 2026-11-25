-- ============================================================
-- AgriSmart - MIGRATION : ajoute les modules Experts, Consultations, Conseils, Cultures et Irrigation
-- à une base EXISTANTE (vos utilisateurs, exploitations et parcelles sont conservés).
-- Import : phpMyAdmin > base "agrismart" > onglet Importer, ou  mysql -u root agrismart < database/migration_modules.sql
-- (Sur une installation neuve, importez plutôt database/agrismart.sql qui contient déjà tout.)
-- ============================================================
USE agrismart;

-- 1. Nouveau rôle "expert" + champs propres aux experts
ALTER TABLE utilisateur
    MODIFY role ENUM('agriculteur','expert','administrateur') NOT NULL DEFAULT 'agriculteur',
    ADD COLUMN specialite    VARCHAR(100) NOT NULL DEFAULT '' AFTER niveau_acces,
    ADD COLUMN experience    INT          NOT NULL DEFAULT 0  AFTER specialite,
    ADD COLUMN disponibilite TINYINT(1)   NOT NULL DEFAULT 1  AFTER experience;

-- ------------------------------------------------------------
-- Module Consultations / Conseils (Membre 2)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS consultation (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    agriculteur_id  INT NOT NULL,
    expert_id       INT NULL,                              -- NULL tant qu'aucun expert ne l'a prise en charge
    exploitation_id INT NULL,                              -- exploitation concernée (optionnelle)
    sujet           VARCHAR(150) NOT NULL,
    description     TEXT NOT NULL,
    date_demande    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    statut          ENUM('ouverte','en_cours','cloturee') NOT NULL DEFAULT 'ouverte',
    INDEX idx_consultation_statut (statut),
    CONSTRAINT fk_consultation_agriculteur FOREIGN KEY (agriculteur_id)  REFERENCES utilisateur(id)  ON DELETE CASCADE,
    CONSTRAINT fk_consultation_expert      FOREIGN KEY (expert_id)       REFERENCES utilisateur(id)  ON DELETE SET NULL,
    CONSTRAINT fk_consultation_exploitation FOREIGN KEY (exploitation_id) REFERENCES exploitation(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS conseil (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    consultation_id INT NOT NULL,
    expert_id       INT NULL,
    contenu         TEXT NOT NULL,
    date_envoi      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    sensible        TINYINT(1) NOT NULL DEFAULT 0,         -- 1 = doit être validé par un administrateur avant diffusion
    valide          TINYINT(1) NOT NULL DEFAULT 0,         -- 1 = visible par l'agriculteur
    INDEX idx_conseil_valide (valide),
    CONSTRAINT fk_conseil_consultation FOREIGN KEY (consultation_id) REFERENCES consultation(id) ON DELETE CASCADE,
    CONSTRAINT fk_conseil_expert       FOREIGN KEY (expert_id)       REFERENCES utilisateur(id)  ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Module Suivi agricole : Cultures / Irrigation (Membre 3)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS culture (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    parcelle_id  INT NOT NULL,
    nom          VARCHAR(100) NOT NULL,
    date_semis   DATE NOT NULL,
    date_recolte DATE NULL,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_culture_parcelle FOREIGN KEY (parcelle_id) REFERENCES parcelle(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS irrigation (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    culture_id      INT NOT NULL,
    date_irrigation DATE NOT NULL,
    quantite_eau    DECIMAL(10,2) NOT NULL,                -- litres
    methode         ENUM('Goutte-à-goutte','Aspersion','Gravitaire','Manuelle') NOT NULL,
    duree           INT NOT NULL,                          -- minutes
    statut          ENUM('planifiee','realisee') NOT NULL DEFAULT 'planifiee',
    INDEX idx_irrigation_date (date_irrigation),
    CONSTRAINT fk_irrigation_culture FOREIGN KEY (culture_id) REFERENCES culture(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 2. (Optionnel) un compte expert de démonstration : expert@agrismart.tn / Expert@1234
INSERT INTO utilisateur (nom, email, mot_de_passe, role, specialite, experience, disponibilite)
VALUES ('Dr Sami Ben Youssef', 'expert@agrismart.tn', '$2y$12$ZfHDkIcUG75XiYfitMcD8e2UfEXigfC1M306nyLXgoPcPnCDBt8ou', 'expert', 'Irrigation et gestion de l''eau', 12, 1);
