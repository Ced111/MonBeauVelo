<?php
namespace App\MonBeauVelo\Configuration;

class ConfigurationSite {
    const DUREE_EXPIRATION = 1800; // Durée d'expiration en secondes (30 minutes ici)

    public static function getURLAbsolue(): string {
        // return 'http://localhost/td-php/ProjetPHP/web/controleurFrontal.php';
        return 'http://webinfo.iutmontp.univ-montp2.fr/~leretourc/projetphp/web/controleurFrontal.php';
    }

    public static function getDebug(): bool {
        // true pour le mode développement (débogage) = get
        // false pour le mode production = post
        return true;
    }

}
?>