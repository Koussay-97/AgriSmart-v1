<?php
/**
 * config/config.php - paramètres de l'application.
 * Pour changer le mot de passe MySQL, le port, etc. SANS modifier ce fichier (donc sans conflit Git) :
 * copier config/config.local.example.php en config/config.local.php (ignoré par Git) et l'adapter.
 */
$config = [
    'db' => [
        'host'    => 'localhost',
        'port'    => 3306,
        'name'    => 'agrismart',
        'user'    => 'root',
        'pass'    => '',
        'charset' => 'utf8mb4',
    ],
    'debug'            => false,   // true = affiche le détail des erreurs SQL (développement uniquement)
    'session_lifetime' => 1800,    // expiration après 30 minutes d'inactivité
    'max_login_tries'  => 5,       // tentatives de connexion échouées avant blocage temporaire
    'lockout_minutes'  => 5,
];

$local = __DIR__ . '/config.local.php';
if (is_file($local)) {
    $config = array_replace_recursive($config, require $local);
}
return $config;
