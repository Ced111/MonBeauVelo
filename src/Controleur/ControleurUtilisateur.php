<?php
namespace App\MonBeauVelo\Controleur;

use App\MonBeauVelo\Lib\ConnexionUtilisateur;
use App\MonBeauVelo\Lib\MessageFlash;
use App\MonBeauVelo\Lib\PanierSession;
use App\MonBeauVelo\Lib\VerificationEmail;
use App\MonBeauVelo\Modele\Repository\CommandeRepository;
use App\MonBeauVelo\Modele\Repository\UtilisateurRepository;
use App\MonBeauVelo\Modele\DataObject\Utilisateur;
use App\MonBeauVelo\Lib\MotDePasse;

class ControleurUtilisateur extends ControleurGenerique {

    public static function afficherListe() : void {
        if (!ConnexionUtilisateur::estAdministrateur()) {
            MessageFlash::ajouter('danger', "Vous n'avez pas l'autorisation d'accéder à la liste des utilisateurs.");
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?controleur=produit&action=afficherListe');
            return;
        }
        $utilisateurRepository = new UtilisateurRepository();
        $utilisateurs = $utilisateurRepository->recuperer();
        ControleurGenerique::afficherVue('utilisateur/liste.php', [
            'utilisateurs' => $utilisateurs,
            'pagetitle' => 'Liste des utilisateurs',
            'cheminVueBody' => 'utilisateur/liste.php'
        ]);
    }

    public static function afficherFormulaireCreation() : void {
        ControleurGenerique::afficherVue('utilisateur/formulaireCreation.php', [
            'pagetitle' => 'Ajouter un nouvelle utilisateur',
            'cheminVueBody' => 'utilisateur/formulaireCreation.php'
        ]);
    }

    public static function afficherFormulaireMiseAJour() {
        $utilisateurRepository = new UtilisateurRepository();

        if (!isset($_REQUEST['idUtilisateur'])) {
            MessageFlash::ajouter('danger', "ID utilisateur manquant.");
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherListe');
            return;
        }

        $idUtilisateur = intval($_REQUEST['idUtilisateur']);
        $utilisateur = $utilisateurRepository->recupererParClePrimaire($idUtilisateur);

        if (!$utilisateur) {
            MessageFlash::ajouter('danger', "ID utilisateur inconnu.");
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherListe');
            return;
        }

        if (!ConnexionUtilisateur::estUtilisateur($idUtilisateur) && !ConnexionUtilisateur::estAdministrateur()) {
            MessageFlash::ajouter('danger', "La mise à jour n’est possible que pour l’utilisateur connecté ou pour l'administrateur.");
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherListe');
            return;
        }

        ControleurGenerique::afficherVue('utilisateur/formulaireMiseAJour.php', [
            'utilisateur' => $utilisateur,
            'pagetitle' => 'Mettre à jour un utilisateur'
        ]);
    }



    public static function afficherFormulaireConnexion() {
        $utilisateurRepository = new UtilisateurRepository();

        // Vérifie si un utilisateur est déjà connecté
        if (\App\MonBeauVelo\Lib\ConnexionUtilisateur::estConnecte()) {
            MessageFlash::ajouter('danger', "Un utilisateur est déjà connecté. Veuillez d'abord vous déconnecter.");
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherListe');
            return;
        }

        // Si un idUtilisateur est spécifié, récupère les informations de cet utilisateur pour pré-remplir le formulaire
        if (isset($_REQUEST['idUtilisateur'])) {
            $idUtilisateur = htmlspecialchars($_REQUEST['idUtilisateur']);
            $utilisateur = $utilisateurRepository->recupererParClePrimaire($idUtilisateur);

            if ($utilisateur) {
                ControleurGenerique::afficherVue('utilisateur/formulaireConnexion.php', [
                    'utilisateur' => $utilisateur,
                    'pagetitle' => 'Connexion à votre compte'
                ]);
            } else {
                MessageFlash::ajouter('danger', "Utilisateur demandé n'existe pas.");
                ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherListe');
            }
        } else {
            // Affiche simplement le formulaire de connexion sans pré-remplir les champs
            ControleurGenerique::afficherVue('utilisateur/formulaireConnexion.php', [
                'pagetitle' => 'Connexion à votre compte'
            ]);
        }
    }


    public static function connecter() {
        $utilisateurRepository = new UtilisateurRepository();
        $commandeRepository = new CommandeRepository();

        // Vérification si email et mot de passe sont présents dans le query string
        if (!isset($_REQUEST['email']) || !isset($_REQUEST['motDePasse'])) {
            MessageFlash::ajouter('danger', 'Email et/ou mot de passe manquant.');
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherFormulaireConnexion&controleur=utilisateur');
            return;
        }

        $email = htmlspecialchars($_REQUEST['email']);
        $password = $_REQUEST['motDePasse'];

        // Récupération de l'utilisateur avec l'email fourni
        $utilisateur = $utilisateurRepository->recupererParEmail($email);

        // Vérification si l'utilisateur existe
        if (!$utilisateur) {
            MessageFlash::ajouter('warning', 'Email inconnu.');
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherFormulaireConnexion&controleur=utilisateur');
            return;
        }

        // Vérification si le mot de passe est correct
        if (!MotDePasse::verifier($password, $utilisateur->getMdpHache())) {
            MessageFlash::ajouter('warning', 'Mot de passe incorrect.');
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherFormulaireConnexion&controleur=utilisateur');
            return;
        }

        // Vérification si l'utilisateur a validé un email
        if (!VerificationEmail::aValideEmail($utilisateur)) {
            MessageFlash::ajouter('danger', 'Vous devez valider votre email avant de pouvoir vous connecter.');
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherFormulaireConnexion&controleur=utilisateur');
            return;
        }

        // Connexion de l'utilisateur
        ConnexionUtilisateur::connecter($utilisateur->getIdUtilisateur());

        // Vérification du panier en session
        $sessionPanier = new PanierSession();
        if ($sessionPanier->aDesArticles()) {
            $commandeRepository = new CommandeRepository();
            $commandeExistante = $commandeRepository->trouverOuCreerPanier($utilisateur->getIdUtilisateur());

            // Vérifier si la commande existante n'a pas d'articles
            if ($commandeRepository->compterArticlesDansCommande($commandeExistante->getIdCommande()) == 0) {
                // Transférer les articles du panier de session
                $commandeRepository->sauvegarderPanierSessionEnBase($utilisateur->getIdUtilisateur(), $sessionPanier->getDetailsPanier());
                $sessionPanier->viderPanier();
            }
        }

        if (ConnexionUtilisateur::estAdministrateur()) {
            MessageFlash::ajouter('success', 'Administrateur connecté.');
        } else {
            MessageFlash::ajouter('success', 'Utilisateur connecté.');
        }
        ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherListe');
    }


    public static function deconnecter() {
        if (!ConnexionUtilisateur::estConnecte()) {
            MessageFlash::ajouter('danger', "Aucun utilisateur n'est connecté.");
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherListe');
            return;
        }

        $estAdmin = ConnexionUtilisateur::estAdministrateur();
        ConnexionUtilisateur::deconnecter();
        if ($estAdmin) {
            MessageFlash::ajouter('success', "Administrateur déconnecté.");
        } else {
            MessageFlash::ajouter('success', "Utilisateur déconnecté.");
        }
        ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherListe');
    }


    public static function mettreAJour() : void {
        if (isset($_REQUEST['idUtilisateur'], $_REQUEST['nom'], $_REQUEST['prenom'])) {
            $utilisateurConnecteEstAdmin = ConnexionUtilisateur::estAdministrateur();

            // Si l'utilisateur connecté n'est pas administrateur et essaye de mettre à jour un autre utilisateur
            if (!$utilisateurConnecteEstAdmin && !ConnexionUtilisateur::estUtilisateur($_REQUEST['idUtilisateur'])) {
                MessageFlash::ajouter('danger', "La prise en compte de la mise à jour n’est possible que pour l’utilisateur connecté.");
                ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherListe');
                return;
            }

            $utilisateurRepository = new UtilisateurRepository();

            // Récupère l'objet utilisateur original par son ID
            $utilisateurOriginal = $utilisateurRepository->recupererParClePrimaire($_REQUEST['idUtilisateur']);

            if (!$utilisateurOriginal) {
                MessageFlash::ajouter('danger', "Utilisateur inexistant.");
                ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherListe');
                return;
            }

            // Vérification du format de l'email
            if (!filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL)) {
                MessageFlash::ajouter('danger', "Adresse email invalide.");
                ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherListe');
                return;
            }

            // Si l'email est différent de l'email actuel, écrire dans emailAValider, créer un nonce, et envoyer un mail de validation
            if ($_REQUEST['email'] !== $utilisateurOriginal->getEmail()) {
                $nonce = bin2hex(random_bytes(32));
                $utilisateurOriginal->setEmailAValider($_REQUEST['email']);
                $utilisateurOriginal->setNonce($nonce);

                VerificationEmail::envoiEmailValidation($utilisateurOriginal);
            }

            $utilisateurMiseAJour = $utilisateurRepository->miseAJourDepuisFormulaire($utilisateurOriginal, $_REQUEST);

            if (!$utilisateurConnecteEstAdmin) {
                if (!MotDePasse::verifier($_REQUEST['mdp_old'], $utilisateurOriginal->getMdpHache())) {
                    MessageFlash::ajouter('warning', "Ancien mot de passe erroné.");
                    ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherFormulaireMiseAJour&idUtilisateur=' . $_REQUEST['idUtilisateur']);
                    return;
                }

                if ($_REQUEST['mdp'] !== $_REQUEST['mdp2']) {
                    MessageFlash::ajouter('warning', "Les nouveaux mots de passe ne correspondent pas.");
                    ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherFormulaireMiseAJour&idUtilisateur=' . $_REQUEST['idUtilisateur']);
                    return;
                }

                // Hachez le nouveau mot de passe avant la mise à jour en utilisant le poivre
                $hashedPassword = MotDePasse::hacher($_REQUEST['mdp']);
                $utilisateurOriginal->setMdpHache($hashedPassword);
            }

            // Mettez à jour le statut d'administrateur uniquement si l'utilisateur connecté est administrateur
            if ($utilisateurConnecteEstAdmin && isset($_REQUEST['estAdmin'])) {
                $estAdmin = $_REQUEST['estAdmin'] === 'on';
                $utilisateurOriginal->setEstAdmin($estAdmin);
            }

            if ($utilisateurRepository->mettreAJour($utilisateurMiseAJour)) {
                MessageFlash::ajouter('success', "Utilisateur mis à jour avec succès.");
                ControleurGenerique::redirectionVersURL('controleurFrontal.php?controleur=utilisateur&action=afficherDetail&idUtilisateur=' . $_REQUEST['idUtilisateur']);
            } else {
                MessageFlash::ajouter('danger', "Erreur lors de la mise à jour de l'utilisateur.");
                ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherListe');
            }
        }
    }


    public static function creerDepuisFormulaire() : void {
        if (isset($_REQUEST['nom'], $_REQUEST['prenom'], $_REQUEST['mdp'], $_REQUEST['mdp2'], $_REQUEST['email'], $_REQUEST['adresse'], $_REQUEST['telephone'])) {

            // Vérifier si les deux mots de passe coïncident
            if ($_REQUEST['mdp'] !== $_REQUEST['mdp2']) {
                MessageFlash::ajouter('warning', 'Mots de passe distincts.');
                ControleurGenerique::redirectionVersURL('controleurFrontal.php?controleur=Utilisateur&action=afficherFormulaireCreation');
                return;
            }

            // Vérifier si l'adresse email est valide
            if (!filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL)) {
                MessageFlash::ajouter('danger', 'Adresse email invalide.');
                ControleurGenerique::redirectionVersURL('controleurFrontal.php?controleur=Utilisateur&action=afficherFormulaireCreation');
                return;
            }

            // Vérifier si l'email n'est pas déjà utilisé
            $utilisateurRepository = new UtilisateurRepository();
            if ($utilisateurRepository->emailEstDejaUtilise($_REQUEST['email'])) {
                MessageFlash::ajouter('danger', 'Adresse email déjà utilisée.');
                ControleurGenerique::redirectionVersURL('controleurFrontal.php?controleur=Utilisateur&action=afficherFormulaireCreation');
                return;
            }

            // Création de l'utilisateur à partir du formulaire
            $nouvelUtilisateur = UtilisateurRepository::construireDepuisFormulaire($_REQUEST);

            // Si l'utilisateur connecté n'est pas administrateur, forcez le nouvel utilisateur à ne pas être administrateur
            if (!ConnexionUtilisateur::estAdministrateur()) {
                $nouvelUtilisateur->setEstAdmin(false);
            }

            $utilisateurRepository = new UtilisateurRepository();
            if ($utilisateurRepository->sauvegarder($nouvelUtilisateur)) {
                VerificationEmail::envoiEmailValidation($nouvelUtilisateur);
                MessageFlash::ajouter('success', 'Utilisateur créé avec succès. Un email de confirmation a été envoyé sur votre boîte mail.');
                ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherListe');
            } else {
                MessageFlash::ajouter('danger', 'Erreur lors de la sauvegarde de l\'utilisateur.');
                ControleurGenerique::redirectionVersURL('controleurFrontal.php?controleur=Utilisateur&action=afficherFormulaireCreation');
            }
        } else {
            MessageFlash::ajouter('danger', 'Données incomplètes.');
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?controleur=Utilisateur&action=afficherFormulaireCreation');
        }
    }


    public static function validerEmail() : void {
        if (isset($_REQUEST['idUtilisateur'], $_REQUEST['nonce'])) {
            $idUtilisateur = $_REQUEST['idUtilisateur'];
            $nonce = $_REQUEST['nonce'];

            if (VerificationEmail::traiterEmailValidation($idUtilisateur, $nonce)) {
                MessageFlash::ajouter('success', "Votre adresse email a bien été validée.");
                ControleurUtilisateur::afficherDetail($idUtilisateur);
            } else {
                MessageFlash::ajouter('danger', "Échec de la validation de l'email.");
                ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherListe');
            }
        } else {
            MessageFlash::ajouter('danger', "Données incomplètes pour la validation de l'email.");
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherListe');
        }
    }


    public static function supprimer() {
        if (isset($_REQUEST['idUtilisateur'])) {
            $idUtilisateur = htmlspecialchars($_REQUEST['idUtilisateur']);

            // Vérifiez si l'utilisateur connecté est celui que l'on souhaite supprimer
            if (!ConnexionUtilisateur::estUtilisateur($idUtilisateur)) {
                MessageFlash::ajouter('danger', "La suppression n’est possible que pour l’utilisateur connecté.");
                ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherListe');
                return;
            }

            $repository = new UtilisateurRepository();

            if ($repository->supprimer($idUtilisateur)) {
                MessageFlash::ajouter('success', "L'utilisateur a bien été supprimé !");
                ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherListe');
            } else {
                MessageFlash::ajouter('danger', "Erreur lors de la suppression de l'utilisateur.");
                ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherListe');
            }
        } else {
            MessageFlash::ajouter('danger', "ID Utilisateur manquant.");
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherListe');
        }
    }


    public static function afficherDetail() : void {
        // Vérification si l'idUtilisateur est présent dans l'URL
        if (isset($_REQUEST['idUtilisateur'])) {
            $idUtilisateur = filter_var($_REQUEST['idUtilisateur'], FILTER_VALIDATE_INT);

            if ($idUtilisateur === false) {
                MessageFlash::ajouter('danger', 'ID Utilisateur invalide.');
                ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherListe');
                return;
            }

            // Appel de la méthode du modèle pour récupérer les utilisateurs par idUtilisateur
            $utilisateurRepository = new UtilisateurRepository();
            $utilisateur = $utilisateurRepository->recupererParClePrimaire($idUtilisateur);

            // Si aucun utilisateur trouvé, redirigez vers la liste avec un message flash
            if (!$utilisateur) {
                MessageFlash::ajouter('warning', 'ID Utilisateur inconnu');
                ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherListe');
            }

            // Si un utilisateur est trouvé, appelez la vue de détail avec le paramètre $utilisateur
            ControleurGenerique::afficherVue('utilisateur/detail.php', [
                'idUtilisateur' => $idUtilisateur,
                'utilisateur' => $utilisateur,
                'pagetitle' => 'Détails utilisateur',
                'cheminVueBody' => 'utilisateur/detail.php'
            ]);
        } else {
            MessageFlash::ajouter('danger', 'ID Utilisateur non fourni.');
            ControleurGenerique::redirectionVersURL('controleurFrontal.php?action=afficherListe');
        }
    }


}
?>
