<?php
/** core/helpers.php - fonctions utilitaires partagées par le Front Office et le Back Office. */

/** Échappe une donnée avant affichage (protection XSS). */
function e($v): string
{
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}

function url(string $page, array $params = []): string
{
    return 'index.php?' . http_build_query(['page' => $page] + $params);
}

function redirect(string $page, array $params = []): void
{
    header('Location: ' . url($page, $params));
    exit;
}

/** Définit (avec $msg) ou lit puis efface (sans argument) un message flash. */
function flash(?string $msg = null, string $type = 'success'): ?array
{
    if ($msg !== null) {
        $_SESSION['flash'] = ['msg' => $msg, 'type' => $type];
        return null;
    }
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

/** Page d'erreur minimale (403, 419...) sans dépendre d'un layout. */
function abortWith(int $code, string $title, string $message, string $backPage = 'login'): void
{
    http_response_code($code);
    echo '<!DOCTYPE html><html lang="fr"><head><meta charset="utf-8"><title>' . e($title) . ' | AgriSmart</title>'
       . '<meta name="viewport" content="width=device-width, initial-scale=1">'
       . '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>'
       . '<body class="bg-light"><div class="container py-5 text-center" style="max-width:560px">'
       . '<h1 class="display-4 text-danger">' . (int) $code . '</h1><h2>' . e($title) . '</h2>'
       . '<p class="text-muted">' . e($message) . '</p>'
       . '<a class="btn btn-success rounded-pill px-4" href="' . e(url($backPage)) . '">Retour</a></div></body></html>';
    exit;
}
