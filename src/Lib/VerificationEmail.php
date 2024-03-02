<?php
namespace App\MonBeauVelo\Lib;

use App\MonBeauVelo\Configuration\ConfigurationSite;
use App\MonBeauVelo\Modele\DataObject\Utilisateur;
use App\MonBeauVelo\Modele\Repository\UtilisateurRepository;

class VerificationEmail {
    public static function envoiEmailValidation(Utilisateur $utilisateur): void {
        $idUtilisateurURL = rawurlencode($utilisateur->getIdUtilisateur());
        $nonceURL = rawurlencode($utilisateur->getNonce());
        $URLAbsolue = ConfigurationSite::getURLAbsolue();
        $lienValidationEmail = "$URLAbsolue?action=validerEmail&controleur=utilisateur&idUtilisateur=$idUtilisateurURL&nonce=$nonceURL";

        $corpsEmail = "Bonjour " . htmlspecialchars($utilisateur->getPrenom()) . ",\n\n";
        $corpsEmail .= "Merci de vous être inscrit sur MonBeauVelo. Pour activer votre compte, veuillez cliquer sur le lien ci-dessous :\n";
        $corpsEmail .= "$lienValidationEmail\n\n";
        $corpsEmail .= "Si vous n'avez pas créé de compte sur MonBeauVelo, veuillez ignorer cet email.\n\n";
        $corpsEmail .= "Cordialement,\n";
        $corpsEmail .= "L'équipe MonBeauVelo";

        $expediteur = "contact@monbeauvelo.com"; // Adresse email de l'expéditeur
        $destinataire = $utilisateur->getEmailAValider(); // Adresse email du destinataire
        $sujet = "Activation de votre compte MonBeauVelo"; // Sujet du courriel

        $headers = "From: MonBeauVelo <$expediteur>\r\n";
        $headers .= "Reply-To: $expediteur\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        if (mail($destinataire, $sujet, $corpsEmail, $headers)) {
            echo "Courriel de validation envoyé avec succès à $destinataire";
        } else {
            echo "Échec de l'envoi du courriel de validation à $destinataire";
        }
    }

    public static function traiterEmailValidation($idUtilisateur, $nonce): bool {
        $utilisateurRepository = new UtilisateurRepository();
        $utilisateur = $utilisateurRepository->recupererParClePrimaire($idUtilisateur);

        if ($utilisateur && $utilisateur->getNonce() === $nonce) {
            $utilisateur->setEmail($utilisateur->getEmailAValider());
            $utilisateur->setEmailAValider("");
            $utilisateur->setNonce("");

            return $utilisateurRepository->mettreAJour($utilisateur);
        }

        return false;
    }

    public static function aValideEmail(Utilisateur $utilisateur) : bool {
        return !empty($utilisateur->getEmail());
    }

}
?>