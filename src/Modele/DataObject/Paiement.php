<?php
namespace App\MonBeauVelo\Modele\DataObject;
use DateTime;

require_once 'AbstractDataObject.php';
class Paiement extends AbstractDataObject {
    private int $idPaiement;
    private int $idCommande;
    private float $montant;
    private DateTime $datePaiement;
    private string $methodePaiement;

    public function __construct(
        int $idCommande,
        float $montant,
        DateTime $datePaiement,
        string $methodePaiement,
        int $idPaiement = 0 // Id peut être 0 pour un nouvel enregistrement
    ) {
        $this->idPaiement = $idPaiement;
        $this->idCommande = $idCommande;
        $this->montant = $montant;
        $this->datePaiement = $datePaiement;
        $this->methodePaiement = $methodePaiement;
    }

    public function getIdPaiement(): int {
        return $this->idPaiement;
    }

    public function getIdCommande(): int {
        return $this->idCommande;
    }

    public function getMontant(): float {
        return $this->montant;
    }

    public function getDatePaiement(): DateTime {
        return $this->datePaiement;
    }

    public function getMethodePaiement(): string {
        return $this->methodePaiement;
    }

    public function formatTableau(): array {
        return [
            'idPaiementTag' => $this->getIdPaiement(),
            'idCommandeTag' => $this->getIdCommande(),
            'montantTag' => $this->getMontant(),
            'datePaiementTag' => $this->getDatePaiement()->format('Y-m-d'),
            'methodePaiementTag' => $this->getMethodePaiement(),
        ];
    }
}
?>