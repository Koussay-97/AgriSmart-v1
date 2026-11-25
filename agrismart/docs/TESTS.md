# Scénarios de test (validation)

Prérequis : base importée, FO sur `:8000`, BO sur `:8001` (voir README).

## Authentification et sessions
- [ ] Inscription FO avec données valides -> redirection vers la connexion ; en base, le mot de passe commence par `$2y$` (pas en clair).
- [ ] Inscription avec email déjà utilisé / mot de passe faible / confirmation différente -> messages d'erreur (JS puis serveur : désactiver JS pour vérifier).
- [ ] Connexion FO réussie -> tableau de bord ; mauvais mot de passe -> « Email ou mot de passe incorrect. ».
- [ ] `index.php?page=dashboard` sans être connecté -> redirigé vers la connexion.
- [ ] Après déconnexion, bouton Précédent du navigateur -> pas d'accès aux pages protégées.
- [ ] DevTools > Application > Cookies : `AGRISMART_FO`, HttpOnly, SameSite=Lax ; la valeur change après reconnexion.
- [ ] Expiration : mettre `session_lifetime` à 10 dans `config/config.php`, attendre 10 s, recharger -> retour à la connexion (remettre 1800).
- [ ] 5 mauvais mots de passe sur le même email -> « Trop de tentatives... » (`DELETE FROM tentative_connexion;` pour débloquer).

## Rôles et autorisation
- [ ] `admin@agrismart.tn` sur le BO -> tableau de bord ; un agriculteur sur le BO -> « Accès réservé aux administrateurs. ».
- [ ] Un admin sur le FO -> message l'invitant à utiliser le Back Office.
- [ ] BO : `index.php?page=utilisateurs` sans connexion -> redirigé vers `login`.
- [ ] Agriculteur A : ouvrir `index.php?page=parcelles&exploitation=<id d'une exploitation de B>` -> « Exploitation introuvable. » (propriété vérifiée).
- [ ] Dans phpMyAdmin, passer le rôle d'un agriculteur connecté à `administrateur` puis recharger une page du FO -> **403** immédiat (rôle relu en base).
- [ ] Bloquer un compte depuis le BO pendant qu'il est connecté -> sa prochaine requête le déconnecte.

## CRUD utilisateurs (Back Office)
- [ ] Ajouter un agriculteur (téléphone + région obligatoires) et un administrateur (non obligatoires).
- [ ] Consulter (icône œil) : infos + exploitations.
- [ ] Modifier : nom, email, rôle, statut ; mot de passe vide = inchangé.
- [ ] Changer le rôle depuis la liste (liste déroulante).
- [ ] Bloquer / activer un compte ; le compte bloqué ne peut plus se connecter.
- [ ] Supprimer -> modale de confirmation ; ses exploitations/parcelles disparaissent (cascade).
- [ ] Garde-fous : impossible de se supprimer / se bloquer / se rétrograder soi-même ; impossible de supprimer le dernier admin actif.
- [ ] Recherche par nom/email, filtres rôle/région/statut, pagination (ajouter > 10 utilisateurs).

## Failles
- [ ] **Injection SQL** : email `' OR '1'='1` (passer le champ en `type="text"` via F12) -> connexion refusée.
- [ ] **XSS** : `INSERT INTO utilisateur (nom, email, mot_de_passe, role, telephone, region) VALUES ('<script>alert(1)</script>', 'xss@test.tn', '<hash>', 'agriculteur', '+216 98 000 000', 'Tunis');` -> le nom s'affiche en texte brut partout (FO et liste BO), sans popup.
- [ ] **CSRF** : F12 > supprimer le champ caché `csrf_token` d'un formulaire puis l'envoyer -> page « 419 Jeton de sécurité invalide ».
- [ ] **Fixation de session** : le cookie de session change à la connexion.
- [ ] En-têtes : onglet Réseau, requête document -> `Content-Security-Policy`, `X-Frame-Options: DENY`, `X-Content-Type-Options: nosniff`.

## Exploitations / parcelles (FO)
- [ ] Ajouter / modifier / supprimer une exploitation et une parcelle (surface `3,5` acceptée, `0` ou `abc` refusée).

## Consultations et conseils (Membre 2)
- [ ] Connexion FO en agriculteur (`ahmed@agrismart.tn`) : menu **Consultations** -> « Nouvelle consultation » (sujet < 5 caractères refusé) -> elle apparaît « Ouverte ».
- [ ] Modifier la consultation tant qu'elle est ouverte ; la supprimer ; une consultation « En cours » ne se supprime plus.
- [ ] Connexion FO en expert (`expert@agrismart.tn`) : arrivée sur la liste des consultations ; « À traiter » contient la consultation ouverte -> **Prendre en charge** (un second expert ne peut plus la prendre).
- [ ] L'expert donne un conseil **non sensible** : visible tout de suite côté agriculteur. Un conseil **sensible** : badge « en attente de validation » pour l'expert, **invisible** pour l'agriculteur.
- [ ] BO (admin) > **Conseils à valider** > Valider : le conseil devient visible côté agriculteur ; Rejeter : il disparaît.
- [ ] BO > **Consultations** : filtres statut/expert, détail, **Affecter** à un autre expert, suppression.
- [ ] Clôturer (agriculteur ou expert) : plus de nouveau conseil possible.
- [ ] Cloisonnement : un agriculteur ne voit pas la consultation d'un autre (`?page=consultation&id=...` -> « introuvable ») ; un expert ne peut pas modifier une consultation prise par un autre expert ; un agriculteur reçoit une 403 sur `?page=conseil-form`.
- [ ] BO > Utilisateurs > Ajouter : rôle **expert** (spécialité obligatoire, expérience 0-60). Supprimer un expert en charge d'une consultation « En cours » : elle repasse « Ouverte ».

## Cultures et irrigation (Membre 3)
- [ ] FO > Suivi agricole > **Cultures** : ajouter une culture sur une parcelle (récolte avant semis refusée), modifier, supprimer (ses irrigations disparaissent).
- [ ] Depuis une exploitation > Parcelles, le bouton **Cultures** filtre les cultures de la parcelle.
- [ ] **Irrigation** : planifier une irrigation (date future), la marquer « réalisée » (coche) ; une irrigation « réalisée » datée du futur est refusée ; quantité `0` ou `abc` refusée, durée > 1440 refusée.
- [ ] Filtres culture/statut ; le total d'eau (irrigations réalisées) et le tableau de bord se mettent à jour.
- [ ] Cloisonnement : `?page=irrigation-form&id=<id d'un autre agriculteur>` -> « Irrigation introuvable. ».
- [ ] BO > **Suivi agricole** : cultures de tous les agriculteurs et eau totale utilisée.
