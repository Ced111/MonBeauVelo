<?php

namespace App\MonBeauVelo\Modele\Repository;

use App\MonBeauVelo\Lib\MotDePasse;
use App\MonBeauVelo\Modele\DataObject\Utilisateur;
use App\MonBeauVelo\Modele\DataObject\AbstractDataObject;

class UtilisateurRepository extends AbstractRepository {

    protected function getNomTable(): string {
        return 'p_utilisateur';
    }

    protected function getNomClePrimaire(): string {
        return 'idUtilisateur';
    }

    protected function getNomsColonnes(): array {
        return ['idUtilisateur', 'nom', 'prenom', 'adresse', 'telephone', 'mdpHache', 'estAdmin', 'email', 'emailAValider', 'nonce'];
    }

    public function recupererParEmail(string $email): ?Utilisateur {
        $result = $this->recupererParColonne('email', $email);
        if ($result instanceof Utilisateur) {
            return $result;
        }
        return null;
    }

    public function emailEstDejaUtilise(string $email): bool {
        $sql = "SELECT COUNT(*) FROM " . $this->getNomTable() . " WHERE email = :email OR emailAValider = :email";
        $pdo = ConnexionBaseDeDonnee::getPdo();
        $pdoStatement = $pdo->prepare($sql);
        $pdoStatement->bindParam(':email', $email);

        $pdoStatement->execute();
        return $pdoStatement->fetchColumn() > 0;
    }


    public function __construct() {
        $this->tableName = 'p_utilisateur';
        $this->className = 'App\MonBeauVelo\Modele\DataObject\Utilisateur';
    }

    protected function construireDepuisTableau(array $utilisateurTableau): AbstractDataObject {
        return new Utilisateur(
            $utilisateurTableau["idUtilisateur"],
            $utilisateurTableau["nom"],
            $utilisateurTableau["prenom"],
            $utilisateurTableau["adresse"],
            $utilisateurTableau["telephone"],
            $utilisateurTableau["mdpHache"],
            $utilisateurTableau["estAdmin"] == 1,
            $utilisateurTableau["email"],
            $utilisateurTableau["emailAValider"],
            $utilisateurTableau["nonce"]
        );
    }

    public static function construireDepuisFormulaire(array $tableauFormulaire): Utilisateur {
        $hashedPassword = MotDePasse::hacher($tableauFormulaire['mdp']);
        $estAdmin = isset($tableauFormulaire['estAdmin']) && $tableauFormulaire['estAdmin'] == 'on';
        $nonce = MotDePasse::genererChaineAleatoire();
        return new Utilisateur(
            0,  // idUtilisateur
            $tableauFormulaire['nom'],
            $tableauFormulaire['prenom'],
            $tableauFormulaire['adresse'],
            $tableauFormulaire['telephone'] ?? null,
            $hashedPassword,
            $estAdmin,
            null,  // email
            $tableauFormulaire['email'],  // emailAValider
            $nonce
        );
    }

    public function miseAJourDepuisFormulaire(Utilisateur $utilisateur, array $donneesFormulaire): Utilisateur {
        $utilisateur->setNom($donneesFormulaire['nom']);
        $utilisateur->setPrenom($donneesFormulaire['prenom']);
        $utilisateur->setAdresse($donneesFormulaire['adresse']);
        $utilisateur->setTelephone($donneesFormulaire['telephone'] ?? null);

        return $utilisateur;
    }

}

