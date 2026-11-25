<?php
/**
 * core/security.php - sessions sécurisées, en-têtes HTTP de sécurité, protection CSRF.
 * Appelé tout en haut de chaque index.php (FO et BO) AVANT toute sortie.
 */

/**
 * Démarre la session avec une configuration sécurisée.
 * $name : nom du cookie de session. Le FO et le BO utilisent des noms DIFFÉRENTS : les cookies ne sont pas
 * isolés par port (localhost:8000 et localhost:8001 partagent leurs cookies), sinon les deux sessions s'écraseraient.
 */
function secure_session_start(string $name): void
{
    $config = require __DIR__ . '/../config/config.php';
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

    ini_set('session.use_strict_mode', '1');      // refuse un identifiant de session inventé par un attaquant
    ini_set('session.use_only_cookies', '1');     // jamais d'ID de session dans l'URL
    ini_set('session.cookie_httponly', '1');      // cookie invisible au JavaScript (limite le vol via XSS)
    ini_set('session.cookie_samesite', 'Lax');    // le cookie n'est pas envoyé depuis un site tiers (limite le CSRF)
    if ($https) {
        ini_set('session.cookie_secure', '1');
    }
    session_name($name);
    session_start();

    // Expiration par inactivité
    $lifetime = (int) $config['session_lifetime'];
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $lifetime)) {
        $_SESSION = [];
        session_regenerate_id(true);
        flash('Votre session a expiré après une période d\'inactivité. Veuillez vous reconnecter.', 'warning');
    }
    $_SESSION['last_activity'] = time();
}

/** En-têtes HTTP de sécurité (clickjacking, MIME sniffing, referrer, Content-Security-Policy). */
function send_security_headers(): void
{
    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    // Les ressources externes autorisées sont celles utilisées par les templates (Bootstrap, icônes, polices).
    header("Content-Security-Policy: default-src 'self'; "
        . "script-src 'self' https://cdn.jsdelivr.net; "
        . "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; "
        . "font-src 'self' https://cdn.jsdelivr.net https://fonts.gstatic.com; "
        . "img-src 'self' data: https:; "
        . "frame-ancestors 'none'; form-action 'self'; base-uri 'self'");
}

// ---------------------------------------------------------------------------------------------
// CSRF : un jeton aléatoire par session, présent dans chaque formulaire POST, vérifié par le routeur.
// ---------------------------------------------------------------------------------------------
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Champ caché à placer juste après l'ouverture de chaque <form method="post">. */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

function csrf_verify(?string $token): bool
{
    if (!$token || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/** À appeler dans le routeur : refuse toute requête POST sans jeton CSRF valide. */
function csrf_guard(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && !csrf_verify($_POST['csrf_token'] ?? null)) {
        abortWith(419, 'Jeton de sécurité invalide', 'Votre formulaire a expiré ou la requête est suspecte. Rechargez la page et réessayez.', 'login');
    }
}
