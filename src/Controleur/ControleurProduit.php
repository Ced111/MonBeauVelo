<?php
namespace App\MonBeauVelo\Controleur;

use App\MonBeauVelo\Modele\Repository\ProduitRepository;
use App\MonBeauVelo\Modele\DataObject\Produit;
use App\MonBeauVelo\Modele\HTTP\Cookie;
use App\MonBeauVelo\Lib\MessageFlash;

class ControleurProduit extends ControleurGenerique {

    public static function afficherListe() : void {
        $produitRepository = new ProduitRepository();
        $produits = $produitRepository->recuperer();
        ControleurGenerique::afficherVue('produit/liste.php', [
            'produits' => $produits,
            'pagetitle' => 'Liste des produits',
            'cheminVueBody' => 'produit/liste.php'
        ]);
    }


    public static function afficherFormulaireCreation() : void {
        ControleurGenerique::afficherVue('produit/formulaireCreation.php', [
            'pagetitle' => 'Ajouter un nouveau produit',
            'cheminVueBody' => 'produit/formulaireCreation.php'
        ]);
    }


    public static function afficherFormulaireMiseAJour() {
        $produitRepository = new ProduitRepository();

        if (isset($_REQUEST['idProduit'])) {
            $idProduit = htmlspecialchars($_REQUEST['idProduit']);
            $produit = $produitRepository->recupererParClePrimaire($idProduit);

            if ($produit) {
                ControleurGenerique::afficherVue('produit/formulaireMiseAJour.php', [
                    'produit' => $produit,
                    'pagetitle' => 'Mettre à jour une produit'
                ]);
            } else {
                MessageFlash::ajouter('danger', "La produit demandée n'existe pas.");
                ControleurGenerique::redirectionVersURL('action=afficherListe');
            }
        } else {
            MessageFlash::ajouter('danger', "ID manquante.");
            ControleurGenerique::redirectionVersURL('action=afficherListe');
        }
    }


    public static function mettreAJour(): void {
        // Vérification de la présence de toutes les clés nécessaires dans $_REQUEST
        if (isset($_REQUEST['idProduit'], $_REQUEST['idCategorie'], $_REQUEST['nom'], $_REQUEST['marque'], $_REQUEST['description'], $_REQUEST['prix'], $_REQUEST['stock'], $_REQUEST['image'])) {
            // Validation des données
            if (!is_numeric($_REQUEST['prix']) || $_REQUEST['prix'] <= 0) {
                MessageFlash::ajouter('danger', "Le prix doit être un nombre positif.");
                ControleurGenerique::redirectionVersURL('action=afficherListe');
                return;
            }

            if (!is_numeric($_REQUEST['stock']) || $_REQUEST['stock'] < 0) {
                MessageFlash::ajouter('danger', "Le stock doit être un nombre entier positif ou zéro.");
                ControleurGenerique::redirectionVersURL('action=afficherListe');
                return;
            }

            $produitRepository = new ProduitRepository();
            $produitOriginal = $produitRepository->recupererParClePrimaire($_REQUEST['idProduit']);
            if (!$produitOriginal) {
                MessageFlash::ajouter('danger', "Produit inexistant.");
                ControleurGenerique::redirectionVersURL('action=afficherListe');
                return;
            }

            $produitMiseAJour = $produitRepository->miseAJourDepuisFormulaire($produitOriginal, $_REQUEST);

            if ($produitRepository->mettreAJour($produitMiseAJour)) {
                MessageFlash::ajouter('success', "Produit mis à jour avec succès.");
                ControleurGenerique::redirectionVersURL('controleur=produit&action=afficherDetail&idProduit=' . $produitOriginal->getIdProduit());
            } else {
                MessageFlash::ajouter('danger', "Erreur lors de la mise à jour du produit.");
                ControleurGenerique::redirectionVersURL('controleur=produit&action=afficherFormulaireMiseAJour&idProduit=' . $produitOriginal->getIdProduit());
            }
        } else {
            MessageFlash::ajouter('danger', "Données incomplètes pour la mise à jour.");
            ControleurGenerique::redirectionVersURL('controleur=produit&action=afficherListe');
        }
    }


    public static function creerDepuisFormulaire(): void {
        if (isset($_REQUEST['idCategorie'], $_REQUEST['nom'], $_REQUEST['marque'], $_REQUEST['description'], $_REQUEST['prix'], $_REQUEST['stock'], $_REQUEST['image'])) {
            // Validation des données
            if (!is_numeric($_REQUEST['prix']) || $_REQUEST['prix'] <= 0) {
                MessageFlash::ajouter('danger', "Le prix doit être un nombre positif.");
                ControleurGenerique::redirectionVersURL('action=afficherListe');
                return;
            }

            if (!is_numeric($_REQUEST['stock']) || $_REQUEST['stock'] < 0) {
                MessageFlash::ajouter('danger', "Le stock doit être un nombre entier positif ou zéro.");
                ControleurGenerique::redirectionVersURL('action=afficherListe');
                return;
            }

            $nouveauProduit = ProduitRepository::construireDepuisFormulaire($_REQUEST);

            $produitRepository = new ProduitRepository();
            if ($produitRepository->sauvegarder($nouveauProduit)) {
                MessageFlash::ajouter('success', "Le produit a été créé avec succès.");
                ControleurGenerique::redirectionVersURL('action=afficherDetail&idProduit=' . $nouveauProduit->getIdProduit());
            } else {
                MessageFlash::ajouter('danger', "Erreur lors de la sauvegarde du produit.");
                ControleurGenerique::redirectionVersURL('action=afficherFormulaireCreation&idProduit=' . $nouveauProduit->getIdProduit());
            }
        } else {
            MessageFlash::ajouter('danger', "Données incomplètes.");
            ControleurGenerique::redirectionVersURL('action=afficherListe');
        }
    }


    public static function supprimer() {
        if (isset($_REQUEST['idProduit'])) {
            $idProduit = $_REQUEST['idProduit'];
            $repository = new ProduitRepository();

            if ($repository->supprimer($idProduit)) {
                MessageFlash::ajouter('success', "Le produit a bien été supprimé !");
                ControleurGenerique::redirectionVersURL('action=afficherListe');
            } else {
                MessageFlash::ajouter('danger', "Erreur lors de la suppression du produit.");
                ControleurGenerique::redirectionVersURL('action=afficherListe');
            }
        } else {
            MessageFlash::ajouter('danger', "ID produit manquant.");
            ControleurGenerique::redirectionVersURL('action=afficherListe');
        }
    }


    public static function afficherDetail() : void {
        // Vérifie si "idProduit" est présent dans l'URL
        if (isset($_REQUEST['idProduit'])) {
            $idProduit = $_REQUEST['idProduit'];
            $produitRepository = new ProduitRepository();
            $produit = $produitRepository->recupererParClePrimaire($idProduit);

            // Si aucun produit trouvé, affichez un message flash et redirigez
            if (!$produit) {
                MessageFlash::ajouter('danger', "Aucun produit trouvé avec cet ID.");
                ControleurGenerique::redirectionVersURL('action=afficherListe');
            }

            // Si un produit est trouvé, appelez la vue de détail avec le paramètre $produit
            ControleurGenerique::afficherVue('produit/detail.php', [
                'idProduit' => $idProduit,
                'produit' => $produit,
                'pagetitle' => 'Détails du produit',
                'cheminVueBody' => 'produit/detail.php'
            ]);
        } else {
            MessageFlash::ajouter('danger', "ID produit non fourni.");
            ControleurGenerique::redirectionVersURL('action=afficherListe');
        }
    }

}
?>
