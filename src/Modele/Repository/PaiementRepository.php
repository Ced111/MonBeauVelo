<?php
namespace App\MonBeauVelo\Modele\Repository;
use App\MonBeauVelo\Modele\DataObject\AbstractDataObject;
use App\MonBeauVelo\Modele\DataObject\Paiement;
use PDO;
use PDOException;

class PaiementRepository extends AbstractRepository {

    protected function getNomClePrimaire(): string {
        return 'idPaiement';
    }

    protected function getNomTable(): string {
        return 'p_paiement';
    }

    protected function getNomsColonnes(): array {
        return ['idPaiement', 'idCommande', 'montant', 'datePaiement', 'methodePaiement'];
    }

    public function enregistrerPaiement(int $idCommande, float $montant, string $methodePaiement = 'carte'): bool {
        $sql = "INSERT INTO {$this->getNomTable()} (idCommande, montant, datePaiement, methodePaiement) VALUES (:idCommande, :montant, CURDATE(), :methodePaiement)";

        $pdoStatement = ConnexionBaseDeDonnee::getPdo()->prepare($sql);

        $pdoStatement->bindParam(':idCommande', $idCommande, PDO::PARAM_INT);
        $pdoStatement->bindParam(':montant', $montant);
        $pdoStatement->bindParam(':methodePaiement', $methodePaiement);

        try {
            return $pdoStatement->execute();
        } catch (PDOException $e) {
            // Gérer l'erreur
            error_log("Erreur lors de l'enregistrement du paiement: " . $e->getMessage());
            return false;
        }
    }

    protected function construireDepuisTableau(array $tableau): AbstractDataObject {
        return new Paiement(
            $tableau['idPaiement'] ?? null,
            $tableau['idCommande'],
            $tableau['montant'],
            $tableau['datePaiement'],
            $tableau['methodePaiement']
        );
    }

}
?>