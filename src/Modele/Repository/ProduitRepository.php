<?php
namespace App\MonBeauVelo\Modele\Repository;

use App\MonBeauVelo\Modele\DataObject\Produit;
use App\MonBeauVelo\Modele\DataObject\AbstractDataObject;
use PDO;

class ProduitRepository extends AbstractRepository {

    protected function getNomTable(): string {
        return 'p_produit';
    }

    protected function getNomClePrimaire(): string {
        return 'idProduit';
    }

    protected function getNomsColonnes(): array {
        return ['idProduit', 'idCategorie', 'nom', 'marque', 'description', 'prix', 'stock', 'image'];
    }

    public function __construct() {
        $this->tableName = 'p_produit';
        $this->className = 'App\MonBeauVelo\Modele\DataObject\Produit';
    }

    public function recupererProduitAvecCategorie(int $idProduit) {
        $sql = "
        SELECT p.*, c.nomCategorie 
        FROM p_produit p
        JOIN p_categorie c ON p.idCategorie = c.idCategorie
        WHERE p.idProduit = :idProduit
    ";
        $pdoStatement = $this->getPdo()->prepare($sql);
        $pdoStatement->bindParam(':idProduit', $idProduit, PDO::PARAM_INT);
        $pdoStatement->execute();

        $resultat = $pdoStatement->fetch(PDO::FETCH_ASSOC);

        if ($resultat === false) {
            return null;
        }

        return $resultat;
    }

    public function mettreAJourStock(int $idProduit, int $differenceQuantite): bool {
        $sql = "UPDATE p_produit SET stock = GREATEST(0, stock + :difference) WHERE idProduit = :idProduit";
        $pdoStatement = $this->getPdo()->prepare($sql);
        $pdoStatement->bindParam(':difference', $differenceQuantite);
        $pdoStatement->bindParam(':idProduit', $idProduit);
        return $pdoStatement->execute();
    }

    protected function construireDepuisTableau(array $produitFormatTableau): AbstractDataObject  {
        return new Produit(
            $produitFormatTableau['idProduit'],
            $produitFormatTableau['idCategorie'],
            $produitFormatTableau['nom'],
            $produitFormatTableau['marque'],
            $produitFormatTableau['description'],
            $produitFormatTableau['prix'],
            $produitFormatTableau['stock'],
            $produitFormatTableau['image'] ?? null
        );
    }

    public function miseAJourDepuisFormulaire(Produit $produit, array $donneesFormulaire): Produit {
        $produit->setIdCategorie($donneesFormulaire['idCategorie']);
        $produit->setNom($donneesFormulaire['nom']);
        $produit->setMarque($donneesFormulaire['marque']);
        $produit->setDescription($donneesFormulaire['description']);
        $produit->setPrix($donneesFormulaire['prix']);
        $produit->setStock($donneesFormulaire['stock']);
        $produit->setImage($donneesFormulaire['image']);

        return $produit;
    }

    public static function construireDepuisFormulaire(array $tableauFormulaire): Produit {
        return new Produit(
            0,  // idProduit sera défini après insertion dans la DB
            $tableauFormulaire['idCategorie'],
            $tableauFormulaire['nom'],
            $tableauFormulaire['marque'] ?? null,
            $tableauFormulaire['description'],
            $tableauFormulaire['prix'],
            $tableauFormulaire['stock'],
            $tableauFormulaire['image'] ?? null
        );
    }
}
