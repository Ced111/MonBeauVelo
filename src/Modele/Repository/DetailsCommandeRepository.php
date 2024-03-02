<?php
namespace App\MonBeauVelo\Modele\Repository;

use App\MonBeauVelo\Modele\DataObject\DetailsCommande;
use App\MonBeauVelo\Modele\DataObject\AbstractDataObject;
use PDO;

class DetailsCommandeRepository extends AbstractRepository {

    protected function getNomTable(): string {
        return 'p_detailsCommande';
    }

    protected function getNomClePrimaire(): string {
        return 'idCommande, idProduit';
    }

    protected function getNomsColonnes(): array {
        return ['idCommande', 'idProduit', 'quantite', 'prixUnitaire'];
    }

    public function __construct() {
        $this->tableName = 'p_detailsCommande';
        $this->className = 'App\MonBeauVelo\Modele\DataObject\DetailsCommande';
    }

    public function ajouterAuPanier(int $idCommande, int $idProduit, int $quantite): bool {
        // Vérifier si le produit est déjà dans le panier
        $sql = "SELECT quantite FROM p_detailsCommande WHERE idCommande = :idCommande AND idProduit = :idProduit";
        $pdoStatement = $this->getPdo()->prepare($sql);
        $pdoStatement->bindParam(':idCommande', $idCommande);
        $pdoStatement->bindParam(':idProduit', $idProduit);
        $pdoStatement->execute();
        $detail = $pdoStatement->fetch(PDO::FETCH_ASSOC);

        if ($detail) {
            // Mettre à jour la quantité si le produit est déjà présent
            $quantite += $detail['quantite'];
            $sqlUpdate = "UPDATE p_detailsCommande SET quantite = :quantite WHERE idCommande = :idCommande AND idProduit = :idProduit";
            $pdoStatementUpdate = $this->getPdo()->prepare($sqlUpdate);
            $pdoStatementUpdate->bindParam(':quantite', $quantite);
            $pdoStatementUpdate->bindParam(':idCommande', $idCommande);
            $pdoStatementUpdate->bindParam(':idProduit', $idProduit);
            return $pdoStatementUpdate->execute();
        } else {
            // Insérer un nouveau détail de commande pour le produit
            $produitRepository = new ProduitRepository();
            $produit = $produitRepository->recupererParClePrimaire($idProduit);
            if (!$produit) {
                return false; // Produit n'existe pas
            }
            $prixUnitaire = $produit->getPrix();
            $sqlInsert = "INSERT INTO p_detailsCommande (idCommande, idProduit, quantite, prixUnitaire) VALUES (:idCommande, :idProduit, :quantite, :prixUnitaire)";
            $pdoStatementInsert = $this->getPdo()->prepare($sqlInsert);
            $pdoStatementInsert->bindParam(':idCommande', $idCommande);
            $pdoStatementInsert->bindParam(':idProduit', $idProduit);
            $pdoStatementInsert->bindParam(':quantite', $quantite);
            $pdoStatementInsert->bindParam(':prixUnitaire', $prixUnitaire);

            // Mise à jour du stock dans la base de données
            $sqlStock = "UPDATE p_produit SET stock = stock - :quantite WHERE idProduit = :idProduit";
            $pdoStatementStock = $this->getPdo()->prepare($sqlStock);
            $pdoStatementStock->bindParam(':quantite', $quantite);
            $pdoStatementStock->bindParam(':idProduit', $idProduit);

            return $pdoStatementInsert->execute();
        }
    }

    public function recupererDetails(int $idCommande): array {
        $sql = "
        SELECT dc.idCommande, dc.idProduit, p.nom AS nomProduit, p.marque, p.description, c.nomCategorie, dc.quantite, dc.prixUnitaire
        FROM p_detailsCommande dc
        JOIN p_produit p ON dc.idProduit = p.idProduit
        JOIN p_categorie c ON p.idCategorie = c.idCategorie
        WHERE dc.idCommande = :idCommande
    ";
        $pdo = ConnexionBaseDeDonnee::getPdo();
        $pdoStatement = $pdo->prepare($sql);
        $pdoStatement->bindParam(':idCommande', $idCommande, PDO::PARAM_INT);

        $pdoStatement->execute();

        $resultats = $pdoStatement->fetchAll(PDO::FETCH_ASSOC);

        $detailsCommande = [];
        foreach ($resultats as $resultat) {
            $detailsCommande[] = $this->construireDepuisTableau($resultat);
        }

        return $detailsCommande;
    }

    public function recupererDetailsParIdProduitEtIdCommande(int $idProduit, int $idCommande): ?DetailsCommande {
        $sql = "
        SELECT dc.idCommande, dc.idProduit, p.nom AS nomProduit, p.marque, p.description, c.nomCategorie, dc.quantite, dc.prixUnitaire
        FROM p_detailsCommande dc
        JOIN p_produit p ON dc.idProduit = p.idProduit
        JOIN p_categorie c ON p.idCategorie = c.idCategorie
        WHERE dc.idProduit = :idProduit AND dc.idCommande = :idCommande
    ";
        $pdo = ConnexionBaseDeDonnee::getPdo();
        $pdoStatement = $pdo->prepare($sql);
        $pdoStatement->bindParam(':idProduit', $idProduit, PDO::PARAM_INT);
        $pdoStatement->bindParam(':idCommande', $idCommande, PDO::PARAM_INT);

        $pdoStatement->execute();

        $resultat = $pdoStatement->fetch(PDO::FETCH_ASSOC);

        if ($resultat === false) {
            return null;
        }

        return $this->construireDepuisTableau($resultat);
    }

    public function compterProduitsDansCommande(int $idCommande): int {
        $sql = "SELECT COUNT(*) FROM p_detailsCommande WHERE idCommande = :idCommande";
        $pdoStatement = $this->getPdo()->prepare($sql);
        $pdoStatement->bindParam(':idCommande', $idCommande, PDO::PARAM_INT);
        $pdoStatement->execute();
        return (int) $pdoStatement->fetchColumn();
    }

    public function mettreAJourQuantite(int $idCommande, int $idProduit, int $nouvelleQuantite): bool {
        // Récupérez la quantité actuelle du produit dans la commande
        $sqlActuel = "SELECT quantite FROM p_detailsCommande WHERE idCommande = :idCommande AND idProduit = :idProduit";
        $pdoStatementActuel = $this->getPdo()->prepare($sqlActuel);
        $pdoStatementActuel->bindParam(':idCommande', $idCommande, PDO::PARAM_INT);
        $pdoStatementActuel->bindParam(':idProduit', $idProduit, PDO::PARAM_INT);
        $pdoStatementActuel->execute();
        $quantiteActuelle = (int) $pdoStatementActuel->fetchColumn();

        // Calculez la différence de quantité
        $differenceQuantite = $nouvelleQuantite - $quantiteActuelle;

        // Mettez à jour la quantité dans la commande
        $sql = "UPDATE p_detailsCommande SET quantite = :quantite WHERE idCommande = :idCommande AND idProduit = :idProduit";
        $pdoStatement = $this->getPdo()->prepare($sql);
        $pdoStatement->bindParam(':quantite', $nouvelleQuantite, PDO::PARAM_INT);
        $pdoStatement->bindParam(':idCommande', $idCommande, PDO::PARAM_INT);
        $pdoStatement->bindParam(':idProduit', $idProduit, PDO::PARAM_INT);

        if ($pdoStatement->execute()) {
            // Mettez à jour le stock du produit
            $produitRepository = new ProduitRepository();
            $produitRepository->mettreAJourStock($idProduit, -$differenceQuantite); // Réduire le stock si la quantité augmente et vice versa
            return true;
        } else {
            return false;
        }
    }

    public function supprimerProduit(int $idCommande, int $idProduit): bool {
        $pdo = ConnexionBaseDeDonnee::getPdo();

        // Récupérer la quantité du produit
        $sqlQuantite = "SELECT quantite FROM p_detailsCommande WHERE idCommande = :idCommande AND idProduit = :idProduit";
        $pdoStatementQuantite = $pdo->prepare($sqlQuantite);
        $pdoStatementQuantite->bindParam(':idCommande', $idCommande);
        $pdoStatementQuantite->bindParam(':idProduit', $idProduit);
        $pdoStatementQuantite->execute();
        $quantite = $pdoStatementQuantite->fetchColumn();

        // Supprimer le produit du panier
        $sql = "DELETE FROM p_detailsCommande WHERE idCommande = :idCommande AND idProduit = :idProduit";
        $pdoStatement = $pdo->prepare($sql);
        $pdoStatement->bindParam(':idCommande', $idCommande);
        $pdoStatement->bindParam(':idProduit', $idProduit);
        $resultatSuppression = $pdoStatement->execute();

        if ($resultatSuppression) {
            // Mettre à jour le stock du produit
            $produitRepository = new ProduitRepository();
            $produitRepository->mettreAJourStock($idProduit, $quantite);
        }

        return $resultatSuppression;
    }

    protected function construireDepuisTableau(array $detailsCommandeFormatTableau): AbstractDataObject {
        return new DetailsCommande(
            $detailsCommandeFormatTableau['idCommande'],
            $detailsCommandeFormatTableau['idProduit'],
            $detailsCommandeFormatTableau['nomProduit'],
            $detailsCommandeFormatTableau['marque'],
            $detailsCommandeFormatTableau['description'],
            $detailsCommandeFormatTableau['nomCategorie'],
            $detailsCommandeFormatTableau['quantite'],
            $detailsCommandeFormatTableau['prixUnitaire']
        );
    }

}
