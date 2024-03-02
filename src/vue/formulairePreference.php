<?php
use App\MonBeauVelo\Configuration\ConfigurationSite;
use App\MonBeauVelo\Lib\PreferenceControleur;

// Récupérer la préférence actuelle de l'utilisateur
$preferenceActuelle = "";
if (PreferenceControleur::existe()) {
    $preferenceActuelle = PreferenceControleur::lire();
}
?>

<h1>Administrateur : Choisissez votre contrôleur de préférence.</h1>

<form method="post" action="controleurFrontal.php">
    <fieldset>
        <h2>Choisir un contrôleur par défaut</h2>

        <input type="radio" id="produitId" name="controleur_defaut" value="produit" <?php echo ($preferenceActuelle == "produit") ? "checked" : ""; ?>>
        <label for="produitId">Produit</label>

        <input type="radio" id="utilisateurId" name="controleur_defaut" value="utilisateur" <?php echo ($preferenceActuelle == "utilisateur") ? "checked" : ""; ?>>
        <label for="utilisateurId">Utilisateur</label>

        <input type="radio" id="commandeId" name="controleur_defaut" value="commande" <?php echo ($preferenceActuelle == "commande") ? "checked" : ""; ?>>
        <label for="commandeId">Commande</label>

        <p>
            <input type="hidden" name="action" value="enregistrerPreference">
            <input type="submit" value="Enregistrer">
        </p>
    </fieldset>
</form>
