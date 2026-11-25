# Template Front Office : eLEARNING (HTML Codex / ThemeWagon)

Le template eLEARNING a été intégré en suivant le workshop (blocs communs dans `views/includes/`, MVC conservé).

## Ce qui a été fait
| Élément | Fichier(s) |
|---|---|
| `<head>` (polices, CSS) | `views/includes/head.php` |
| Écran de chargement | `views/includes/spinner.php` |
| Barre de navigation (lien actif automatique via `$currentPage`) | `views/includes/navbar.php` |
| Bandeau de titre + fil d'Ariane des pages intérieures | `views/includes/page-header.php` (nouveau) |
| Pied de page + bouton « retour en haut » | `views/includes/footer.php` |
| Scripts communs | `views/includes/scripts.php` |
| Recoloration du template | `css/bootstrap.min.css`, `css/style.css` (cyan `#06BBCC` → vert `#2C7A46`, marine `#181d38` → forêt `#17352A`, clair `#F0FBFC` → sable `#F4F1E8`) |
| Ajustements AgriSmart | `css/agrismart.css` (chargé en dernier) |
| Images de la présentation | `img/hero.jpg`, `farmer.jpg`, `irrigation.jpg`, `solar.jpg`, `logo.svg` |

## Bibliothèques hébergées localement (important)
La Content-Security-Policy (`core/security.php`) n'autorise que `'self'`, jsDelivr et Google Fonts.
jQuery, Bootstrap JS et Font Awesome sont donc dans `lib/`, `css/all.min.css` et `webfonts/` (et non chargés depuis un CDN),
ce qui évite d'affaiblir la CSP et permet de travailler sans connexion Internet.

## Ajouter une page (Membres 2 et 3)
1. Copier le haut/bas d'une vue existante (ex. `views/exploitations.php`) : `$pageTitle`, `$currentPage`, puis les `require_once` des includes.
2. Garder `page-header.php` pour le bandeau de titre (optionnel : `$breadcrumb = [['Libellé', url('page')], ['Page courante', null]];`).
3. Contenu dans `<section class="container pb-5">…</section>` ; blocs sur fond sable : `class="bg-light p-4"`.
4. Boutons carrés du template : `btn btn-primary` (pas de `rounded-pill`). Icônes : Font Awesome 5 (`fa fa-…`), pas Bootstrap Icons.
5. Pas de JavaScript inline (interdit par la CSP) : mettre le code dans un fichier `js/…` et le charger via `$extraScripts`.

## Licence
Le template gratuit exige de **conserver le crédit** (HTML Codex / ThemeWagon) dans le pied de page : ne pas le supprimer. Voir `LICENSE-elearning-template.txt`.
