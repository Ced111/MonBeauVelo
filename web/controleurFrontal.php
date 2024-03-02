<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

use App\MonBeauVelo\Controleur\ControleurGenerique;
use App\MonBeauVelo\Lib\PreferenceControleur;
    require_once __DIR__ . '/../src/Lib/Psr4AutoloaderClass.php';

    // Initialisation
    $loader = new App\MonBeauVelo\Lib\Psr4AutoloaderClass();
    $loader->register();
    $loader->addNamespace('App\MonBeauVelo', __DIR__ . '/../src');

    // Utilisation de la préférence de contrôleur si elle existe
    $defaultControleur = 'produit';
    if (PreferenceControleur::existe()) {
        $defaultControleur = PreferenceControleur::lire();
    }
    $controleur = $_REQUEST['controleur'] ?? $defaultControleur;
    $nomDeClasseControleur = 'App\MonBeauVelo\Controleur\Controleur' . ucfirst($controleur); // a modifier !
    $action = $_REQUEST['action'] ?? 'afficherListe';

    // Vérifiez si la classe de contrôleur existe
    if (class_exists($nomDeClasseControleur)) {
        $validMethods = get_class_methods($nomDeClasseControleur);

        if (in_array($action, $validMethods)) {
            $nomDeClasseControleur::$action();
        } else {
            ControleurGenerique::afficherErreur("Action non reconnue.");
        }
    } else {
        ControleurGenerique::afficherErreur("Contrôleur non reconnu.");
    }

?>


<!--
Site web : http://webinfo.iutmontp.univ-montp2.fr/~leretourc/projetphp/web/controleurFrontal.php?action=afficherListe&controleur=produit

Les administrateurs sont nommés par le super administrateur, seule personne ayant accès à phpmyadmin.
Il suffit de modifier la valeur 0 en 1 dans la colonne estAdmin de la table p_utilisateur.
-->