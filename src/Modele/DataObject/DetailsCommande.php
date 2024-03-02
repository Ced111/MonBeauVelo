<?php
namespace App\MonBeauVelo\Modele\DataObject;
require_once 'AbstractDataObject.php';

class DetailsCommande extends AbstractDataObject {
    private int $idCommande;
    private int $idProduit;
    private string $nomProduit;
    private string $marque;
    private string $description;
    private string $nomCategorie;
    private int $quantite;
    private float $prixUnitaire;

    public function __construct(int $idCommande, int $idProduit, string $nomProduit, string $marque, string $description, string $nomCategorie, int $quantite, float $prixUnitaire) {
        $this->idCommande = $idCommande;
        $this->idProduit = $idProduit;
        $this->nomProduit = $nomProduit;
        $this->marque = $marque;
        $this->description = $description;
        $this->nomCategorie = $nomCategorie;
        $this->quantite = $quantite;
        $this->prixUnitaire = $prixUnitaire;
    }

    public function getNomClePrimaire(): array {
        return ['idCommande', 'idProduit'];
    }

    public function getIdCommande(): int {
        return $this->idCommande;
    }

    public function getIdProduit(): int {
        return $this->idProduit;
    }

    public function getNomProduit(): string {
        return $this->nomProduit;
    }

    public function getQuantite(): int {
        return $this->quantite;
    }

    public function getPrixUnitaire(): float {
        return $this->prixUnitaire;
    }

    public function getMarque(): string {
        return $this->marque;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getNomCategorie(): string {
        return $this->nomCategorie;
    }

    public function formatTableau(): array {
        return [
            'idCommandeTag' => $this->idCommande,
            'idProduitTag' => $this->idProduit,
            'quantiteTag' => $this->quantite,
            'prixUnitaireTag' => $this->prixUnitaire,
        ];
    }
}