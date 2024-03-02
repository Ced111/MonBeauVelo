<?php
namespace App\MonBeauVelo\Modele\HTTP;

use App\MonBeauVelo\Configuration\ConfigurationSite;

use Exception;

class Session {
    private static ?Session $instance = null;

    /**
     * @throws Exception
     */
    private function __construct() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            if (session_start() === false) {
                throw new Exception("La session n'a pas réussi à démarrer.");
            }
        }
    }

    public function contient($nom): bool {
        return isset($_SESSION[$nom]);
    }

    public function enregistrer(string $nom, mixed $valeur): void {
        $_SESSION[$nom] = $valeur;
    }

    public function lire(string $nom): mixed {
        return $this->contient($nom) ? $_SESSION[$nom] : null;
    }

    public function supprimer($nom): void {
        if ($this->contient($nom)) {
            unset($_SESSION[$nom]);
        }
    }

    public function detruire() : void {
        session_unset();
        session_destroy();
        Cookie::supprimer(session_name());
        self::$instance = null;
    }


    public static function getInstance(): Session {
        if (is_null(self::$instance)) {
            self::$instance = new Session();
            self::verifierDerniereActivite();
        }
        return self::$instance;
    }

    public static function verifierDerniereActivite() {
        if (isset($_SESSION['derniereActivite']) && (time() - $_SESSION['derniereActivite'] > ConfigurationSite::DUREE_EXPIRATION)) {
            session_unset();
        }
        $_SESSION['derniereActivite'] = time();
    }

}
?>