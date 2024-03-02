<?php

namespace App\MonBeauVelo\Modele\HTTP;

/**
 * Classe utilitaire pour gérer les cookies.
 */
class Cookie {
    /**
     * Enregistre un cookie avec une clé et une valeur données.
     *
     * @param string $cle La clé du cookie.
     * @param mixed $valeur La valeur du cookie.
     * @param int|null $dureeExpiration La durée d'expiration du cookie en secondes.
     */
    public static function enregistrer(string $cle, $valeur, ?int $dureeExpiration = null): void {
        setcookie($cle, $valeur, $dureeExpiration ?? 0, "/");
    }

    /**
     * Lit la valeur d'un cookie en utilisant sa clé.
     *
     * @param string $cle La clé du cookie.
     * @return mixed La valeur du cookie ou null si le cookie n'existe pas.
     */
    public static function lire(string $cle) {
        return $_COOKIE[$cle] ?? null;
    }

    /**
     * Vérifie si un cookie avec une clé donnée existe.
     *
     * @param string $cle La clé du cookie.
     * @return bool True si le cookie existe, false sinon.
     */
    public static function contient(string $cle) : bool {
        return isset($_COOKIE[$cle]);
    }

    /**
     * Supprime un cookie en utilisant sa clé.
     *
     * @param string $cle La clé du cookie à supprimer.
     */
    public static function supprimer(string $cle) : void {
        unset($_COOKIE[$cle]);
        setcookie($cle, "", 1, "/");
    }
}
?>
