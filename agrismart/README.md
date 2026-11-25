# AgriSmart — Projet Technologies Web (ESPRIT 2026-2027)

Plateforme web guidée par l'IA pour une agriculture durable. Architecture **MVC en PHP** (PDO / MySQL), sans framework.
Équipe : Koussay (Membre 1), Khlifi Israa (Membre 2), Lahbib Beya (Membre 3).

## Fonctionnalités

| Domaine | Détail |
|---|---|
| **Front Office (agriculteur)** | inscription, connexion/déconnexion, tableau de bord, profil (modifier ses infos, changer son mot de passe, supprimer son compte), CRUD des exploitations et des parcelles |
| **Back Office (administrateur)** | connexion dédiée, tableau de bord (statistiques), **CRUD complet des utilisateurs** (liste paginée + recherche + filtres rôle/région/statut, consultation, ajout, modification, suppression), changement de rôle, blocage/activation d'un compte, profil administrateur |
| **Experts et consultations (Membre 2)** | rôle `expert` créé par l'administrateur ; l'agriculteur soumet une consultation (sujet, description, exploitation concernée), un expert la prend en charge, donne des **conseils** et la clôture ; un conseil « sensible » n'est visible qu'après **validation par un administrateur** (Back Office) ; l'admin voit toutes les consultations et peut les (ré)affecter |
| **Suivi agricole (Membre 3)** | **cultures** (par parcelle : semis, récolte) et **irrigations** (planifiées puis réalisées : date, litres, méthode, durée) avec total d'eau par culture et sur le tableau de bord ; vue globale pour l'administrateur |
| **Rôles et autorisation** | rôles `agriculteur`, `expert` et `administrateur` stockés en base ; contrôle d'accès refait côté serveur sur **chaque** page sensible (le rôle est relu en base à chaque requête : un compte supprimé, bloqué ou rétrogradé perd l'accès immédiatement) ; erreur **403** si mauvais rôle |
| **Sécurité** | voir le tableau ci-dessous |
| **Validation des saisies** | côté **serveur** (PHP, source de vérité) et côté **navigateur** (`js/validate.js`) sur tous les formulaires |

### Sécurité mise en place

| Risque | Protection | Où ? |
|---|---|---|
| Injection SQL | requêtes préparées PDO + `ATTR_EMULATE_PREPARES = false` | tous les contrôleurs, `config/database.php` |
| Mots de passe en clair | `password_hash()` / `password_verify()` (+ `password_needs_rehash`) | `UserController` |
| XSS | `htmlspecialchars()` (fonction `e()`) sur toute sortie, validation des entrées, en-tête Content-Security-Policy (aucun JS inline) | vues, `core/security.php` |
| CSRF | jeton aléatoire par session, vérifié avec `hash_equals()` pour **toute** requête POST (`csrf_guard()`) ; suppression et déconnexion uniquement en POST | `core/security.php`, routeurs |
| Fixation de session | `session_regenerate_id(true)` à la connexion et au changement de mot de passe | `UserController` |
| Vol de session | cookie `HttpOnly` + `SameSite=Lax` (+ `Secure` en HTTPS), `use_strict_mode`, `use_only_cookies` | `secure_session_start()` |
| Sessions abandonnées | expiration après 30 min d'inactivité | `secure_session_start()` |
| Force brute | blocage après 5 échecs (email) pendant 5 min, table `tentative_connexion` | `UserController::login` |
| Énumération d'emails | message de connexion générique + `password_verify` factice | `UserController::login` |
| Élévation de privilèges | rôle forcé à `agriculteur` à l'inscription ; un admin ne peut pas changer son propre rôle, se bloquer ou se supprimer ; le dernier admin actif est protégé | `UserController` |
| Clickjacking, MIME sniffing | `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy` | `send_security_headers()` |

> Les sessions du Front Office et du Back Office ont des **noms de cookie différents** (`AGRISMART_FO` / `AGRISMART_BO`) :
> les cookies ne sont pas isolés par port, donc sans cela se connecter au BO déconnecterait le FO.

## Installation

Prérequis : **PHP 8+** (extensions `pdo_mysql`, `mbstring`), **MySQL / MariaDB** (XAMPP ou WAMP convient).

1. Cloner le dépôt.
2. **Base de données** : importer `database/agrismart.sql` (phpMyAdmin → *Importer*, ou `mysql -u root < database/agrismart.sql`).
   Cela crée la base `agrismart`, les tables et les données de démonstration.
   *Base déjà installée avant l'ajout des modules Consultations / Cultures / Irrigation ?* Importez plutôt `database/migration_modules.sql` (dans la base `agrismart`) : elle ajoute le rôle expert et les nouvelles tables **sans perdre** vos données.
3. **Configuration** : par défaut `root` sans mot de passe sur `localhost`. Si votre MySQL est différent,
   copier `config/config.local.example.php` en `config/config.local.php` et l'adapter (ce fichier est ignoré par Git).
4. **Lancer** (deux terminaux, depuis la racine du projet) :

```bash
# Front Office (agriculteur)
cd frontoffice && php -S localhost:8000
# -> http://localhost:8000/index.php?page=home

# Back Office (administrateur)
cd backoffice && php -S localhost:8001
# -> http://localhost:8001/index.php?page=login
```
Ou placer le dossier dans `htdocs` (XAMPP) / `www` (WAMP) et ouvrir `http://localhost/agrismart/frontoffice/` et `.../backoffice/`.

### Comptes de démonstration

| Rôle | Email | Mot de passe |
|---|---|---|
| Administrateur | `admin@agrismart.tn` | `Admin@1234` |
| Expert | `expert@agrismart.tn` | `Expert@1234` |
| Agriculteur | `ahmed@agrismart.tn` (ou `fatma@…`, `mohamed@…`) | `demo1234` | s 

Si un compte est bloqué après trop d'échecs de connexion : `DELETE FROM tentative_connexion;` dans phpMyAdmin.

## Architecture

```
agrismart/
├── config/
│   ├── config.php                 # paramètres (BD, durée de session...) + surcharge config.local.php
│   └── database.php               # classe Database : Singleton PDO sécurisé (getConnexion())
├── core/
│   ├── helpers.php                # e(), url(), redirect(), flash(), abortWith()
│   └── security.php               # session sécurisée, en-têtes HTTP, CSRF
├── models/                        # MODÈLES : attributs + getters/setters UNIQUEMENT (aucun SQL)
│   └── Utilisateur (mère) / Agriculteur / Expert / Administrateur / Exploitation / Parcelle
│       / Consultation / Conseil / Culture / Irrigation .php
├── controllers/
│   └── UserController.php         # logique + SQL du module Utilisateurs, partagé FO + BO
├── frontoffice/                   # point d'entrée : index.php?page=xxx
│   ├── controllers/               # Auth, Dashboard, Profile, Exploitation, Parcelle, Consultation, Conseil, Culture, Irrigation
│   ├── views/                     # HTML uniquement + includes/ (head, navbar, footer...)
│   └── js/ (main.js, validate.js) · css/ · img/
├── backoffice/
│   ├── index.php                  # routeur + contrôle d'accès administrateur
│   └── app/ (routes.php, Controllers/, Views/{layout,partials,pages,auth,assets})
└── database/agrismart.sql         # schéma + données de démonstration
```

**MVC** : le *Modèle* ne contient que des données ; le *Contrôleur* contient toute la logique (SQL préparé, hachage, validation,
sessions, rôles) ; la *Vue* n'affiche que du HTML échappé avec `e()` et ne fait jamais de requête SQL.

## Ajouter un module (tous les membres)

**Front Office** : créer `views/ma-page.php` (copier le haut/bas d'une vue existante), un contrôleur `MonController extends Controller`
dont chaque action commence par `$user = $this->requireLogin();`, puis ajouter la route dans `$routes` de `frontoffice/index.php`.
**Back Office** : créer `app/Views/pages/ma-page.php`, un contrôleur dont chaque action commence par `$this->requireAdmin();`,
appeler `$this->render('ma-page', $data, 'Titre', 'cle')`, ajouter la route dans `app/routes.php` et activer le lien dans `partials/sidebar.php`.
**Règles communes** : requêtes **préparées** uniquement ; tout formulaire POST contient `<?= csrf_field() ?>` ; toute donnée affichée passe par `e()` ;
toute suppression se fait en POST ; validation PHP **et** `data-rules` (JS) sur chaque champ.

## Répartition du travail
| Membre | Module |
|---|---|
| 1 — Koussay | Utilisateurs (CRUD, rôles, sécurité, sessions), agriculteurs, exploitations, parcelles, BO « Gérer les utilisateurs » |
| 2 — Khlifi Israa | Experts, **consultations**, **conseils** (+ validation BO), BO dashboard. *Reste : rendez-vous* |
| 3 — Lahbib Beya | **Cultures**, **irrigation**, suivi agricole BO. *Reste : fertilisation, interventions, météo, chatbot IA, notifications* |

Organisation Git (branches, Project Board, commits) : voir [`docs/GIT_WORKFLOW.md`](docs/GIT_WORKFLOW.md).
Scénarios de test pour la validation : voir [`docs/TESTS.md`](docs/TESTS.md).

## À faire (étapes suivantes)
- [ ] Intégrer les fichiers du template choisi (css/js/images) et remplacer le balisage simplifié des includes/partials
- [ ] Rendez-vous (membre 2) ; fertilisation, interventions, météo, chatbot IA, notifications (membre 3)
