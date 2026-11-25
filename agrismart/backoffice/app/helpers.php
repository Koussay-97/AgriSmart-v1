<?php
/** Helpers propres au Back Office (e(), url(), redirect(), flash() sont dans ../../core/helpers.php). */

/** Contrôleur utilisateurs partagé (une seule instance par requête). */
function users(): UserController
{
    static $controller = null;
    if ($controller === null) {
        $controller = new UserController();
    }
    return $controller;
}

/** Classe .active pour un lien de la sidebar (workshop Back Office, étape 4). */
function isActive(string $key, string $currentPage): string
{
    return $key === $currentPage ? ' active' : '';
}
