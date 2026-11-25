<?php
/** Helpers propres au Front Office (e(), url(), redirect(), flash() sont dans ../core/helpers.php). */

/** Contrôleur utilisateurs partagé (une seule instance par requête). */
function users(): UserController
{
    static $controller = null;
    if ($controller === null) {
        $controller = new UserController();
    }
    return $controller;
}

/** Compte connecté au Front Office (agriculteur OU expert), relu en base, ou null. */
function currentAccount(): ?Utilisateur
{
    $u = users()->currentUser();
    return ($u instanceof Agriculteur || $u instanceof Expert) ? $u : null;
}

/** Agriculteur connecté, ou null. */
function currentUser(): ?Agriculteur
{
    $u = currentAccount();
    return $u instanceof Agriculteur ? $u : null;
}

/** Expert connecté, ou null. */
function currentExpert(): ?Expert
{
    $u = currentAccount();
    return $u instanceof Expert ? $u : null;
}

function isLoggedIn(): bool
{
    return currentAccount() !== null;
}

function isExpert(): bool
{
    return currentExpert() !== null;
}

/** Page d'accueil de l'espace connecté : tableau de bord (agriculteur) ou consultations (expert). */
function homeRoute(): string
{
    return isExpert() ? 'consultations' : 'dashboard';
}

/** Classe .active pour le lien de navbar correspondant à la page courante (workshop étape 5). */
function navActive(string $key, string $currentPage): string
{
    return $key === $currentPage ? ' active' : '';
}

/** Date SQL (AAAA-MM-JJ ou AAAA-MM-JJ hh:mm:ss) -> JJ/MM/AAAA ; "—" si vide. */
function fmtDate(?string $s): string
{
    if ($s === null || $s === '') {
        return '—';
    }
    $t = strtotime($s);
    return $t ? date('d/m/Y', $t) : '—';
}

/** Litres -> "1 234 L" (sans décimales inutiles). */
function fmtLitres(float $l): string
{
    return number_format($l, ($l == floor($l)) ? 0 : 1, ',', ' ') . ' L';
}

/** Pastille HTML du statut d'une consultation. */
function consultationBadge(Consultation $c): string
{
    $color = $c->getCouleur();
    $text = in_array($color, ['warning', 'info'], true) ? ' text-dark' : '';
    return '<span class="badge bg-' . e($color) . $text . '">' . e($c->getLibelle()) . '</span>';
}
