<?php
namespace App\MonBeauVelo\Lib;

use App\MonBeauVelo\Modele\HTTP\Session;

class PanierSession {
    private array $detailsPanier;
    private Session $session;

    public function __construct() {
        $this->session = Session::getInstance();
        $this->detailsPanier = $this->session->lire('panier') ?? [];
    }

    public function ajouterAuPanierSession(int $idProduit, int $quantite, float $prixUnitaire): void {
        if (isset($this->detailsPanier[$idProduit])) {
            // Mise à jour de la quantité si le produit existe déjà dans le panier
            $this->detailsPanier[$idProduit]['quantite'] += $quantite;
        } else {
            // Ajout d'un nouveau produit dans le panier
            $this->detailsPanier[$idProduit] = [
                'quantite' => $quantite,
                'prixUnitaire' => $prixUnitaire
            ];
        }
        $this->session->enregistrer('panier', $this->detailsPanier);
    }

    public function getDetailsPanier(): array {
        return $this->detailsPanier;
    }

    public function supprimerProduit(int $idProduit): void {
        unset($this->detailsPanier[$idProduit]);
        $this->session->enregistrer('panier', $this->detailsPanier);
    }

    public function viderPanier(): void {
        $this->detailsPanier = [];
        $this->session->enregistrer('panier', $this->detailsPanier);
    }

    public function calculerTotal(): float {
        $total = 0.0;
        foreach ($this->detailsPanier as $idProduit => $details) {
            $total += $details['quantite'] * $details['prixUnitaire'];
        }
        return $total;
    }

    public function modifierQuantite(int $idProduit, int $nouvelleQuantite): void {
        if (isset($this->detailsPanier[$idProduit])) {
            // Mise à jour de la quantité si le produit existe déjà dans le panier
            $this->detailsPanier[$idProduit]['quantite'] = $nouvelleQuantite;
            $this->session->enregistrer('panier', $this->detailsPanier);
        }
    }

    public static function aDesArticles(): bool {
        $session = Session::getInstance();
        return !empty($session->lire('panier'));
    }
}
?>
