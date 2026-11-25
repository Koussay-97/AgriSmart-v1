<?php
/**
 * partials/sidebar.php : menu latéral (workshop BO étape 4).
 * Logique dynamique : $currentPage est comparé à la clé de chaque lien via isActive().
 * route = null  => module pas encore branché (désactivé, "bientôt").
 */
$currentPage = $currentPage ?? '';
$menu = [
    ['key' => 'dashboard',     'label' => 'Tableau de bord',  'icon' => 'bi-speedometer2', 'route' => 'dashboard',     'owner' => 'Membre 2'],
    ['key' => 'utilisateurs',  'label' => 'Utilisateurs',     'icon' => 'bi-people',       'route' => 'utilisateurs',  'owner' => 'Membre 1'],
    ['key' => 'experts',       'label' => 'Experts',          'icon' => 'bi-person-badge', 'route' => 'utilisateurs',  'owner' => 'Membre 2', 'params' => ['role' => 'expert']],
    ['key' => 'consultations', 'label' => 'Consultations',    'icon' => 'bi-chat-dots',    'route' => 'consultations', 'owner' => 'Membre 2'],
    ['key' => 'conseils',      'label' => 'Conseils à valider', 'icon' => 'bi-patch-check', 'route' => 'conseils',      'owner' => 'Membre 2'],
    ['key' => 'suivi',         'label' => 'Suivi agricole',   'icon' => 'bi-flower2',      'route' => 'suivi',         'owner' => 'Membre 3'],
];
?>
    <!-- ======== sidebar-nav start =========== -->
    <aside class="sidebar-nav-wrapper">
        <div class="navbar-logo">
            <a href="<?= url('dashboard') ?>" class="text-white text-decoration-none fs-4 fw-bold">
                <i class="bi bi-flower2 text-warning"></i> Agri<span class="text-warning">Smart</span>
            </a>
        </div>
        <nav class="sidebar-nav">
            <ul class="list-unstyled">
                <?php foreach ($menu as $item): ?>
                    <li class="nav-item<?= isActive($item['key'], $currentPage) ?>">
                        <?php if ($item['route']): ?>
                            <a href="<?= url($item['route'], $item['params'] ?? []) ?>"><i class="bi <?= e($item['icon']) ?>"></i><span><?= e($item['label']) ?></span></a>
                        <?php else: ?>
                            <a href="#" class="disabled" title="Module <?= e($item['owner']) ?> : bientôt disponible"><i class="bi <?= e($item['icon']) ?>"></i><span><?= e($item['label']) ?></span></a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </aside>
    <!-- ======== sidebar-nav end =========== -->
