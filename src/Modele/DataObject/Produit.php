<?php
namespace App\MonBeauVelo\Modele\DataObject;
require_once 'AbstractDataObject.php';

class Produit extends AbstractDataObject {
    private int $idProduit;
    private int $idCategorie;
    private string $nom;
    private ?string $marque;
    private string $description;
    private float $prix;
    private int $stock;
    private ?string $image;

    public function __construct(
        int $idProduit,
        int $idCategorie,
        string $nom,
        ?string $marque,
        string $description,
        float $prix,
        int $stock,
        ?string $image
    ) {
        $this->idProduit = $idProduit;
        $this->idCategorie = $idCategorie;
        $this->nom = $nom;
        $this->marque = $marque;
        $this->description = $description;
        $this->prix = $prix;
        $this->stock = $stock;
        $this->image = $image;
    }

    public function formatTableau(): array {
        return array(
            "idProduitTag" => $this->getIdProduit(),
            "idCategorieTag" => $this->getIdCategorie(),
            "nomTag" => $this->getNom(),
            "marqueTag" => $this->getMarque(),
            "descriptionTag" => $this->getDescription(),
            "prixTag" => $this->getPrix(),
            "stockTag" => $this->getStock(),
            "imageTag" => $this->getImage(),
        );
    }

    public function setIdProduit(int $idProduit) {
        $this->idProduit = $idProduit;
    }

    public function setIdCategorie(int $idCategorie) {
        $this->idCategorie = $idCategorie;
    }

    public function setNom(string $nom) {
        $this->nom = $nom;
    }

    public function setMarque(?string $marque) {
        $this->marque = $marque;
    }

    public function getMarque(): ?string {
        return $this->marque;
    }

    public function setDescription(string $description) {
        $this->description = $description;
    }

    public function setPrix(float $prix) {
        $this->prix = $prix;
    }

    public function setStock(int $stock) {
        $this->stock = $stock;
    }

    public function getIdProduit(): int {
        return $this->idProduit;
    }

    public function getIdCategorie(): int {
        return $this->idCategorie;
    }

    public function getNom(): string {
        return $this->nom;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getPrix(): float {
        return $this->prix;
    }

    public function getStock(): int {
        return $this->stock;
    }

    public function getImage(): ?string {
        return $this->image;
    }

    public function setImage(?string $image) {
        $this->image = $image;
    }

}
