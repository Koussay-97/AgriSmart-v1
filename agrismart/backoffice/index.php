<?php
/**
 * AgriSmart - Back Office (Administrateur) - point d'entrée / routeur.
 * Test local : php -S localhost:8001   puis   http://localhost:8001/index.php?page=dashboard
 */
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../core/security.php';
secure_session_start('AGRISMART_BO');   // cookie de session propre au Back Office (différent du Front Office)
send_security_headers();

require_once __DIR__ . '/../models/bootstrap.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/app/helpers.php';
require_once __DIR__ . '/app/Controllers/Controller.php';

csrf_guard(); // toute requête POST doit porter un jeton CSRF valide

$routes = require __DIR__ . '/app/routes.php';

$page = (string) ($_GET['page'] ?? 'dashboard');
if (!isset($routes[$page])) {
    http_response_code(404);
    abortWith(404, 'Page introuvable', 'Cette page n\'existe pas ou n\'est pas encore activée dans app/routes.php.', 'dashboard');
}

// Contrôle d'accès centralisé : tout le Back Office est réservé aux administrateurs, sauf la page de connexion.
// (Chaque contrôleur re-vérifie aussi : défense en profondeur.)
if ($page !== 'login') {
    users()->requireRole(Utilisateur::ROLE_ADMIN);
}

[$class, $method] = $routes[$page];
require_once __DIR__ . "/app/Controllers/$class.php";
(new $class())->$method();
