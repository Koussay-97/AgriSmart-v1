<?php
/** clé de page => [Controller, méthode]. Décommenter une ligne quand son contrôleur et sa vue existent (workshop étape 11). */
return [
    // --- Authentification administrateur (seule page publique du Back Office) ---
    'login'              => ['AuthController', 'login'],
    'logout'             => ['AuthController', 'logout'],

    // --- Gestion des utilisateurs (CRUD + rôles + statut) ---
    'dashboard'          => ['DashboardController',    'index'],
    'utilisateurs'       => ['UtilisateursController', 'index'],
    'utilisateur-show'   => ['UtilisateursController', 'show'],
    'utilisateur-form'   => ['UtilisateursController', 'form'],
    'utilisateur-delete' => ['UtilisateursController', 'delete'],
    'utilisateur-toggle' => ['UtilisateursController', 'toggle'],
    'utilisateur-role'   => ['UtilisateursController', 'role'],

    // --- Profil de l'administrateur connecté ---
    'profil'             => ['ProfilController', 'index'],
    'profil-password'    => ['ProfilController', 'password'],

    // --- Membre 2 : experts (= utilisateurs de rôle expert), consultations, validation des conseils ---
    'consultations'      => ['ConsultationsController', 'index'],
    'consultation-show'  => ['ConsultationsController', 'show'],
    'consultation-assign' => ['ConsultationsController', 'assign'],
    'consultation-delete' => ['ConsultationsController', 'delete'],
    'conseils'           => ['ConseilsController', 'index'],
    'conseil-validate'   => ['ConseilsController', 'validate'],
    'conseil-delete'     => ['ConseilsController', 'delete'],

    // --- Membre 3 : suivi agricole (cultures + irrigation, vue globale) ---
    'suivi'              => ['SuiviController', 'index'],
];
