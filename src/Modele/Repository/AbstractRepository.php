<?php

namespace App\MonBeauVelo\Modele\Repository;

use App\MonBeauVelo\Modele\DataObject\AbstractDataObject;
use App\MonBeauVelo\Modele\DataObject\Produit;
use App\MonBeauVelo\Modele\DataObject\Utilisateur;
use PDO;
use PDOException;

abstract class AbstractRepository {
    protected string $tableName;
    protected string $className;

    protected function getPdo(): PDO {
        return ConnexionBaseDeDonnee::getPdo();
    }

    public function sauvegarder(AbstractDataObject $dataObject): bool {
        $pdo = ConnexionBaseDeDonnee::getPdo();

        $table = $this->getNomTable();
        $columnsArray = $this->getNomsColonnes();
        $columns = implode(", ", $columnsArray);

        $valuesPlaceholders = "";
        foreach ($columnsArray as $column) {
            $valuesPlaceholders = $valuesPlaceholders . ":{$column}Tag, ";
        }
        $valuesPlaceholders = substr($valuesPlaceholders, 0, -2);


        $sql = "INSERT INTO $table ($columns) VALUES ($valuesPlaceholders)";
        $pdoStatement = $pdo->prepare($sql);

        try {
            if ($pdoStatement->execute($dataObject->formatTableau())) {
                $lastInsertId = $pdo->lastInsertId();
                if ($dataObject instanceof Utilisateur && $dataObject->getIdUtilisateur() == 0) {
                    $dataObject->setIdUtilisateur((int)$lastInsertId);
                } elseif ($dataObject instanceof Produit && $dataObject->getIdProduit() == 0) {
                    $dataObject->setIdProduit((int)$lastInsertId);
                }
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log("Erreur lors de la sauvegarde dans la table $table: " . $e->getMessage());
            error_log("SQL: $sql");
            error_log("Données tentées d'insertion: " . print_r($dataObject->formatTableau(), true));
            error_log("Détails de l'erreur PDO: " . print_r($e->errorInfo, true));
            return false;
        }
    }

    public function mettreAJour(AbstractDataObject $object): bool {
        $nomTable = $this->getNomTable();
        $nomsColonnes = $this->getNomsColonnes();

        $updateParts = [];
        foreach ($nomsColonnes as $colonne) {
            $updateParts[] = "{$colonne} = :{$colonne}Tag";
        }

        $nomClePrimaire = method_exists($object, 'getNomClePrimaire') ? $object->getNomClePrimaire() : $this->getNomClePrimaire();
        $whereParts = [];
        foreach ((array) $nomClePrimaire as $cle) {
            $whereParts[] = "{$cle} = :{$cle}Tag";
        }
        $sql = "UPDATE {$nomTable} SET " . implode(', ', $updateParts) . " WHERE " . implode(' AND ', $whereParts);

        $pdoStatement = ConnexionBaseDeDonnee::getPdo()->prepare($sql);

        $values = $object->formatTableau();

        foreach ((array) $nomClePrimaire as $cle) {
            $getterMethod = "get" . ucfirst($cle);
            $values[$cle . 'Tag'] = $object->$getterMethod();
        }

        try {
            $result = $pdoStatement->execute($values);
            return $result;
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour de {$nomTable}: " . $e->getMessage());
            return false;
        }
    }


    public function supprimer(string $valeurClePrimaire): bool {
        $nomTable = $this->getNomTable();
        $nomClePrimaire = $this->getNomClePrimaire();
        $sql = "DELETE FROM {$nomTable} WHERE {$nomClePrimaire} = :valeurClePrimaireTag";

        $pdoStatement = ConnexionBaseDeDonnee::getPdo()->prepare($sql);
        $values = array("valeurClePrimaireTag" => $valeurClePrimaire);

        try {
            $pdoStatement->execute($values);
            return true;
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression dans {$nomTable}: " . $e->getMessage());
            return false;
        }
    }

    public function recuperer(): array {
        $sql = "SELECT * FROM " . $this->getNomTable();
        $pdoStatement = ConnexionBaseDeDonnee::getPdo()->prepare($sql);

        $pdoStatement->execute();
        $resultats = $pdoStatement->fetchAll();

        if ($resultats === false) {
            return [];
        }

        $objetListe = [];
        foreach ($resultats as $ligneTableau) {
            $objetListe[] = $this->construireDepuisTableau($ligneTableau);
        }
        return $objetListe;
    }


    public function recupererParClePrimaire(string $valeurClePrimaire): ?AbstractDataObject {
        $sql = "SELECT * FROM " . $this->getNomTable() . " WHERE " . $this->getNomClePrimaire() . " = :valeurClePrimaire";
        $pdo = ConnexionBaseDeDonnee::getPdo();
        $pdoStatement = $pdo->prepare($sql);
        $pdoStatement->bindParam(':valeurClePrimaire', $valeurClePrimaire);

        $pdoStatement->execute([
            ':valeurClePrimaire' => $valeurClePrimaire
        ]);

        $result = $pdoStatement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            return null;
        }

        return $this->construireDepuisTableau($result);
    }

    public function recupererParColonne(string $nomColonne, $valeurColonne): ?AbstractDataObject {
        $sql = "SELECT * FROM " . $this->getNomTable() . " WHERE {$nomColonne} = :valeurColonne";
        $pdo = ConnexionBaseDeDonnee::getPdo();
        $pdoStatement = $pdo->prepare($sql);
        $pdoStatement->bindParam(':valeurColonne', $valeurColonne);

        $pdoStatement->execute();
        $result = $pdoStatement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            return null;
        }

        return $this->construireDepuisTableau($result);
    }


    protected abstract function getNomsColonnes(): array;

    protected abstract function getNomTable(): string;

    protected abstract function getNomClePrimaire(): string;

    protected abstract function construireDepuisTableau(array $objetFormatTableau): AbstractDataObject;


}
?>
