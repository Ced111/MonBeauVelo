<?php
namespace App\MonBeauVelo\Lib;
use App\MonBeauVelo\Modele\Repository\UtilisateurRepository;
use App\MonBeauVelo\Modele\HTTP\Session;

class ConnexionUtilisateur {
    private static string $cleConnexion = "_utilisateurConnecte";

    public static function connecter(int $idUtilisateur): void {
        $session = Session::getInstance();
        $session->enregistrer(self::$cleConnexion, $idUtilisateur);
    }

    public static function estConnecte(): bool {
        $session = Session::getInstance();

        if ($session->contient(self::$cleConnexion)) {
            $idUtilisateurConnecte = $session->lire(self::$cleConnexion);
            $utilisateurRepository = new UtilisateurRepository();
            $utilisateur = $utilisateurRepository->recupererParClePrimaire($idUtilisateurConnecte);

            if (!$utilisateur) {
                self::deconnecter();
                return false;
            }

            return true;
        }

        return false;
    }


    public static function deconnecter(): void {
        $session = Session::getInstance();
        $session->supprimer(self::$cleConnexion);
    }

    public static function getIdUtilisateurConnecte(): ?int {
        $session = Session::getInstance();
        return $session->lire(self::$cleConnexion);
    }

    public static function estUtilisateur($idUtilisateur): bool {
        if (self::estConnecte()) {
            $idUtilisateurConnecte = self::getIdUtilisateurConnecte();
            return (int)$idUtilisateurConnecte === (int)$idUtilisateur;
        }
        return false;
    }

    public static function estAdministrateur() : bool {
        if (!self::estConnecte()) {
            return false;
        }

        $idUtilisateur = self::getIdUtilisateurConnecte();
        if ($idUtilisateur === null) {
            return false;
        }

        $utilisateurRepository = new UtilisateurRepository();
        $utilisateur = $utilisateurRepository->recupererParClePrimaire($idUtilisateur);

        return $utilisateur !== null && $utilisateur->getEstAdmin();
    }
}

?>
