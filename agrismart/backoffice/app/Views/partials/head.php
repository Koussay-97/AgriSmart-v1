<?php /* partials/head.php : <head> + preloader (workshop BO étape 3). Variable : $title */ ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'AgriSmart Admin') ?> | AgriSmart Back Office</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- TODO template : une fois le template Back Office choisi, copier ses css/fonts/images/js dans app/Views/assets/ et remplacer/ajouter ses <link> ici. -->
    <link href="app/Views/assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <!-- ======== preloader =========== -->
    <div id="preloader"><div class="spinner"></div></div>
    <!-- ======== preloader end =========== -->
