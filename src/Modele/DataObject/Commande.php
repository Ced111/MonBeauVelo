<?php
namespace App\MonBeauVelo\Modele\DataObject;

use App\MonBeauVelo\Modele\Repository\DetailsCommandeRepository;

require_once 'AbstractDataObject.php';

class Commande extends AbstractDataObject {
    private int $idCommande;
    private int $idUtilisateur;
    private string $dateCommande;
    private string $statut;

    public function __construct(
        int $idCommande,
        int $idUtilisateur,
        string $dateCommande,
        string $statut
    ) {
        $this->idCommande = $idCommande;
        $this->idUtilisateur = $idUtilisateur;
        $this->dateCommande = $dateCommande;
        $this->statut = $statut;
    }

    public function formatTableau(): array {
        return array(
            "idCommandeTag" => $this->getIdCommande(),
            "idUtilisateurTag" => $this->getIdUtilisateur(),
            "dateCommandeTag" => $this->getDateCommande(),
            "statutTag" => $this->getStatut(),
        );
    }

    public function getIdCommande(): int {
        return $this->idCommande;
    }

    public function setIdCommande(int $idCommande): void {
        $this->idCommande = $idCommande;
    }

    public function getIdUtilisateur(): int {
        return $this->idUtilisateur;
    }

    public function setIdUtilisateur(int $idUtilisateur): void {
        $this->idUtilisateur = $idUtilisateur;
    }

    public function getDateCommande(): string {
        return $this->dateCommande;
    }

    public function setDateCommande(string $dateCommande): void {
        $this->dateCommande = $dateCommande;
    }

    public function getStatut(): string {
        return $this->statut;
    }

    public function setStatut(string $statut): void {
        $this->statut = $statut;
    }

    public function getDetailsCommande(): array {
        $detailsCommandeRepository = new DetailsCommandeRepository();
        return $detailsCommandeRepository->recupererDetails($this->idCommande);
    }

    public function calculerTotal(): float {
        $total = 0.0;
        $details = $this->getDetailsCommande();
        foreach ($details as $detail) {
            $total += $detail->getQuantite() * $detail->getPrixUnitaire();
        }
        return $total;
    }

}
