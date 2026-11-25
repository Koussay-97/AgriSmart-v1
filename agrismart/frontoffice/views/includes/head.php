<?php /* includes/head.php : contenu de <head> UNIQUEMENT (sans les balises <head></head>, qui restent dans la page appelante).
         Variable attendue : $pageTitle
         Template : eLEARNING (HTML Codex / ThemeWagon), recoloré aux couleurs AgriSmart.
         Toutes les bibliothèques sont hébergées localement (css/, lib/, webfonts/) : la Content-Security-Policy n'autorise que 'self' + Google Fonts. */ ?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle ?? 'AgriSmart') ?> | AgriSmart</title>
<meta name="description" content="AgriSmart : plateforme web guidée par l'IA pour une agriculture durable.">

<!-- Favicon -->
<link rel="icon" type="image/svg+xml" href="img/logo.svg">

<!-- Polices Google (autorisées par la CSP) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">

<!-- Icônes Font Awesome (local) -->
<link href="css/all.min.css" rel="stylesheet">

<!-- Bibliothèques du template (local) -->
<link href="lib/animate/animate.min.css" rel="stylesheet">
<link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

<!-- Bootstrap personnalisé + feuille de style du template -->
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">

<!-- Ajustements AgriSmart (en dernier pour surcharger le template) -->
<link href="css/agrismart.css" rel="stylesheet">
