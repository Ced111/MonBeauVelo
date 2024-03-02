<?php
namespace App\MonBeauVelo\Controleur;

use App\MonBeauVelo\Lib\ConnexionUtilisateur;
use App\MonBeauVelo\Lib\PanierSession;
use App\MonBeauVelo\Modele\DataObject\Commande;
use App\MonBeauVelo\Modele\HTTP\Session;
use App\MonBeauVelo\Modele\Repository\CommandeRepository;
use App\MonBeauVelo\Modele\Repository\DetailsCommandeRepository;
use App\MonBeauVelo\Lib\MessageFlash;
use App\MonBeauVelo\Modele\Repository\PaiementRepository;
use App\MonBeauVelo\Modele\Repository\ProduitRepository;

class ControleurCommande extends ControleurGenerique {

    public static function afficherListe() : void {
        if (!ConnexionUtilisateur::estAdministrateur()) {
            MessageFlash::ajouter('danger', "Vous n'avez pas l'autorisation d'accéder à la liste de toutes les commandes.");
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherListe');
            return;
        }
        $commandeRepository = new CommandeRepository();
        $commandes = $commandeRepository->recuperer();
        ControleurGenerique::afficherVue('commande/liste.php', [
            'commandes' => $commandes,
            'pagetitle' => 'Liste des commandes',
            'cheminVueBody' => 'commande/liste.php'
        ]);
    }


    public static function afficherListeCommandesUtilisateur() : void {
        $commandeRepository = new CommandeRepository();
        $idUtilisateur = ConnexionUtilisateur::getIdUtilisateurConnecte();
        $commandes = $commandeRepository->recupererCommandesUtilisateur($idUtilisateur, 'panier', '!=');
        self::afficherVue('commande/listeMesCommandes.php', [
            'commandes' => $commandes,
            'pagetitle' => 'Mes commandes',
            'cheminVueBody' => 'commande/listeMesCommandes.php'
        ]);
    }


    public static function afficherPanier() : void {
        $idUtilisateur = ConnexionUtilisateur::getIdUtilisateurConnecte();
        if ($idUtilisateur === null) {
            if (!PanierSession::aDesArticles()) {
                ControleurGenerique::afficherVue('commande/detailPanier.php', [
                    'commande' => null,
                    'details' => [],
                    'pagetitle' => 'Mon panier',
                    'cheminVueBody' => 'commande/detailPanier.php'
                ]);
                return;
            }
            ControleurCommande::afficherPanierSession();
            return;
        }

        $commandeRepository = new CommandeRepository();
        $commande = $commandeRepository->recupererPanierParIdUtilisateur($idUtilisateur);

        $details = [];
        if ($commande !== null) {
            $detailsCommandeRepository = new DetailsCommandeRepository();
            $details = $detailsCommandeRepository->recupererDetails($commande->getIdCommande());
        }

        ControleurGenerique::afficherVue('commande/detailPanier.php', [
            'commande' => $commande,
            'details' => $details,
            'pagetitle' => 'Mon panier',
            'cheminVueBody' => 'commande/detailPanier.php'
        ]);
    }


    public static function afficherPanierSession() : void {
        $sessionPanier = new PanierSession();
        $detailsPanier = $sessionPanier->getDetailsPanier();

        if (empty($detailsPanier)) {
            MessageFlash::ajouter('info', 'Votre panier est vide.');
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherListe');
            return;
        }

        ControleurGenerique::afficherVue('commande/detailPanierSession.php', [
            'detailsPanier' => $detailsPanier,
            'pagetitle' => 'Mon panier',
            'cheminVueBody' => 'commande/detailPanierSession.php'
        ]);
    }


    public static function afficherFormulaireMiseAJour() {
        $commandeRepository = new CommandeRepository();

        if (isset($_REQUEST['idCommande'])) {
            $idCommande = htmlspecialchars($_REQUEST['idCommande']);
            $commande = $commandeRepository->recupererParClePrimaire($idCommande);

            if ($commande) {
                ControleurGenerique::afficherVue('commande/formulaireMiseAJour.php', [
                    'commande' => $commande,
                    'pagetitle' => 'Mettre à jour une commande'
                ]);
            } else {
                MessageFlash::ajouter('danger', "La commande demandée n'existe pas.");
                ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherListe');
            }
        } else {
            MessageFlash::ajouter('danger', "ID manquante.");
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherListe');
        }
    }


    public static function ajouterPanier(): void {
        // Récupérer le produit et vérifier la validité
        if (!isset($_REQUEST['idProduit']) || !is_numeric($_REQUEST['idProduit'])) {
            MessageFlash::ajouter('danger', "Produit invalide.");
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherListe');
            return;
        }
        $idProduit = (int)$_REQUEST['idProduit'];
        $produitRepository = new ProduitRepository();
        $produit = $produitRepository->recupererParClePrimaire($idProduit);

        if (!$produit) {
            MessageFlash::ajouter('danger', "Produit non trouvé.");
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherListe');
            return;
        }

        // Vérifier le stock disponible
        if ($produit->getStock() <= 0) {
            MessageFlash::ajouter('danger', "Stock épuisé pour ce produit.");
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherDetail&idProduit=' . $idProduit);
            return;
        }

        // Récupérer et vérifier la quantité
        $quantite = $_REQUEST['quantite'] ?? 1;
        if (!is_numeric($quantite) || $quantite <= 0) {
            MessageFlash::ajouter('danger', "Quantité invalide.");
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherDetail&idProduit=' . $idProduit);
            return;
        }
        $quantite = (int)$quantite;

        // Gérer le panier selon que l'utilisateur est connecté ou non
        $idUtilisateur = ConnexionUtilisateur::getIdUtilisateurConnecte();
        if (!$idUtilisateur) {
            $panier = new PanierSession();
            $panier->ajouterAuPanierSession($idProduit, $quantite, $produit->getPrix());
            MessageFlash::ajouter('success', "Produit ajouté au panier avec succès.");
        } else {
            $commandeRepository = new CommandeRepository();
            $commande = $commandeRepository->trouverOuCreerPanier($idUtilisateur);

            $session = Session::getInstance();
            $session->enregistrer('idCommande', $commande->getIdCommande());

            $detailsCommandeRepository = new DetailsCommandeRepository();
            if ($detailsCommandeRepository->ajouterAuPanier($commande->getIdCommande(), $idProduit, $quantite, $produit->getPrix())) {
                MessageFlash::ajouter('success', "Produit ajouté au panier avec succès.");
            } else {
                MessageFlash::ajouter('danger', "Erreur lors de l'ajout du produit au panier.");
            }
        }
        ControleurGenerique::redirectionVersURL('controleurFrontal.php?controleur=Produit&action=afficherDetail&idProduit=' . $idProduit);
    }

    public static function mettreAJour(): void {
        if (isset($_REQUEST['idCommande'], $_REQUEST['statut'])) {
            $commandeRepository = new CommandeRepository();
            $commandeOriginal = $commandeRepository->recupererParClePrimaire($_REQUEST['idCommande']);

            if (!$commandeOriginal) {
                MessageFlash::ajouter('danger', "Commande inexistante.");
                ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherListe');
                return;
            }

            $commandeMiseAJour = $commandeRepository->miseAJourDepuisFormulaire($commandeOriginal, $_REQUEST);

            if ($commandeRepository->mettreAJour($commandeMiseAJour)) {
                MessageFlash::ajouter('success', "Commande mise à jour avec succès.");
                ControleurGenerique::redirectionVersURL("controleurFrontal.php?controleur=commande&action=afficherDetail&idCommande=" . $_REQUEST['idCommande']);
            } else {
                MessageFlash::ajouter('danger', "Erreur lors de la mise à jour de la commande.");
                ControleurGenerique::redirectionVersURL("controleurFrontal.php?controleur=commande&action=afficherDetail&idCommande=" . $_REQUEST['idCommande']);
            }
        } else {
            MessageFlash::ajouter('danger', "Données incomplètes pour la mise à jour.");
            ControleurGenerique::redirectionVersURL("controleurFrontal.php?controleur=commande&action=afficherDetail&idCommande=" . $_REQUEST['idCommande']);
        }
    }


    public static function modifierQuantite(): void {
        if (isset($_REQUEST['idCommande'], $_REQUEST['idProduit'], $_REQUEST['quantite'])) {
            $idCommande = (int) $_REQUEST['idCommande'];
            $idProduit = (int) $_REQUEST['idProduit'];
            $quantite = (int) $_REQUEST['quantite'];

            if ($quantite < 0) {
                MessageFlash::ajouter('danger', "La quantité ne peut pas être négative.");
                ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherPanier&controleur=commande');
                return;
            }

            $produitRepository = new ProduitRepository();
            $produit = $produitRepository->recupererParClePrimaire($idProduit);

            if ($produit === null || $quantite > $produit->getStock()) {
                MessageFlash::ajouter('danger', "Quantité demandée non disponible en stock.");
                ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherPanier&controleur=commande');
                return;
            }

            $detailsCommandeRepository = new DetailsCommandeRepository();
            $commandeRepository = new CommandeRepository();

            if ($quantite === 0) {
                // Supprimer le produit du panier
                if ($detailsCommandeRepository->supprimerProduit($idCommande, $idProduit)) {
                    // Vérifier si c'était le dernier produit dans la commande
                    if ($detailsCommandeRepository->compterProduitsDansCommande($idCommande) == 0) {
                        // Supprimer la commande entière si c'était le dernier produit
                        $commandeRepository->supprimer($idCommande);
                        MessageFlash::ajouter('success', "Le produit et la commande ont été supprimés.");
                        ControleurGenerique::redirectionVersURL('controleurFrontal.php?controleur=Produit&action=afficherListe');
                        return;
                    } else {
                        MessageFlash::ajouter('success', "Produit supprimé du panier.");
                    }
                } else {
                    MessageFlash::ajouter('danger', "Erreur lors de la suppression du produit.");
                }
            } else {
                // Mettre à jour la quantité si elle est supérieure à 0
                $resultat = $detailsCommandeRepository->mettreAJourQuantite($idCommande, $idProduit, $quantite);
                if ($resultat) {
                    MessageFlash::ajouter('success', "Quantité modifiée avec succès.");
                } else {
                    MessageFlash::ajouter('danger', "Erreur lors de la modification de la quantité.");
                }
            }
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherPanier&controleur=commande');
        } else {
            MessageFlash::ajouter('danger', "Données incomplètes pour la modification de la quantité.");
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherPanier&controleur=commande');
        }
    }


    public static function modifierQuantiteSession(): void {
        if (isset($_REQUEST['idProduit'], $_REQUEST['quantite'])) {
            $idProduit = $_REQUEST['idProduit'];
            $quantite = $_REQUEST['quantite'];

            $sessionPanier = new PanierSession();
            if ($quantite > 0) {
                $sessionPanier->modifierQuantite($idProduit, $quantite);
                MessageFlash::ajouter('success', 'Quantité mise à jour.');
            } else {
                MessageFlash::ajouter('danger', 'Quantité invalide.');
            }
        } else {
            MessageFlash::ajouter('danger', 'Informations manquantes pour mettre à jour la quantité.');
        }

        ControleurGenerique::redirectionVersURL('controleurFrontal.php?controleur=Commande&action=afficherPanier');
    }


    public static function supprimer() {
        if (isset($_REQUEST['idCommande'])) {
            $idCommande = $_REQUEST['idCommande'];
            $repository = new CommandeRepository();

            if ($repository->supprimer($idCommande)) {
                MessageFlash::ajouter('success', "La commande a bien été supprimée !");
                ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherListe');
            } else {
                MessageFlash::ajouter('danger', "Erreur lors de la suppression de la commande.");
                ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherListe');
            }
        } else {
            MessageFlash::ajouter('danger', "ID commande manquant.");
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherListe');
        }
    }


    public static function supprimerProduit(): void {
        $idUtilisateur = ConnexionUtilisateur::getIdUtilisateurConnecte();
        if (!$idUtilisateur) {
            MessageFlash::ajouter('danger', "Vous devez être connecté pour supprimer un produit du panier.");
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherFormulaireConnexion');
            return;
        }

        $idCommande = $_REQUEST['idCommande'] ?? null;
        $idProduit = $_REQUEST['idProduit'] ?? null;

        if ($idCommande === null || $idProduit === null) {
            MessageFlash::ajouter('danger', "Informations manquantes pour supprimer le produit du panier.");
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?controleur=Commande&action=afficherPanier');
            return;
        }

        $detailsCommandeRepository = new DetailsCommandeRepository();
        if ($detailsCommandeRepository->supprimerProduit($idCommande, $idProduit)) {
            // Vérifiez s'il reste d'autres produits dans la commande
            $produitsRestants = $detailsCommandeRepository->compterProduitsDansCommande($idCommande);
            if ($produitsRestants == 0) {
                // Supprimez la commande si c'était le dernier produit
                $commandeRepository = new CommandeRepository();
                $commandeRepository->supprimer($idCommande);
                MessageFlash::ajouter('success', "Produit et commande supprimés avec succès.");
                ControleurGenerique::redirectionVersURL('controleurFrontal.php?controleur=Produit&action=afficherListe');
                return;
            }
            MessageFlash::ajouter('success', "Produit supprimé du panier avec succès.");
        } else {
            MessageFlash::ajouter('danger', "Erreur lors de la suppression du produit du panier.");
        }
        ControleurGenerique::redirectionVersURL('controleurFrontal.php?controleur=Commande&action=afficherPanier');
    }


    public static function supprimerProduitSession(): void {
        if (isset($_REQUEST['idProduit'])) {
            $idProduit = $_REQUEST['idProduit'];

            $sessionPanier = new PanierSession();
            $sessionPanier->supprimerProduit($idProduit);

            MessageFlash::ajouter('success', 'Produit supprimé du panier.');
        } else {
            MessageFlash::ajouter('danger', 'Informations manquantes pour supprimer le produit.');
        }

        ControleurGenerique::redirectionVersURL('controleurFrontal.php?controleur=Commande&action=afficherPanier');
    }


    public static function payer(): void {
        if (!ConnexionUtilisateur::estConnecte()) {
            MessageFlash::ajouter('danger', "Vous devez être connecté pour effectuer cette action.");
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherFormulaireConnexion');
            return;
        }

        $idUtilisateur = ConnexionUtilisateur::getIdUtilisateurConnecte();
        $commandeRepository = new CommandeRepository();
        $panier = $commandeRepository->recupererPanierParIdUtilisateur($idUtilisateur);

        if (!$panier) {
            MessageFlash::ajouter('danger', "Votre panier est vide.");
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherListe');
            return;
        }

        $detailsCommandeRepository = new DetailsCommandeRepository();
        $detailsPanier = $detailsCommandeRepository->recupererDetails($panier->getIdCommande());

        $session = Session::getInstance();
        $session->enregistrer('idCommande', $panier->getIdCommande());

        ControleurGenerique::afficherVue('commande/vuePaiement.php', [
            'panier' => $panier,
            'detailsPanier' => $detailsPanier,
            'pagetitle' => 'Paiement',
            'cheminVueBody' => 'commande/vuePaiement.php'
        ]);
    }


    public static function traiterPaiementFictif(): void {
        if (!ConnexionUtilisateur::estConnecte()) {
            MessageFlash::ajouter('danger', "Vous devez être connecté pour valider un paiement.");
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherFormulaireConnexion');
            return;
        }

        $session = Session::getInstance();
        $idCommande = $session->lire('idCommande');

        if (!$idCommande) {
            MessageFlash::ajouter('danger', "Aucune commande à finaliser.");
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?controleur=Commande&action=afficherPanier');
            return;
        }

        $commandeRepository = new CommandeRepository();
        $panier = $commandeRepository->recupererParClePrimaire($idCommande);

        if ($panier === null) {
            MessageFlash::ajouter('danger', "La commande n'a pas été trouvée.");
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?controleur=Commande&action=afficherPanier');
            return;
        }

        $montant = $panier->calculerTotal();
        $paiementRepository = new PaiementRepository();
        $paiementEffectue = $paiementRepository->enregistrerPaiement($idCommande, $montant);

        if ($paiementEffectue) {
            $panier->setStatut('payée');
            $commandeRepository->mettreAJour($panier);
            MessageFlash::ajouter('success', "Paiement accepté !");
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?controleur=Commande&action=afficherRecus&idCommande=' . $idCommande);
        } else {
            MessageFlash::ajouter('danger', "Le paiement n'a pas pu être enregistré.");
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?controleur=Commande&action=afficherPanier');
        }
    }


    public static function afficherRecus() {
        if (!ConnexionUtilisateur::estConnecte()) {
            MessageFlash::ajouter('danger', "Vous devez être connecté pour accéder à cette page.");
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherFormulaireConnexion');
            return;
        }

        $idCommande = $_GET['idCommande'] ?? null;

        if (!$idCommande) {
            MessageFlash::ajouter('danger', "Aucune commande spécifiée.");
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?controleur=Commande&action=afficherPanier');
            return;
        }

        $commandeRepository = new CommandeRepository();
        $commande = $commandeRepository->recupererParClePrimaire($idCommande);

        if ($commande === null) {
            MessageFlash::ajouter('danger', "La commande n'a pas été trouvée.");
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?controleur=Commande&action=afficherPanier');
            return;
        }

        ControleurGenerique::afficherVue('commande/recusPaiement.php', [
            'commande' => $commande,
            'pagetitle' => 'Reçus de paiement',
            'cheminVueBody' => 'commande/recusPaiement.php'
        ]);
    }


    public static function afficherDetail() : void {
        if (isset($_REQUEST['idCommande'])) {
            $idCommande = $_REQUEST['idCommande'];
            $commandeRepository = new CommandeRepository();
            $commande = $commandeRepository->recupererParClePrimaire($idCommande);

            if (!$commande) {
                MessageFlash::ajouter('danger', "Aucune commande trouvée avec cet ID.");
                ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherListe');
                return;
            }

            $detailsCommandeRepository = new DetailsCommandeRepository();
            $details = $detailsCommandeRepository->recupererDetails($idCommande);

            ControleurGenerique::afficherVue('commande/detail.php', [
                'commande' => $commande,
                'details' => $details,
                'pagetitle' => 'Détails de la commande',
                'cheminVueBody' => 'commande/detail.php'
            ]);
        } else {
            MessageFlash::ajouter('danger', "ID commande non fourni.");
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherListe');
        }
    }

}
?>
