<?php /* includes/page-header.php : bandeau de titre + fil d'Ariane (style "Header" du template) des pages intérieures.
         Variables : $pageTitle (obligatoire), $breadcrumb (optionnel) = [ ['Libellé', 'cle-de-page'|null], ... ] */
$breadcrumb = $breadcrumb ?? [];
?>
<!-- Header Start -->
<div class="container-fluid bg-primary py-5 mb-5 page-header">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10 text-center">
                <h1 class="display-4 text-white animated slideInDown"><?= e($pageTitle ?? '') ?></h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center mb-0">
                        <li class="breadcrumb-item"><a class="text-white" href="<?= url('home') ?>">Accueil</a></li>
                        <?php foreach ($breadcrumb as $crumb): ?>
                            <?php if (!empty($crumb[1])): ?>
                                <li class="breadcrumb-item"><a class="text-white" href="<?= e($crumb[1]) ?>"><?= e($crumb[0]) ?></a></li>
                            <?php else: ?>
                                <li class="breadcrumb-item text-white active" aria-current="page"><?= e($crumb[0]) ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php if (!$breadcrumb): ?>
                            <li class="breadcrumb-item text-white active" aria-current="page"><?= e($pageTitle ?? '') ?></li>
                        <?php endif; ?>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<!-- Header End -->
