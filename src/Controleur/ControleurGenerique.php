<?php

namespace App\MonBeauVelo\Controleur;

use App\MonBeauVelo\Lib\MessageFlash;
use App\MonBeauVelo\Lib\PreferenceControleur;
use App\MonBeauVelo\Modele\HTTP\Session;
use App\MonBeauVelo\Modele\HTTP\Cookie;

class ControleurGenerique {

    protected static function afficherVue(string $cheminVue, array $parametres = []) {
        extract($parametres);

        $pagetitle = "Mon Beau Vélo";
        $cheminVueBody = $cheminVue;

        $messagesFlash = MessageFlash::lireTousMessages();

        require __DIR__ . '/../vue/vueGenerale.php';
    }


    public static function afficherErreur(string $message) {
        echo '<p style="color: red;">Erreur: ' . htmlspecialchars($message) . '</p>';
    }


    public static function formulairePreference(): void {
        ControleurGenerique::afficherVue('formulairePreference.php', [
            'pagetitle' => 'Préférences utilisateur',
            'cheminVueBody' => 'formulairePreference.php'
        ]);
    }


    public static function enregistrerPreference(): void {
        if (isset($_POST['controleur_defaut'])) {
            $preference = $_POST['controleur_defaut'];
            PreferenceControleur::enregistrer($preference);

            MessageFlash::ajouter('success', 'Votre préférence est enregistrée !');
            ControleurGenerique::redirectionVersURL('controleurFrontal.php');
        } else {
            MessageFlash::ajouter('danger', 'Erreur lors de l’enregistrement de la préférence.');
            ControleurGenerique::redirectionVersURL('controleurFrontal.php');
        }
    }


    public static function redirectionVersURL(string $url) {
        header("Location: $url");
        exit();
    }

}
