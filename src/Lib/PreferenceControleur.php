<?php

namespace App\MonBeauVelo\Lib;

use App\MonBeauVelo\Modele\HTTP\Cookie;

/**
 * Gère les préférences des utilisateurs en utilisant les cookies.
 */
class PreferenceControleur {
    // Clé utilisée pour stocker la préférence de contrôleur dans un cookie.
    private static string $clePreference = "preferenceControleur";

    /**
     * Enregistre la préférence de l'utilisateur dans un cookie.
     *
     * @param string $preference La préférence à enregistrer.
     */
    public static function enregistrer(string $preference) : void {
        Cookie::enregistrer(self::$clePreference, $preference);
    }

    /**
     * Lit la préférence de l'utilisateur depuis un cookie.
     *
     * @return string La préférence enregistrée, ou une chaîne vide si elle n'existe pas.
     */
    public static function lire() : string {
        return Cookie::lire(self::$clePreference) ?? "";
    }

    /**
     * Vérifie si une préférence est enregistrée dans un cookie.
     *
     * @return bool True si la préférence existe, false sinon.
     */
    public static function existe() : bool {
        return Cookie::contient(self::$clePreference);
    }

    /**
     * Supprime la préférence de l'utilisateur.
     */
    public static function supprimer() : void {
        Cookie::supprimer(self::$clePreference);
    }

    /**
     * Récupère la clé utilisée pour stocker la préférence.
     *
     * @return string La clé de préférence.
     */
    public static function getClePreference(): string {
        return self::$clePreference;
    }
}
?>
