<?php
namespace App\MonBeauVelo\Modele\Repository;

use App\MonBeauVelo\Lib\MessageFlash;
use App\MonBeauVelo\Modele\DataObject\Commande;
use App\MonBeauVelo\Modele\DataObject\AbstractDataObject;
use PDO;

class CommandeRepository extends AbstractRepository {

    protected function getNomTable(): string {
        return 'p_commande';
    }

    protected function getNomClePrimaire(): string {
        return 'idCommande';
    }

    protected function getNomsColonnes(): array {
        return ['idCommande', 'idUtilisateur', 'dateCommande', 'statut'];
    }

    public function __construct() {
        $this->tableName = 'p_commande';
        $this->className = 'App\MonBeauVelo\Modele\DataObject\Commande';
    }

    public function trouverOuCreerPanier(int $idUtilisateur): Commande {
        $pdo = ConnexionBaseDeDonnee::getPdo();
        $sql = "SELECT * FROM p_commande WHERE idUtilisateur = :idUtilisateur AND statut = 'panier'";
        $pdoStatement = $pdo->prepare($sql);
        $pdoStatement->bindParam(':idUtilisateur', $idUtilisateur);
        $pdoStatement->execute();
        $commandeData = $pdoStatement->fetch(PDO::FETCH_ASSOC);

        if (!$commandeData) {
            // Insertion de la nouvelle commande dans la base de données
            $sqlInsert = "INSERT INTO p_commande (idUtilisateur, dateCommande, statut) VALUES (:idUtilisateur, :dateCommande, :statut)";
            $pdoStatementInsert = $pdo->prepare($sqlInsert);
            $dateCommande = date('Y-m-d'); // La date actuelle
            $statut = 'panier';
            $pdoStatementInsert->bindParam(':idUtilisateur', $idUtilisateur);
            $pdoStatementInsert->bindParam(':dateCommande', $dateCommande);
            $pdoStatementInsert->bindParam(':statut', $statut);
            $pdoStatementInsert->execute();

            // Récupération de l'ID de la commande nouvellement créée
            $idNouvelleCommande = (int)$pdo->lastInsertId();
            // Création de l'objet Commande avec l'ID récupéré
            $nouvelleCommande = new Commande($idNouvelleCommande, $idUtilisateur, $dateCommande, $statut);
            return $nouvelleCommande;
        } else {
            // Construire un objet Commande à partir des données existantes
            return $this->construireDepuisTableau($commandeData);
        }
    }

    public function recupererCommandesUtilisateur(int $idUtilisateur, string $statut, string $operateur) : array {
        $sql = "SELECT * FROM " . $this->getNomTable() . " WHERE idUtilisateur = :idUtilisateur AND statut $operateur :statut";
        $pdo = ConnexionBaseDeDonnee::getPdo();
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':idUtilisateur', $idUtilisateur);
        $stmt->bindParam(':statut', $statut);
        $stmt->execute();
        $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $commandes = [];
        foreach ($resultats as $resultat) {
            $commandes[] = $this->construireDepuisTableau($resultat);
        }
        return $commandes;
    }

    public function recupererPanierParIdUtilisateur(int $idUtilisateur): ?Commande {
        $sql = "SELECT * FROM " . $this->getNomTable() . " WHERE idUtilisateur = :idUtilisateur AND statut = 'panier'";
        $pdo = ConnexionBaseDeDonnee::getPdo();
        $pdoStatement = $pdo->prepare($sql);
        $pdoStatement->bindParam(':idUtilisateur', $idUtilisateur);

        $pdoStatement->execute();

        $result = $pdoStatement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            return null;
        }

        return $this->construireDepuisTableau($result);
    }

    public function compterArticlesDansCommande(int $idCommande): int {
        $pdo = ConnexionBaseDeDonnee::getPdo();
        $sql = "SELECT COUNT(*) FROM p_detailsCommande WHERE idCommande = :idCommande";
        $pdoStatement = $pdo->prepare($sql);
        $pdoStatement->bindParam(':idCommande', $idCommande);
        $pdoStatement->execute();
        return (int) $pdoStatement->fetchColumn();
    }

    public function sauvegarderPanierSessionEnBase(int $idUtilisateur, array $detailsPanier) {
        $commande = $this->trouverOuCreerPanier($idUtilisateur);
        $detailsCommandeRepository = new DetailsCommandeRepository();
        $produitRepository = new ProduitRepository();
        $produitsModifies = [];
        $panierModifie = false;

        foreach ($detailsPanier as $idProduit => $details) {
            $quantiteDemandee = $details['quantite'];

            // Vérifier le stock disponible
            $produit = $produitRepository->recupererParClePrimaire($idProduit);
            if ($produit !== null) {
                $stockDisponible = $produit->getStock();

                if ($quantiteDemandee > $stockDisponible) {
                    // Ajuster la quantité à celle du stock disponible
                    $quantiteDemandee = $stockDisponible;
                    $produitsModifies[] = $produit->getNom();
                    $panierModifie = true;
                }

                // Ajouter ou mettre à jour dans la base de données
                $detailsCommandeRepository->ajouterAuPanier($commande->getIdCommande(), $idProduit, $quantiteDemandee);
                $produitRepository->mettreAJourStock($idProduit, -$quantiteDemandee);
            }
        }
        if ($panierModifie) {
            MessageFlash::ajouter('info', "Votre panier a été sauvegardé. La quantité de certains produits a été modifiée pour correspondre au stock disponible : " . implode(", ", $produitsModifies) . ".");
        } else {
            MessageFlash::ajouter('success', "Votre panier a été sauvegardé.");
        }
    }

    protected function construireDepuisTableau(array $commandeFormatTableau): AbstractDataObject  {
        return new Commande(
            $commandeFormatTableau['idCommande'],
            $commandeFormatTableau['idUtilisateur'],
            $commandeFormatTableau['dateCommande'],
            $commandeFormatTableau['statut']
        );
    }

    public function miseAJourDepuisFormulaire(Commande $commande, array $donneesFormulaire): Commande {
        $commande->setStatut($donneesFormulaire['statut']);
        return $commande;
    }
}
