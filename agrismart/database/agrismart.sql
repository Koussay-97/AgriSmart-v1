-- ============================================================
-- AgriSmart - Base de données MySQL / MariaDB
-- Import : phpMyAdmin > onglet Importer, ou  mysql -u root < database/agrismart.sql
-- ============================================================
CREATE DATABASE IF NOT EXISTS agrismart CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE agrismart;

DROP TABLE IF EXISTS tentative_connexion;
DROP TABLE IF EXISTS irrigation;
DROP TABLE IF EXISTS culture;
DROP TABLE IF EXISTS conseil;
DROP TABLE IF EXISTS consultation;
DROP TABLE IF EXISTS parcelle;
DROP TABLE IF EXISTS exploitation;
DROP TABLE IF EXISTS utilisateur;

-- ------------------------------------------------------------
-- Utilisateurs (Agriculteur + Administrateur dans la même table, colonne "role")
-- Le rôle n'est JAMAIS choisi par le visiteur : forcé à 'agriculteur' à l'inscription.
-- ------------------------------------------------------------
CREATE TABLE utilisateur (
    id                 INT AUTO_INCREMENT PRIMARY KEY,
    nom                VARCHAR(100) NOT NULL,
    email              VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe       VARCHAR(255) NOT NULL,              -- hachage password_hash() (bcrypt)
    role               ENUM('agriculteur','expert','administrateur') NOT NULL DEFAULT 'agriculteur',
    telephone          VARCHAR(20)  NOT NULL DEFAULT '',
    adresse            VARCHAR(255) NOT NULL DEFAULT '',
    region             VARCHAR(60)  NOT NULL DEFAULT '',
    niveau_acces       TINYINT      NOT NULL DEFAULT 1,    -- utilisé par les administrateurs
    specialite         VARCHAR(100) NOT NULL DEFAULT '',   -- utilisé par les experts
    experience         INT          NOT NULL DEFAULT 0,    -- années d'expérience (experts)
    disponibilite      TINYINT(1)   NOT NULL DEFAULT 1,    -- expert disponible ou non
    actif              TINYINT(1)   NOT NULL DEFAULT 1,    -- 0 = compte bloqué par un admin
    derniere_connexion DATETIME NULL,
    created_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_role (role)
) ENGINE=InnoDB;

CREATE TABLE exploitation (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    agriculteur_id  INT NOT NULL,
    nom             VARCHAR(100) NOT NULL,
    localisation    VARCHAR(150) NOT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_exploitation_user FOREIGN KEY (agriculteur_id) REFERENCES utilisateur(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE parcelle (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    exploitation_id  INT NOT NULL,
    nom              VARCHAR(100) NOT NULL,
    surface          DECIMAL(8,2) NOT NULL,                 -- hectares
    type_sol         ENUM('Argileux','Sablonneux','Limoneux','Calcaire') NOT NULL,
    CONSTRAINT fk_parcelle_exploitation FOREIGN KEY (exploitation_id) REFERENCES exploitation(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Protection contre la force brute sur la connexion (voir UserController::login)
CREATE TABLE tentative_connexion (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    email       VARCHAR(150) NOT NULL,
    ip          VARCHAR(45)  NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_ip (ip)
) ENGINE=InnoDB;

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

-- ------------------------------------------------------------
-- Données de démonstration
--   Administrateur : admin@agrismart.tn / Admin@1234
--   Agriculteurs   : ahmed@agrismart.tn, fatma@agrismart.tn, mohamed@agrismart.tn / demo1234
--   Expert         : expert@agrismart.tn / Expert@1234
-- ------------------------------------------------------------
INSERT INTO utilisateur (id, nom, email, mot_de_passe, role, telephone, adresse, region, niveau_acces) VALUES
(1, 'Ahmed Ben Salah', 'ahmed@agrismart.tn',   '$2y$12$TiqTxL6rFVbf9iENBpU8MOtCIkrrU0etwJPmsAitVfkdCuoEFX5MG', 'agriculteur',   '+216 98 123 456', 'Rue de la Liberté, Sidi Bouzid',      'Sidi Bouzid', 1),
(2, 'Fatma Trabelsi',  'fatma@agrismart.tn',   '$2y$12$TiqTxL6rFVbf9iENBpU8MOtCIkrrU0etwJPmsAitVfkdCuoEFX5MG', 'agriculteur',   '+216 97 654 321', 'Route de Sousse, Kairouan',           'Kairouan',    1),
(3, 'Mohamed Gharbi',  'mohamed@agrismart.tn', '$2y$12$TiqTxL6rFVbf9iENBpU8MOtCIkrrU0etwJPmsAitVfkdCuoEFX5MG', 'agriculteur',   '+216 55 222 111', 'Avenue Habib Bourguiba, Nabeul',      'Nabeul',      1),
(4, 'Admin AgriSmart', 'admin@agrismart.tn',   '$2y$12$rsAidkOcP5YdetFQ8FRLWu3bt6KePXWBVGD/p5FyG9aMz/g6Z27Qa', 'administrateur','',                '',                                '',            1);

INSERT INTO exploitation (id, agriculteur_id, nom, localisation) VALUES
(1, 1, 'Ferme El Baraka',   'Sidi Bouzid'),
(2, 1, 'Verger Ben Salah',  'Regueb'),
(3, 2, 'Domaine Trabelsi',  'Kairouan'),
(4, 3, 'Serres du Cap Bon', 'Nabeul');

INSERT INTO parcelle (exploitation_id, nom, surface, type_sol) VALUES
(1, 'Parcelle Nord',  4.50, 'Argileux'),
(1, 'Parcelle Sud',   3.00, 'Sablonneux'),
(2, 'Oliveraie',      6.20, 'Calcaire'),
(3, 'Champ de blé',   8.00, 'Limoneux'),
(4, 'Serre tomates',  1.20, 'Limoneux'),
(4, 'Serre poivrons', 0.90, 'Sablonneux');

-- ------------------------------------------------------------
-- Données de démonstration des modules Consultations / Cultures / Irrigation
--   Expert : expert@agrismart.tn / Expert@1234
-- ------------------------------------------------------------
INSERT INTO utilisateur (id, nom, email, mot_de_passe, role, specialite, experience, disponibilite) VALUES
(5, 'Dr Sami Ben Youssef', 'expert@agrismart.tn', '$2y$12$ZfHDkIcUG75XiYfitMcD8e2UfEXigfC1M306nyLXgoPcPnCDBt8ou', 'expert', 'Irrigation et gestion de l''eau', 12, 1);

INSERT INTO consultation (id, agriculteur_id, expert_id, exploitation_id, sujet, description, statut) VALUES
(1, 1, 5, 1, 'Jaunissement des feuilles de tomates', 'Depuis une semaine, les feuilles du bas jaunissent sur la parcelle Nord. J''arrose tous les matins. Est-ce un excès d''eau ou une carence ?', 'en_cours'),
(2, 2, NULL, 3, 'Calendrier d''irrigation pour le blé', 'Quelle fréquence d''irrigation recommandez-vous pour du blé en sol limoneux pendant la période sèche ?', 'ouverte');

INSERT INTO conseil (consultation_id, expert_id, contenu, sensible, valide) VALUES
(1, 5, 'Réduisez l''arrosage à un jour sur deux et vérifiez le drainage de la parcelle : le jaunissement du bas des plants est souvent lié à un excès d''eau.', 0, 1),
(1, 5, 'Si le jaunissement persiste après 10 jours, appliquez un apport d''azote organique (compost mûr) en faible dose, au pied des plants.', 1, 0);

INSERT INTO culture (id, parcelle_id, nom, date_semis, date_recolte) VALUES
(1, 1, 'Tomates', '2026-03-15', '2026-07-20'),
(2, 3, 'Oliviers', '2020-11-01', NULL),
(3, 4, 'Blé dur', '2026-11-10', '2027-06-15'),
(4, 5, 'Tomates sous serre', '2026-09-01', NULL);

INSERT INTO irrigation (culture_id, date_irrigation, quantite_eau, methode, duree, statut) VALUES
(1, '2026-06-01', 3200.00, 'Goutte-à-goutte', 90, 'realisee'),
(1, '2026-06-08', 3000.00, 'Goutte-à-goutte', 85, 'realisee'),
(2, '2026-07-10', 8000.00, 'Goutte-à-goutte', 180, 'realisee'),
(3, '2026-12-05', 5000.00, 'Aspersion', 120, 'planifiee'),
(4, '2026-12-01', 1800.00, 'Goutte-à-goutte', 60, 'planifiee');
