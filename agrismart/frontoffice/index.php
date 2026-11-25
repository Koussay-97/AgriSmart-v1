<?php
/**
 * AgriSmart - Front Office (Agriculteur) - point d'entrée / routeur.
 * Test local : php -S localhost:8000   puis   http://localhost:8000/index.php?page=home
 */
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../core/security.php';
secure_session_start('AGRISMART_FO');   // cookie de session propre au Front Office
send_security_headers();

require_once __DIR__ . '/../models/bootstrap.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/controllers/Controller.php';

csrf_guard(); // toute requête POST doit porter un jeton CSRF valide

// clé de page => [Controller, méthode]
$routes = [
    'home'              => ['HomeController',         'index'],
    'login'             => ['AuthController',         'login'],
    'register'          => ['AuthController',         'register'],
    'logout'            => ['AuthController',         'logout'],
    'dashboard'         => ['DashboardController',    'index'],
    'profile'           => ['ProfileController',      'index'],
    'profile-password'  => ['ProfileController',      'password'],
    'profile-delete'    => ['ProfileController',      'delete'],
    'exploitations'     => ['ExploitationController', 'index'],
    'exploitation-form' => ['ExploitationController', 'form'],
    'exploitation-delete' => ['ExploitationController', 'delete'],
    'parcelles'         => ['ParcelleController',     'index'],
    'parcelle-form'     => ['ParcelleController',     'form'],
    'parcelle-delete'   => ['ParcelleController',     'delete'],

    // --- Membre 2 : consultations et conseils (agriculteur + expert) ---
    'consultations'       => ['ConsultationController', 'index'],
    'consultation'        => ['ConsultationController', 'show'],
    'consultation-form'   => ['ConsultationController', 'form'],
    'consultation-delete' => ['ConsultationController', 'delete'],
    'consultation-close'  => ['ConsultationController', 'close'],
    'consultation-take'   => ['ConsultationController', 'take'],
    'conseil-form'        => ['ConseilController',       'form'],
    'conseil-delete'      => ['ConseilController',       'delete'],

    // --- Membre 3 : suivi agricole (cultures, irrigation) ---
    'cultures'            => ['CultureController',     'index'],
    'culture-form'        => ['CultureController',     'form'],
    'culture-delete'      => ['CultureController',     'delete'],
    'irrigations'         => ['IrrigationController',  'index'],
    'irrigation-form'     => ['IrrigationController',  'form'],
    'irrigation-done'     => ['IrrigationController',  'done'],
    'irrigation-delete'   => ['IrrigationController',  'delete'],
    // 'meteo'            => ['MeteoController',       'index'],   // à faire
];

$page = (string) ($_GET['page'] ?? 'home');
if (!isset($routes[$page])) {
    http_response_code(404);
    (new Controller404())->index();
    exit;
}

[$class, $method] = $routes[$page];
require_once __DIR__ . "/controllers/$class.php";
(new $class())->$method();
