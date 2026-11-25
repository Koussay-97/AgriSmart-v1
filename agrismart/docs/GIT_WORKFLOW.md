# Organisation du travail en groupe (Git + GitHub Project Board)

Le professeur vérifie que **chaque membre a des contributions traçables** : commits, branches et pull requests au nom de chacun.

## 1. GitHub Project Board
Créer un *Project* (onglet *Projects* du dépôt, modèle « Board ») avec les colonnes **To do / In progress / In review / Done**.
Une carte (issue) par tâche, assignée à **un** membre, par exemple :

| Issue | Assignée à |
|---|---|
| Base MySQL + `config/database.php` (Singleton PDO) | Membre 1 |
| UserController : inscription, connexion, sessions, rôles | Membre 1 |
| CRUD utilisateurs Back Office (liste, ajout, modification, suppression) | Membre 1 |
| Sécurité : CSRF, en-têtes HTTP, validation JS (`validate.js`) | Membre 1 |
| CRUD exploitations / parcelles (Front Office) | Membre 1 |
| Module experts + consultations | Membre 2 |
| Module cultures / météo / chatbot | Membre 3 |

> Répartissez équitablement avant la séance : chaque membre doit pouvoir expliquer **et** montrer ses commits.
> Si cette livraison a été préparée par un seul membre, découpez-la en plusieurs commits/PR par fonctionnalité et répartissez les relectures.

## 2. Branches
- `main` : toujours fonctionnelle, jamais de commit direct.
- `feature/<module>-<nom-membre>` : une branche par tâche (`feature/users-crud-koussay`, `feature/experts-israa`...).

```bash
git checkout main && git pull
git checkout -b feature/users-crud-koussay
# ... travail ...
git add .
git commit -m "feat(users): CRUD utilisateurs du back office (#12)"
git push -u origin feature/users-crud-koussay
```
Puis ouvrir une **Pull Request** vers `main`, faire relire par un autre membre, **Merge** (le lien `#12` ferme l'issue et déplace la carte dans *Done*).

## 3. Messages de commit
`type(module): description courte (#issue)` avec les types `feat`, `fix`, `docs`, `refactor`, `style`.

## 4. Avant la validation (une seule machine par groupe)
1. `git checkout main && git pull` : tout est fusionné.
2. Importer `database/agrismart.sql`, adapter `config/config.local.php`.
3. Dérouler `docs/TESTS.md` en entier sur la machine de présentation.
4. Vérifier : `README.md` à jour, nom du dépôt conforme aux consignes, aucun mot de passe réel commité.
